<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
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
class SisSegmentForm extends TWindow
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

        $this->form = new BootstrapFormBuilder;
        $this->form->setFormTitle('CADASTRAR NÍVEL');
        $this->form->setFieldSizes('100%');
        $this->form->generateAria(); // automatic aria-label

        $this->form->style = 'display: table;width:100%'; // change style
        parent::setProperty('class', 'window_modal');

        // $this->form->appendPage('Dados pessoais');

        $id = new THidden('id');

        $name = new TEntry('name');
        $level = new TCombo('meta_value');
        $name->forceUpperCase();

        $levelOptions = [
            0 => 'NENHUMA AÇÃO',
            1 => 'VISUALIZAR',
            2 => 'EDITAR',
            3 => 'CRIAR / EDITAR',
            4 => 'CRIAR / EDITAR / EXCLUIR'
        ];

        $level->addItems($levelOptions);
        $level->setDefaultOption("Selecione");
        $level->value(0);
        // add campos
        $this->form->addFields([$id]);

        $row =  $this->form->addFields(
            [new TLabel('Nome')],
            [$name],
            [new TLabel('PERMISSÕES')],
            [$level]
        );

        $row->layout = [
            'col-sm-2 control-label', 'col-sm-4',
            'col-sm-2 control-label', 'col-sm-4',
        ];

        // $row =  $this->form->addFields(
        //     [new TLabel('')],
        //     [$nome_mae]
        // );

        // $row->layout = ['col-sm-2 control-label', 'col-sm-8'];


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

            if (!$data->name) {

                new TMessage('error', 'Faltando Nome');
                return false;
            }


            TTransaction::open('sistema'); // open transaction
            $object = new SisGeneralField;

            $unitId = TSession::getValue('userunitid');

            $object->fromArray((array) $data);
            $object->unit_id = $unitId;
            $object->meta_key = "user_permission";
        
            $object->group_id = TSession::getValue('usergroupids');
            $object->store(); // store the object

            TTransaction::close(); // Closes the transaction
            new TMessage('info', 'Informações salvas com Sucesso!', new TAction(array("SisLevelUserManager", 'onReload')));
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

    public function onEdit($param)
    {

        try {
            if (isset($param['key'])) {

                // get the parameter $key
                $key = $param['key'];

                // open a transaction with database 'permission'
                TTransaction::open('sistema');

                // instantiates object System_user
                $object = new SisGeneralField($key);

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
