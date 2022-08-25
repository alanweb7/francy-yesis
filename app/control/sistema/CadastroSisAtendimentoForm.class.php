<?php

use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Wrapper\TDBCombo;
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
class CadastroSisAtendimentoForm extends TWindow
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

        // date_default_timezone_set('America/Belem');
        // "https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initMap"
        // TWindow::include_js('https://maps.googleapis.com/maps/api/js?key=AIzaSyCvlHIxGDuqD4hbZP2hQ0ojfelVlQT-u1s&sensor=false&libraries=places');
        TWindow::include_css('app/lib/include/generalCssForms.css?ver=1.2.37');
        // TWindow::include_js('app/lib/include/googleAutoComplete.js?ver=1.2.37');
        // TWindow::include_js('app/lib/include/jquery.geocomplete.js');

     
        $this->form = new BootstrapFormBuilder;
        if (!isset($_GET["id"])) {
            $this->inUpdate = true;
            $this->form->setFormTitle('NOVO CADASTRO');
        } else {

            $this->inUpdate = false;
            $this->form->setFormTitle('GERENCIAR CADASTRO');
        }
        $this->form->setFieldSizes('100%');
        $this->form->generateAria(); // automatic aria-label

        $this->form->style = 'display: table;width:100%'; // change style
        parent::setProperty('class', 'window_modal');

        $this->form->appendPage('Dados pessoais');



        $id = new THidden('id');
        $pessoa_id = new TEntry('pessoa_id');

        $criteria_type = new TCriteria();
        $criteria_type->add(new TFilter('meta_key', '=', "group_category"));
        $type_segment = new TDBCombo('segmento', 'sistema', 'SisGeneralField', 'id', 'name', 'name ASC', $criteria_type);

        $lotacao_status = new TCombo("lotacao_id");

        $lotacaoItems = [
            1 => "SIM",
            2 => "NÂO",
            3 => "Ajuda de Custo",
        ];

        $lotacao_status->addItems($lotacaoItems);
        $lotacao_status->setDefaultOption("Selecionar");

        $created_by = new THidden('created_by');

        $nome = new TEntry('nome');
        $cns = new TEntry('cns');
        $nome_mae = new TEntry('nome_mae');
        $nome->forceUpperCase();

        $indicacao = new TEntry('indicacao');
        $indicacao->forceUpperCase();

        $multiplicador = new TEntry('multiplicador');
        $multiplicador->forceUpperCase();

        $local_votacao = new TDBCombo('local_votacao', 'sistema', 'SisLocaisVotacaoRegioes', 'local_votacao', 'local_votacao', 'local_votacao ASC');
        $local_votacao->enableSearch('true');


        $zona = new TDBCombo('zona', 'sistema', 'SisLocaisVotacaoRegioes', 'zona', 'zona', 'zona ASC');
        $zona->enableSearch('true');

        $secao = new TDBCombo('secao', 'sistema', 'SisLocaisVotacaoRegioes', 'secao', 'secao', 'secao ASC');
        $secao->enableSearch('true');

        $type_segment->setDefaultOption("Selecionar");
        if (isset($_GET["type_register"])) {
            $typeSegmentVal = $_GET["type_register"];
            $type_segment->setValue($typeSegmentVal);
            $type_segment->setEditable(false);
        }

        $dataCadastro = new TDate('data_cadastro');
        $dataCadastro->setValue(date('d/m/Y'));
        $dataCadastro->setMask('dd/mm/yyyy');
        $dataCadastro->setDatabaseMask('yyyy-mm-dd');

        $sexo = new TCombo('sexo');

        $sexoItens = [
            1 => 'Masculino',
            2 => 'Feminino',
            3 => 'Não Informar'
        ];

        $sexo->addItems($sexoItens);
        $sexo->setDefaultOption('Selecionar');


        $dataNasc = new TDate('nascimento');
        $dataNasc->setMask('dd/mm/yyyy');
        $dataNasc->setDatabaseMask('yyyy-mm-dd');

        $horaCadastro = new TTime('hora_cadastro');
        $horaCadastro->setValue(date('H:i'));

        $endereco = new TEntry('endereco');
        $endereco->class = "controls";
        $endereco->setId("searchInput");
        $complemento = new TEntry('complemento');


        $pessoa_id->setEditable(false);

    
        $addressList = new TElement('div');
        $addressList->add('<div id="addresList">-</div>');

        $bt5a = new TButton('bt5a');
        $bt5a->setLabel('Buscar Endereço');
        $bt5a->addFunction("searchAddress();");
        $bt5a->class = 'btn btn-success btn-lg';

        // add campos
        $this->form->addFields([$id], [$created_by]);


        $row =  $this->form->addFields(
            [new TLabel('')],
            [new TLabel('Mátricula'), $pessoa_id]
        );

        $pessoa_id->setValue($param["id"]);

        $row->layout = ['col-sm-2 control-label', 'col-sm-4'];

        $row =  $this->form->addFields(
            [new TLabel('')],
            [new TLabel('Tipo de Cadastro'), $type_segment],
            [new TLabel('Lotação'), $lotacao_status],
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-4', 'col-sm-4'];


        $row =  $this->form->addFields(
            [new TLabel('Nome da Mãe')],
            [$nome_mae]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields(
            [new TLabel('Endereço')],
            [$endereco]
            // ,[$bt5a]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-6', 'col-sm-2'];


        $row = $this->form->addFields([$addressList]);
        $row->layout = ['col-sm-12'];



        $row = $this->form->addFields([new TLabel('Complemento')], [$complemento]);
        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields(
            [new TLabel('CNS')],
            [$cns]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-2'];

    
        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'far:check-circle green');

        // wrap the page content using vertical box

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        // $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
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

            if (!$data->fone1) {

                new TMessage('error', 'Faltando Telefone');
                return false;
            }

            if (!$data->cidade || !$data->bairro) {

                // new TMessage('error', 'Faltando completar endereço');
                // return false;
            }

            TTransaction::open('sistema'); // open transaction
            $object = new SisAtendimentos;

            $unitId         = TSession::getValue('userunitid');
            $userId         = TSession::getValue('userid');


            $object->fromArray((array) $data);



            $object->unit_id        = $unitId;
            $object->updated_by     = $userId;
            $object->operador_id    = $userId;

            if (empty($object->created_by)) {
                $object->created_by  = $userId;
            }

            $object->store(); // store the object

            $redirect = NULL;

            $classRedirect = [
                6 => "ListaMultiplicadoresDataGridView",
                8 => "ListaColaboradoresDataGridView",
                2 => "ListaAssessoriaDataGridView"
            ];

            if($object->segmento) $redirect = new TAction(array($classRedirect[$object->segmento], 'onReload'));
            
            // new TMessage('info', 'Informações salvas com Sucesso!');
            new TMessage('info', 'Informações salvas com Sucesso!', $redirect);
            
            TTransaction::close(); // Closes the transaction

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

        TSession::setValue(__CLASS__ . "user_atendimento", $param["key"]);

    }

    public static function checkPermission($param)
    {

        try {
            $key = $param['key']; // get the parameter $key
            TTransaction::open('unit_a'); // open a transaction with database
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

            return false;
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
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
                $object = new SisAtendimentos($key);

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
