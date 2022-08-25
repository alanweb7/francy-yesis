<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TTime;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * FormBuilderGridView
 *
 * @version    1.0 
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class CadastroPessoasForm extends TWindow
{
    private $form;

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

        $this->form->style = 'display: table;width:100%'; // change style
        parent::setProperty('class', 'window_modal');

        $this->form->appendPage('Dados pessoais');

        $id = new THidden('id');

        $nome = new TEntry('nome');
        $nome->forceUpperCase();
        
        $indicacao = new TEntry('indicacao');
        $indicacao->forceUpperCase();

        $dataCadastro = new TDate('data_cadastro');
        $dataCadastro->setValue( date('d/m/Y') );
        $dataCadastro->setMask('dd/mm/yyyy');
        $dataCadastro->setDatabaseMask('yyyy-mm-dd');
        
        $dataNasc = new TDate('data_nascimento');
        $dataNasc->setMask('dd/mm/yyyy');
        $dataNasc->setDatabaseMask('yyyy-mm-dd');
        
        $horaCadastro = new TTime('hora_cadastro');
        $horaCadastro->setValue( date('H:i') );

        $endereco = new TEntry('endereco');
        $endereco->class = "controls";
        $endereco->setId("searchInput");
        
        $localizacao = new THidden('localizacao');
        $localizacao->setId("localizacao");

        $cidade = new TEntry('cidade');
        $cidade->setEditable(false);
        $cidade->setId('cidade');

        $bairro = new TEntry('bairro');
        $bairro->setEditable(false);
        $bairro->setId('bairro');
        
        $telefone = new TEntry('telefone');
        $telefone->class = "telefones";


        $addressList = new TElement('div');
        $addressList->add('<div id="addresList">-</div>');

        $bt5a = new TButton('bt5a');
        $bt5a->setLabel('Buscar Endereço');
        $bt5a->addFunction("searchAddress();");
        $bt5a->class = 'btn btn-success btn-lg';

        // add campos
         $this->form->addFields( [ $id ]);

       $row =  $this->form->addFields( 
           [ new TLabel('Nome') ], [ $nome ],
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-8'];

        $row = $this->form->addFields([new TLabel('Endereço')],
            [$endereco], [$bt5a]);

        $row->layout = ['col-sm-2 control-label', 'col-sm-6', 'col-sm-2'];

        $row = $this->form->addFields([$addressList]);
        $row->layout = ['col-sm-12'];

        $row = $this->form->addFields(
            [new TLabel('Cidade')],
            [$cidade],
            [new TLabel('Bairro')],
            [$bairro]
        );
        $row->layout = ['col-sm-2 control-label', 'col-sm-3', 'col-sm-2 control-label', 'col-sm-2'];

        $row = $this->form->addFields(
            [new TLabel('Data do Atendimento')], [$dataCadastro],
            [new TLabel('Hora')], [$horaCadastro]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-3', 'col-sm-2 control-label', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Dt. Nascimento') ],
        [ $dataNasc ],
        [ new TLabel('Telefone') ],
        [ $telefone ]);
        
        $row->layout = ['col-sm-2 control-label', 'col-sm-3', 'col-sm-2 control-label', 'col-sm-2'];
        
        $this->form->addFields( [ $localizacao ]);
 
        $this->form->appendPage('informações detalhadas');

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

            $data = $this->form->getData();

            if(!$data->nome){

                new TMessage('error', 'Faltando Nome');
                return false;
                
            }

            if(!$data->telefone){

                new TMessage('error', 'Faltando Telefone');
                return false;
                
            }

            if(!$data->cidade || !$data->bairro ){

                new TMessage('error', 'Faltando completar endereço');
                return false;
                
            }

            $this->form->setData($data);

            TTransaction::open('sistema'); // open transaction
            $object = new SisPessoas;
           
            $object->fromArray((array) $data);
            $object->store(); // store the object

            new TMessage('info', 'Informações salvas com Sucesso!', new TAction(array('ListaPessoasDataGridView', 'onReload')));
            TTransaction::close(); // Closes the transaction

            $objects = TSession::getValue('session_contacts');
            $objects[$data->code] = $data;

            TSession::setValue('session_contacts', $objects);

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
    $protocolo = '123';//Número do protocolo da solicitação
    
    try{
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
      }
      catch (Exception $e){
        new TMessage('error', '<b>Error</b> ' . $e->getMessage() );
      }
}

    public function sendMail(){

        $preferences = SystemPreference::getAllPreferences();
        $mail = new TMail;
        $mail->SMTPDebug = 2;
        $mail->setFrom( trim($preferences['mail_from']), 'CRONOTEAM' );
        $mail->addAddress( trim('alanweb7@gmail.com'), "Alan Silva" );
        $mail->setSubject( 'CronoTeam - Comprovante de inscrição' );
        // $mail->addAttach( $file, 'Comprovante de inscrição.pdf' );
        if ($preferences['smtp_auth'])
        {
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

    public function onEdit($param)
    {
 
        try
        {
            if (isset($param['key']))
            {

                // get the parameter $key
                $key=$param['key'];
       
                // open a transaction with database 'permission'
                TTransaction::open('sistema');
                
                // instantiates object System_user
                $object = new SisPessoas($key);
                
                // fill the form with the active record data
                $this->form->setData($object);
                
                // close the transaction
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
