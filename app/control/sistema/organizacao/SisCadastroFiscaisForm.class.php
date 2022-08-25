<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBMultiSearch;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * FormBuilderGridView
 * COLOCAR A FUNCAO DE VERIFICAR DUPLICIDADE
 *
 * @version    1.0 
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SisCadastroFiscaisForm extends TWindow
{
    private $form;
    private $inUpdate = false;
    private $typeRegister;
    private $html;
    private $html2;


    /**
     * Class constructor
     * Creates the page
     */
    public function __construct($param)
    {
        parent::__construct();
        parent::setSize(0.8, null);

        $this->html = new THtmlRenderer('app/resources/jquery_masks.html');

        $replace = array();
        $this->html->enableSection('main', $replace);

        $this->form = new BootstrapFormBuilder('form_interaction');
        if (!isset($_GET["id"])) {
            $this->inUpdate = true;
            $this->form->setFormTitle('NOVO ATENDIMENTO');
        } else {

            $this->inUpdate = false;
            $this->form->setFormTitle('GERENCIAR ATENDIMENTO');
        }
        $this->form->setFieldSizes('100%');
        $this->form->generateAria(); // automatic aria-label

        $this->form->style = 'display: table;width:100%'; // change style
        parent::setProperty('class', 'window_modal');

        $this->form->appendPage('Dados do Atendimento');



        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $telefone = new TEntry('telefone');
        $posicao = new TCombo('posicao');


        $zona = new TDBCombo('zona', 'sistema', 'SisLocaisVotacaoFull', 'zona', 'zona', 'zona ASC');

        $secao = new TCombo('secao');
        $local_votacao = new TCombo('local_votacao');
        $endereco_local = new TEntry('endereco_local');

        $criteria_type = new TCriteria();
        // $criteria_type->add(new TFilter('meta_key', '=', "group_category"));
        $tipo_solicitacao = new TDBCombo('segmento', 'sistema', 'SisTipoAtendimento', 'id', 'nome', 'nome ASC', $criteria_type);


        //Hidden Fields
        $nome = new TEntry('nome');

        $telefone = new TEntry('fone1');
        $telefoneFixo = new TEntry('fone2');
        $descricao = new TEntry('descricao');

        $cidade = new TDBCombo('cidade', 'sistema', 'SisLocaisVotacaoFull', 'municipio', 'municipio', 'municipio ASC');
        $bairro = new TEntry('bairro');
        $endereco = new TEntry('endereco');

        $observacao = new TText('observacao');
        $indicacao     = new TEntry('indicacao');

        /**
         * acoes automaticas
         */

        // set exit action for input_exit
        $change_action = new TAction(array($this, 'onChangeAction'));
        $cidade->setChangeAction($change_action);

        $change_secoes_action = new TAction(array($this, 'onChangeActionSecoes'));
        $change_action->setParameter('zona', '{zona}');
        $zona->setChangeAction($change_secoes_action);


        $cidade->setValue('ANANINDEUA');
        $cidade->setEditable(1);


        // self::onChangeAction(['id' => $param['id'] ?? null]);

        // $solicitacao->addItems($options);

        $hours = array();
        for ($n = 0; $n < 24; $n++) {
            $hours[$n] = "$n:00";
        }


        // Transformfields 

        $id->setEditable(false);
        $nome->forceUpperCase();
        $descricao->forceUpperCase();
        $telefone->class = "telefone";


        $PosicaoItens = [
            "titular" => "Titular",
            "reserva" => "Reserva"
        ];

        $posicao->addItems($PosicaoItens);
        $posicao->setDefaultOption('Selecione');
        $posicao->setValue('titular');

        // $cidade->enableSearch("true");
        // $cidade->setEditable(false);
        $cidade->setId('cidade');
        // $cidade->setValue('Ananindeua');

        $zona->enableSearch(true);
        $zona->setId('zona');

        $dataCadastro = new TDate('data_cadastro');
        $dataCadastro->setValue(date('d/m/Y'));
        $dataCadastro->setMask('dd/mm/yyyy');
        $dataCadastro->setDatabaseMask('yyyy-mm-dd');

        $descricao->setSize('100%', 50);

        $telefone->setMask('(99) 9 9999-9999');
        $telefoneFixo->setMask('(99) 9999-9999');

        // $local_consulta->placeholder = "Ex.: Policlínica";
        // $endereco_consulta->placeholder = "Ex.: Arterial 18, 124 - Ananindeua, Centro";

        // add campos
        // make form structure


        $row =  $this->form->addFields(
            [],
            [new TLabel('#ID'), $id],
            [new TLabel('Nome'), $nome]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-2', 'col-sm-6'];

        $row =  $this->form->addFields(
            [new TLabel('')],
            [new TLabel('Telefone (Whatsapp)'), $telefone],
            [new TLabel('Telefone Fixo'), $telefoneFixo]

        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-4', 'col-sm-4'];


        $row = $this->form->addFields(
            [new TLabel('OBSERVAÇÃO')],
            [$observacao]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields(
            [new TLabel('POSIÇÃO')],
            [$posicao]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-3'];


        $this->form->appendPage('OUTRAS INFORMAÇÕES');

        $label = new TLabel('Localização', '#7D78B6', 12, 'bi');
        $label->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent([$label]);

        $row = $this->form->addFields(
            [],
            [new TLabel('Zona'), $zona],
            [new TLabel('Seção'), $secao]
        );


        $row->layout = ['col-sm-2 control-label', 'col-sm-4', 'col-sm-4'];

        $row = $this->form->addFields(
            [],
            [new TLabel('Local de votação'), $local_votacao]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields(
            [new TLabel('Cidade')],
            [$cidade]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $label = new TLabel('Informações Adicionais', '#7D78B6', 12, 'bi');
        $label->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent([$label]);

        $row = $this->form->addFields(
            [new TLabel('Indicação')],
            [$indicacao]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];


        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'far:check-circle green');



        /**
         * Script para controle das máscaras
         **/

        $script = new TElement('script');
        $script->type = 'text/javascript';
        $javascript = "       
        // $(\"input.telefone\")
        // .mask(\"(99) 9999-99999\")
        // .focusout(function (event) {  
        //     var target, phone, element;  
        //     target = (event.currentTarget) ? event.currentTarget : event.srcElement;  
        //     phone = target.value.replace(/\D/g, '');
        //     element = $(target);  
        //     element.unmask();  
        //     if(phone.length > 10) {  
        //         element.mask(\"(99) 9 9999-9999\");  
        //     } else {  
        //         element.mask(\"(99) 9999-9999\");  
        //     }  
        // });          
        ";
        $script->add($javascript);
        parent::add($script);
        // wrap the page content using vertical box

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        // $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->html);
        $vbox->add($this->form);

        parent::add($vbox);
    }



    /**
     * Action to be executed when the user changes the combo_change field
     */
    public static function onGetLocaisVotacao($param)
    {


        try {


            // echo "<br>";
            TTransaction::open('sistema'); // open transaction

            // query criteria
            $criteria = new TCriteria;
            // $criteria->add(new TFilter('municipio', 'LIKE', $cidade));
            // $criteria->add(new TFilter('status', '=', 'M'));

            // load using repository

            if (isset($param['type']) && $param['type'] == 'fiscal') {

                $data = new SisPessoasFiscais($param['id']);

                // echo "<pre>";
                // var_dump($data);
                // echo "</pre>";

            } elseif (isset($param['type']) && $param['type'] == 'zonas') {
                # code...

                // $conn = TTransaction::get(); // get PDO connection

                $cidade = $param['cidade'] ?? 'ANANINDEUA';

                $data = SisLocaisVotacaoView::where('municipio', '=', $cidade)
                    ->groupBy('zona')
                    ->load();

                // $data = $list;
            } elseif (isset($param['type']) && $param['type'] == 'secao') {

                $cidade = $param['cidade'] ?? 'ANANINDEUA';
                $zona = $param['zona'] ?? '43';

                $data = SisLocaisVotacaoView::where('municipio', '=', $cidade)
                    ->where('zona', '=', $zona)
                    ->load();
     
            } else {

                // $criteria->add(new TFilter('municipio', 'LIKE', 'ANANINDEUA'));
                // // $criteria->add(new TFilter('status', '=', 'M'));

                // $repository = new TRepository('SisLocaisVotacaoFull');

                $conn = TTransaction::get(); // get PDO connection
                // run query
                $data = $conn->query("SELECT id, zona, municipio from locais_votacao WHERE municipio = 'ANANINDEUA'  GROUP BY zona order by id");

                // $data = $repository->load($criteria);
            }

            TTransaction::close(); // close transaction

            return $data;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function messageLoad()
    {
    }

    public static function onChangeCidadeZona($param)
    {

        try {

            /**
             * initial change
             */
            $zonas = self::onGetLocaisVotacao(['type' => 'zonas', 'cidade' => $param['cidade'] ?? 'ANANINDEUA']);


            // $obj = new StdClass;

            if (!empty($zonas)) {

                $listZona = [];

                foreach ($zonas as $key => $zona) {
                    $listZona[$zona->zona] = $zona->zona;
                }

                $listZona[0] = "Selecione";
                TCombo::reload('form_interaction', 'zona', $listZona);
            }


            // $obj->zona =  0;
            // TForm::sendData('form_interaction', $obj);
        } catch (\Throwable $th) {
            //throw $th;
            echo $th;
        }
    }


    /**
     * Action to be executed when the user changes the combo_change field
     */
    public static function onChangeActionSecoes($param)
    {

        try {
            //code...

            /**
             * initial change
             */

            $zonas = self::onGetLocaisVotacao(['type' => 'zonas', 'cidade' => $param['cidade'] ?? 'ANANINDEUA']);


            if (!empty($zonas)) {

                $listZona = [];

                foreach ($zonas as $key => $zona) {
                    $listZona[$zona->zona] = $zona->zona;
                }

                TCombo::reload('form_interaction', 'secoes', $listZona);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    /**
     * Action to be executed when the user changes the combo_change field
     */
    public static function onChangeAction($param)
    {

        try {

            echo "escolhendo zonas de {$param['cidade']}<br>";

            $option = $param['cidade'] ?? 'ANANINDEUA';

            // $obj = new StdClass;

            // if ($option != "ANANINDEUA") {
            //     new TMessage('error', 'Cidade não cadastrada');

            //     $obj->local_votacao = "";
            // $obj->cidade = 'ANANINDEUA';
            // $obj->zona = $zona;
            //     $obj->secao = null;
            //     TForm::sendData('form_interaction', $obj);
            //     return false;
            // }

            $locais = self::onGetLocaisVotacao(['type' => 'zonas', 'cidade' => $param['cidade'] ?? null]);

            $listZona = array();

            if (!empty($locais)) {

                foreach ($locais as $key => $local) {
                    # code...

                    $listZona[$local->zona] = $local->zona;
                }
            }

            TCombo::reload('form_interaction', 'zona', $listZona);

            // TForm::sendData('form_interaction', $obj);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Post data
     */
    public function onSave($param)
    {
        try {

            // create a new object

            $data = $this->form->getData(); // optional parameter: active record class
            $this->form->setData($data);

            // var_dump(TSession::getValue(__CLASS__ . "type_register"));
            // var_dump($data);

            // return;
            if (!$data->nome) {

                new TMessage('error', 'Faltando Nome');
                return false;
            }

            // if (!$data->fone1) {

            //     new TMessage('error', 'Faltando Telefone');
            //     return false;
            // }

            // if (!$data->cidade || !$data->bairro) {

            //     // new TMessage('error', 'Faltando completar endereço');
            //     // return false;
            // }

            $cidade = $data->cidade ?? false;

            TTransaction::open('sistema'); // open transaction

            $criteria = new TCriteria;

            // load using repository
            $repository = new TRepository('SisLocaisRegioes');
            $criteria->add(new TFilter('municipio', 'LIKE', $cidade));
            $regioes = $repository->load($criteria);

            $regiaoId = null;

            if ($cidade) {


                foreach ($regioes as $regiao) {

                    $regiaoId = $regiao->regiao_id ?? false;


                    // echo "regiao encontrada: " . "$regiaoId - $regiao->regiao";
                }
            }




            $object = new SisPessoasFiscais;

            $unitId         = TSession::getValue('userunitid');
            $userId         = TSession::getValue('userid');


            $object->fromArray((array) $data);



            $object->unit_id    = $unitId;
            $object->updated_by = $userId;
            $object->user_id    = $userId;
            $object->regiao_id  = $regiaoId;

            if ($object->fone1) {

                $object->fone1    = $this->removeCaracteresEspeciais($object->fone1);
            }
            if ($object->fone2) {

                $object->fone2    = $this->removeCaracteresEspeciais($object->fone2);
            }

            $object->solicitacao   = json_encode($object->solicitacao);

            // if (empty($object->created_by)) {
            //     $object->created_by  = $userId;
            // }

            $object->store(); // store the object

            $redirect = NULL;

            $classRedirect = [
                6 => "ListaMultiplicadoresDataGridView",
                7 => "SisListaFiscaisDataGridView",
                8 => "ListaColaboradoresDataGridView",
                2 => "ListaAssessoriaDataGridView",
                120 => "ListaAtendimentosSaudeDataGridView"
            ];

            $redirect = new TAction(array($classRedirect[7], 'onReload'));

            if ($object->segmento) $redirect = new TAction(array($classRedirect[$object->segmento], 'onResponse'));
            // new TMessage('info', 'Informações salvas com Sucesso!');
            new TMessage('info', 'Informações salvas com Sucesso!', $redirect);

            TTransaction::close(); // Closes the transaction
            // TApplication::loadPage('ListaAtendimentosSaudeDataGridView', 'onTeste');
            // TApplication::executeMethod('ListaAtendimentosSaudeDataGridView','onResponse');

            // $objects = TSession::getValue('session_contacts');
            // $objects[$data->code] = $data;

            // TSession::setValue('session_contacts', $objects);

            // $this->enviarEmail($param);
            // mensagem de sucesso e reloada dos dados da view
            // new TMessage('info', 'Record added', new TAction(array('ListaPessoasGroupView', 'onReload')));

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }



    public function onLoad($param)
    {

        try {


            self::onChangeCidadeZona(['cidade' => 'ANANINDEUA']);
        } catch (\Throwable $th) {
            //throw $th;
        }
        /**
         * carregar cidade e zona
         */

        // var_dump($param["key"]);





    }

    public static function checkPermission($param)
    {

        try {

            // nao existe verificacoes
            return true;

            $key = $param['key']; // get the parameter $key
            TTransaction::open('sistema'); // open a transaction with database
            $object = new SisPessoas($key, FALSE); // instantiates the Active Record

            TTransaction::close(); // close the transaction


            $userId = TSession::getValue("userid");


            // permitindo editar pelo administrador
            $permiteds = [1];
            if (in_array($userId, $permiteds)) {

                return true;
            }

            if ($object->created_by == $userId) {

                return true;
            }

            if ($object->created_by == $userId) {

                return true;
            }

            return false;
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }


    public function removeCaracteresEspeciais($str)
    {


        $res = preg_replace("/[^0-9]/", "", $str);
        return $res;
    }

    public function onEdit($param)
    {

        try {
            if (isset($param['key'])) {

                // get the parameter $key
                $key = $param['key'];


                if (!self::checkPermission($param)) {

                    new TMessage("error", "Sem permissão para editar!");
                    return false;
                }

                // open a transaction with database 'permission'
                TTransaction::open('sistema');

                // instantiates object System_user
                $object = new SisPessoasFiscais($key);

                $this->getLocaisBytype(array('cidade' => $object->cidade ?? false, 'zona' => $object->zona ?? null));
                // $this->getSecoesBytype(array('cidade' => $object->cidade ?? false, 'zona' => $object->zona ?? null));

                // self::onChangeCidadeZona(['cidade' => $object->cidade, 'zona' => $object->zona  ?? false]);


                // fill the form with the active record data
                $this->form->setData($object);

                // close the transaction
                TTransaction::close();
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    public function getLocaisBytype($param)
    {

        try {
            //code...

            $obj = new StdClass;
            $obj->zona = $param['zona'];

            TCombo::reload('form_interaction', 'zona', []);
            $locais = self::onGetLocaisVotacao(['type' => 'zonas', 'cidade' => $param['cidade'] ?? null]);

            $listLocais = [];
            foreach ($locais as $key => $local) {
                # code...
                $listLocais[$local->zona] = $local->zona;
            }
            TCombo::reload('form_interaction', 'zona', $listLocais);
            TForm::sendData('form_interaction', $obj);

            $this->getSecoesBytype(array('cidade' => $param['cidade'] ?? false, 'zona' => $param['zona'] ?? null));

        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function getSecoesBytype($param)
    {

        try {
            //code...

            $obj = new StdClass;
            $obj->zona = $param['zona'];
            $obj->secao = $param['secao'] ?? null;

            $locais = self::onGetLocaisVotacao(['type' => 'secao', 'cidade' => $param['cidade'] ?? null, 'zona' => $param['zona'] ?? null]);

            $listLocais = [];
            
            foreach ($locais as $key => $local) {
                # code...
                $listLocais[$local->secao] = $local->secao;
            }

    
            TCombo::reload('form_interaction', 'secao', $listLocais);
            // TForm::sendData('form_interaction', $obj);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function getLocaisVotacaoBytype($param)
    {

        try {
            //code...

            $obj = new StdClass;
            $obj->zona = $param['zona'];
            $obj->secao = $param['secao'] ?? null;

            $locais = self::onGetLocaisVotacao(['type' => 'locais', 'cidade' => $param['cidade'] ?? null, 'zona' => $param['zona'] ?? null]);

            $listLocais = [];
            
            foreach ($locais as $key => $local) {
                # code...
                $listLocais[$local->secao] = $local->secao;
            }

    
            TCombo::reload('form_interaction', 'secao', $listLocais);
            // TForm::sendData('form_interaction', $obj);

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
