<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Registry\TSession;
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
class SisCadastroLocaisVotacaoForm extends TWindow
{
    private $form;
    private $inUpdate = false;
    private $typeRegister;

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

        $this->form = new BootstrapFormBuilder;
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



        $id = new THidden('id');
        $nome = new TEntry('nome');
        $telefone = new TEntry('telefone');
        $local_consulta = new TEntry('local_consulta');
        $situacao = new TCombo('situacao');
        $endereco_consulta = new TEntry('endereco_consulta');

        $criteria_type = new TCriteria();
        // $criteria_type->add(new TFilter('meta_key', '=', "group_category"));
        $tipo_solicitacao = new TDBCombo('segmento', 'sistema', 'SisTipoAtendimento', 'id', 'nome', 'nome ASC', $criteria_type);


        //Hidden Fields
        $created_by = new THidden('created_by');

        $nome = new TEntry('nome');
        $telefone = new TEntry('fone1');
        $telefoneFixo = new TEntry('fone2');
        $descricao = new TEntry('descricao');

        $endereco = new TEntry('endereco');
        $cidade = new TDBCombo('cidade', 'sistema', 'SisCidadesVw', 'nome', 'nome', 'nome ASC');
        $bairro = new TEntry('bairro');

        $observacao = new TText('observacao');
        $cns = new TEntry('cns');
        $nome_mae = new TEntry('nome_mae');
        $data_consulta = new TDate('data_consulta');
        $hora_consulta = new TCombo('hora_consulta');

        // $solicitacao     = new TMultiSearch('solicitacao');
        $solicitacao     = new TDBMultiSearch('solicitacao', 'sistema', 'SisTipoAtendimento', 'id', 'nome', 'nome ASC', $criteria_type);
        $indicacao     = new TEntry('indicacao');


        // $options = $this->getEspecialidades(NULL);

        $solicitacao->setMinLength(1);


        // $solicitacao->addItems($options);

        $hours = array();
        for ($n = 0; $n < 24; $n++) {
            $hours[$n] = "$n:00";
        }


        // Transformfields 
        $nome->forceUpperCase();
        $descricao->forceUpperCase();
        $hora_consulta->addItems($hours);
        $hora_consulta->setValue(8);
        $telefone->class = "telefone";


        $SituacaoItens = [
            "solicitado" => "Solicitado",
            "resolvido" => "Resolvido"
        ];

        $situacao->addItems($SituacaoItens);
        $situacao->setDefaultOption('Selecione');

        $cidade->enableSearch("true");
        // $cidade->setEditable(false);
        $cidade->setId('cidade');
        $cidade->setValue('Ananindeua');

        $dataCadastro = new TDate('data_cadastro');
        $dataCadastro->setValue(date('d/m/Y'));
        $dataCadastro->setMask('dd/mm/yyyy');
        $dataCadastro->setDatabaseMask('yyyy-mm-dd');

        $descricao->setSize('100%', 50);
        $data_consulta->setMask('dd/mm/yyyy');
        $data_consulta->setDatabaseMask('yyyy-mm-dd');
        $data_consulta->setValue(date('Y-m-d H:i'));


        $telefone->setMask('(99) 9 9999-9999');
        $telefoneFixo->setMask('(99) 9999-9999');

        $local_consulta->placeholder = "Ex.: Policlínica";
        $endereco_consulta->placeholder = "Ex.: Arterial 18, 124 - Ananindeua, Centro";

        // add campos
        $this->form->addFields([$id], [$created_by]);



        // make form structure




        $row =  $this->form->addFields(
            [new TLabel('')],
            [new TLabel('Nome'), $nome],
            [new TLabel('CNS'), $cns]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-6', 'col-sm-2'];

        $row =  $this->form->addFields(
            [new TLabel('')],
            [new TLabel('Telefone (Whatsapp)'), $telefone],
            [new TLabel('Telefone Fixo'), $telefoneFixo]

        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-4', 'col-sm-4'];


        $row =  $this->form->addFields(
            [new TLabel('')],
            [new TLabel('DATA MARCADA'), $data_consulta],
            [new TLabel('HORA'), $hora_consulta]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-4', 'col-sm-4'];


        $row = $this->form->addFields(
            [new TLabel('SOLICITAÇAO')],
            [$solicitacao]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields(
            [new TLabel('OBSERVAÇÃO')],
            [$observacao]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields(
            [new TLabel('SITUAÇÃO')],
            [$situacao]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-3'];


        $this->form->appendPage('OUTRAS INFORMAÇÕES');

        $row = $this->form->addFields(
            [new TLabel('Indicação')],
            [$indicacao]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $label = new TLabel('Paciente / Solicitante', '#7D78B6', 12, 'bi');
        $label->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent([$label]);

        $row = $this->form->addFields(
            [new TLabel('Endereço do Solicitante')],
            [$endereco]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields(
            [new TLabel('Cidade')],
            [$cidade],
            [new TLabel('Bairro')],
            [$bairro]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-3', 'col-sm-1 control-label', 'col-sm-4'];

        $label = new TLabel('Hospital / Clínica', '#7D78B6', 12, 'bi');
        $label->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent([$label]);

        $row = $this->form->addFields(
            [new TLabel('Local da Consulta')],
            [$local_consulta]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields(
            [new TLabel('Endereço da Consulta')],
            [$endereco_consulta]
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

            TTransaction::open('sistema'); // open transaction
            $object = new SisLocaisVotacaoTRecord;

            $unitId         = TSession::getValue('userunitid');
            $userId         = TSession::getValue('userid');


            $object->fromArray((array) $data);



            $object->unit_id        = $unitId;
            $object->updated_by     = $userId;
            $object->user_id    = $userId;

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
                8 => "ListaColaboradoresDataGridView",
                9 => "SisListaLocaisVotacaoGridView",
                2 => "ListaAssessoriaDataGridView",
                120 => "ListaAtendimentosSaudeDataGridView"
            ];

            $redirect = new TAction(array($classRedirect[9], 'onReload'));

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


    public function testeMensagem($param)
    {

        new TMessage('info', 'Rodou');
        parent::closeWindow();
    }


    public function enviarEmail($param)
    {

        $email = 'alanweb7@gmail.com';
        // $protocolo = $param['protocolo'];//Número do protocolo da solicitação
        $protocolo = '123'; //Número do protocolo da solicitação

        try {
            $mail = new TMail;
            $mail->setFrom('contato@siei.com.br', "Nome de envio");
            $mail->setSubject('vaga de emprego');
            $mail->setHtmlBody('Você foi selecionado para a entrevista de empregoa referente a vaga: . Solicitamos que entre em contato com a empresa para agendar a entrevista!');
            $mail->addAddress($email, "Nome email");
            $mail->SetUseSmtp();
            $mail->SetSmtpHost('mail.siei.com.br', '465');
            $mail->SetSmtpUser('contato@siei.com.br', '@segurolive332'); //retirei a senha por questão de segurança
            //$mail->setReplyTo($ini['repl']);
            $mail->send(); // enviar
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        } catch (Exception $e) {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
        }
    }

    public function sendMail()
    {

        $preferences = SystemPreference::getAllPreferences();
        $mail = new TMail;
        $mail->SMTPDebug = 2;
        $mail->setFrom(trim($preferences['mail_from']), 'CRONOTEAM');
        $mail->addAddress(trim('alanweb7@gmail.com'), "Alan Silva");
        $mail->setSubject('CronoTeam - Comprovante de inscrição');
        // $mail->addAttach( $file, 'Comprovante de inscrição.pdf' );
        if ($preferences['smtp_auth']) {
            $mail->SetUseSmtp();
            $mail->SetSmtpHost($preferences['smtp_host'], $preferences['smtp_port']);
            $mail->SetSmtpUser($preferences['smtp_user'], $preferences['smtp_pass']);
        }
        $body = str_replace('##NOME DO ATLETA##', 'Nome de teste', $preferences['corpo_comprovante']);
        $body = str_replace('##EVENTO##', "Evento de teste", $body);
        $mail->setTextBody($body);
        $mail->send();
    }

    public function onLoad($param)
    {

        // echo "entrou no onload<br>";
        // echo "<pre>";
        // var_dump($param);
        // echo "</pre>";
        // echo "<pre>";
        // var_dump($param["key"]);
        // echo "</pre>";

        // TSession::setValue(__CLASS__ . "user_atendimento", $param["id"]);

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

    public function getEspecialidades($param)
    {


        $list = [
            1 => 'ANGIOLOGISTA',
            2 => 'CARDIO',
            3 => 'C. CARDIOVASCULAR',
            4 => 'C. GERAL',
            5 => 'DERMA',
            6 => 'ENDOCRIONO',
            7 => 'FONO',
            8 => 'GASTRO',
            9 => 'GERIATRIA',
            10 => 'GINECOLOGIA',
            11 => 'INFECTOMASTO',
            12 => 'NEFRO',
            13 => 'NEUROLOGIA',
            14 => 'NUTRICIONISTA',
            15 => 'OFTALMO',
            16 => 'ORTOPEDIA',
            17 => 'OTORRINO',
            18 => 'PEDIATRA',
            19 => 'PNEUMO',
            20 => 'PSIQUIATRA',
            21 => 'PISICOLOGO',
            22 => 'REUMATO',
            23 => 'UROLOGISTA',
            24 => 'D. OSSEA',
            25 => 'EDA',
            26 => 'RX',
            27 => 'USG',
            28 => 'FISIOTERAPIA',
            29 => 'ANESTESIOLOGISTA',
            30 => 'ALERGISTA',
            31 => 'INFECTOLOGISTA',
            32 => 'NEURO CIRURGIA',
            33 => 'NEURO PEDIATRA',
            34 => 'ELETROCARDIOGRAMA'
        ];



        try {

            // nao existe verificacoes
       
            TTransaction::open('sistema'); // open transaction
            
            // query criteria
            $criteria = new TCriteria; 
            // $criteria->add(new TFilter('gender', '=', 'F')); 
            // $criteria->add(new TFilter('status', '=', 'M')); 
            
            // load using repository
            $repository = new TRepository('SisTipoAtendimento'); 
            $proced = $repository->load($criteria); 
            $listaprocedimentos = [];
            
            foreach ($proced as $key => $proc) {
                
                $listaprocedimentos[] = $proc;
                
            }
            
            TTransaction::close(); // open transaction

            return $listaprocedimentos;
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
                $object = new SisAtendimentoSaude($key);


                $object->data_consulta = TDate::date2br($object->data_consulta);
                $object->solicitacao = json_decode($object->solicitacao);
                // $object->data_consulta = DateTime::createFromFormat('Y-m-d', $object->data_consulta)->format( 'd/m/Y' );
                // $object->data_consulta = date('yyyy-mm-dd H:i');

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
}
