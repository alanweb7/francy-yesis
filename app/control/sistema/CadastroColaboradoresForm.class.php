<?php

use Adianti\Control\TWindow;
use Adianti\Registry\TSession;
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
class CadastroColaboradoresForm extends TWindow
{
    private $form;
    private $inUpdate = false;

    /**
     * Class constructor
     * Creates the page
     */
    public function __construct()
    {
        parent::__construct();
        parent::setSize(0.8, null);

        // date_default_timezone_set('America/Belem');
        // "https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initMap"
        TWindow::include_js('https://maps.googleapis.com/maps/api/js?key=AIzaSyCvlHIxGDuqD4hbZP2hQ0ojfelVlQT-u1s&sensor=false&libraries=places');
        TWindow::include_css('app/lib/include/generalCssForms.css?ver=1.2.37');
        TWindow::include_js('app/lib/include/googleAutoComplete.js?ver=1.2.37');
        TWindow::include_js('app/lib/include/jquery.geocomplete.js');

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('CADASTRO DE COLABORADORES');
        $this->form->setFieldSizes('100%');
        $this->form->generateAria(); // automatic aria-label

        $this->form->style = 'display: table;width:100%'; // change style
        parent::setProperty('class', 'window_modal');

        $this->form->appendPage('Dados pessoais');

        $id = new THidden('id');
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
        
        $localizacao = new THidden('localizacao');
        $localizacao->setId("localizacao");
        
        $cidade = new TEntry('cidade');
        $cidade->forceUpperCase();
        // $cidade->setEditable(false);
        $cidade->setId('cidade');
        
        $bairro = new TEntry('bairro'); 
        // $bairro->setEditable(false);
        $bairro->setId('bairro');

        $cep = new TEntry('cep');

        $telefone = new TEntry('fone1');
        $telefone2 = new TEntry('telefone2');
        $telefone->class = "telefones";
        $telefone2->class = "telefones";


        $addressList = new TElement('div');
        $addressList->add('<div id="addresList">-</div>');

        $bt5a = new TButton('bt5a');
        $bt5a->setLabel('Buscar Endereço');
        $bt5a->addFunction("searchAddress();");
        $bt5a->class = 'btn btn-success btn-lg';

        // add campos
        $this->form->addFields([$id],[$created_by]);
        

        $row =  $this->form->addFields(
            [new TLabel('')],
            [new TLabel('Nome'), $nome],
            [new TLabel('Sexo'), $sexo],
            [new TLabel('Dt. Nascimento'), $dataNasc],
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-4', 'col-sm-2', 'col-sm-2'];

        $row =  $this->form->addFields(
            [new TLabel('Nome da Mãe')],
            [$nome_mae]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields(
            [new TLabel('Endereço')],
            [$endereco],
            [$bt5a]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-6', 'col-sm-2'];


        $row = $this->form->addFields([$addressList]);
        $row->layout = ['col-sm-12'];



        $row = $this->form->addFields(
            [new TLabel('Cidade')],
            [$cidade],
            [new TLabel('Bairro')],
            [$bairro],
            [new TLabel('CEP')],
            [$cep]
        );
        $row->layout = ['col-sm-2 control-label', 'col-sm-2', 'col-sm-1 control-label', 'col-sm-2', 'col-sm-1 control-label', 'col-sm-2'];

        $row = $this->form->addFields([new TLabel('Complemento')], [$complemento]);
        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields(

            [new TLabel('Fone 1')],
            [$telefone],
            [new TLabel('Fone 2')],
            [$telefone2],
            [new TLabel('CNS')],
            [$cns]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-2', 'col-sm-1 control-label', 'col-sm-2', 'col-sm-1 control-label', 'col-sm-2'];



        $this->form->addFields([$localizacao]);

        $this->form->appendPage('informações detalhadas');

        $row = $this->form->addFields(
            [new TLabel('Local de Votação'), $local_votacao],
            [new TLabel('Zona'), $zona],
            [new TLabel('Seção'), $secao],
            [new TLabel('Indicador / Liderança'), $multiplicador]
        );

        $row->layout = ['col-sm-8', 'col-sm-2', 'col-sm-2', 'col-sm-6'];

        $this->form->addAction('Salvar', new TAction(array($this, 'onSend')), 'far:check-circle green');

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
    public function onSend($param)
    {
        try {

            // create a new object

            $data = $this->form->getData(); // optional parameter: active record class
            $this->form->setData($data);

            if (!$data->nome) {

                new TMessage('error', 'Faltando Nome');
                return false;
            }

            if (!$data->fone1) {

                new TMessage('error', 'Faltando Telefone');
                return false;
            }

            if (!$data->cidade || !$data->bairro) {

                new TMessage('error', 'Faltando completar endereço');
                return false;
            }

            TTransaction::open('sistema'); // open transaction
            $object = new SisPessoas;

            $unitId         = TSession::getValue('userunitid');
            $userId         = TSession::getValue('userid');
   

            $object->fromArray((array) $data);



            $object->unit_id        = $unitId;
            $object->user_id        = $userId;
            $object->updated_by     = $userId;
            $object->operador_id    = $userId;

            if(empty($object->created_by)){
                $object->created_by  = $userId;
            }

            $object->store(); // store the object

            new TMessage('info', 'Informações salvas com Sucesso!', new TAction(array('ListaColaboradoresDataGridView', 'onReload')));
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

    public function onLoad()
    {

       
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
            if(in_array($userId, $permiteds) ){

                return true;

            }

            if($object->created_by == $userId){

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


                if(!self::checkPermission($param)){
    
                    new TMessage("error", "Sem permissão para editar!");
                    return false;
    
                }

                // open a transaction with database 'permission'
                TTransaction::open('sistema');

                // instantiates object System_user
                $object = new SisPessoas($key);

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