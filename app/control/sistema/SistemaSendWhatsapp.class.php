<?php

use Adianti\Control\TAction;
use Adianti\Widget\Form\TCombo;

/**
 * FormBuilderView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SistemaSendWhatsapp extends TPage
{
    private $form;

    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();

        // create the form
        $this->form = new BootstrapFormBuilder('form_show_hide');
        $this->form->setFormTitle("Envio de Whatsapp");
        $this->form->generateAria(); // automatic aria-label

        // create the form fields
        $id          = new TEntry('id');
        $lideranca = new TEntry('description');
        $colletion = new TEntry('collection');
        $password    = new TPassword('password');
        $created     = new TDateTime('created');
        $expires     = new TDate('expires');
        $number       = new TEntry('number');
        $color       = new TColor('color');
        $text        = new TText('text');
        $group_id    = new TCombo('group_id');
        $type        = new TCombo('type');
        // $type_send   = new TCombo('type_send');

        $lideranca->setInnerIcon(new TImage('fa:user blue'), 'left');
        $colletion->setInnerIcon(new TImage('fa:user blue'), 'left');
        //$color->setOption('components', ['opacity' => false]);

        $id->setEditable(FALSE);
        $created->setMask('dd/mm/yyyy hh:ii');
        $expires->setMask('dd/mm/yyyy');
        $created->setDatabaseMask('yyyy-mm-dd hh:ii');
        $expires->setDatabaseMask('yyyy-mm-dd');
        $number->setSize('100%');
        $color->setSize('100%');
        $created->setSize('100%');
        $expires->setSize('100%');
        $group_id->setSize('100%');

        $type->setSize('100%');
        $type->setChangeAction(new TAction(array($this, 'onChangeType')));

        $type->addItems([
            'a' => 'Individual',
            'b' => 'Gupos',
            'c' => 'Digitar Contato'
        ]);

        // $type_send->addItems([
        //     'a' => 'Imediato', 
        //     'b' => 'Programado'
        // ]);

        $group_id->addItems([
            '1' => 'Lideranças',
            '2' => 'Região'
        ]);

        // disable dates (bootstrap date picker)
        $expires->setOption('datesDisabled', ["23/02/2019", "24/02/2019", "25/02/2019"]);

        $created->setValue(date('Y-m-d H:i'));
        $expires->setValue(date('Y-m-d', strtotime("+1 days")));
        $group_id->setValue(30);
        $color->setValue('#FF0000');
        $type->setDefaultOption("Selecione");
 
        // add the fields inside the form
        $this->form->addFields([new TLabel('Id')],          [$id]);

        $row = $this->form->addFields(
            [new TLabel('Tipo')],
            [$type]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-3'];

        $row = $this->form->addFields(
            [new TLabel('Grupo')],
            [$group_id]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-3'];
        
        
        $this->form->addFields(
            [new TLabel('Fone (Whatsapp')],
            [$number]
        );
        
        $row->layout = ['col-sm-2 control-label', 'col-sm-3'];

        // default value
        $type->setValue('a');

        // fire change event
        self::onChangeType(['type' => 'a']);

        $this->form->addFields([new TLabel('Lideranças')], [$lideranca]);
        $this->form->addFields([new TLabel('Região')], [$colletion]);
        // $this->form->addFields([new TLabel('Password')],    [$password]);
        // $this->form->addFields(
        //     [new TLabel('Created at')],
        //     [$created],
        //     [new TLabel('Expires at')],
        //     [$expires]
        // );

        $lideranca->placeholder = 'Description placeholder';
        $lideranca->setTip('Tip for description');

        $colletion->placeholder = 'Description placeholder';
        $colletion->setTip('Tip for description');

        $label = new TLabel('Mensagem', '#7D78B6', 12, 'bi');
        $label->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent([$label]);

        $this->form->addFields([new TLabel('Texto')], [$text]);
        $text->setSize('100%', 80);

        // define the form action 
        $this->form->addAction('Enviar', new TAction(array($this, 'onSend')), 'far:check-circle green');
        $this->form->addHeaderAction('Enviar', new TAction(array($this, 'onSend')), 'fa:rocket orange');

        // extra dropdown.
        $dropdown = new TDropDown('Opções', 'fa:th blue');
        $dropdown->addPostAction('PostAction', new TAction(array($this, 'onSend')), $this->form->getName(), 'far:check-circle');
        $dropdown->addAction('Shortcut to customers', new TAction(array('ListaColaboradoresDataGridView', 'onReload')), 'fa:link');
        // $this->form->addFooterWidget($dropdown);
        // $this->form->addHeaderWidget($dropdown);

        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        parent::add($vbox);
    }

    /**
     * Simulates an save button
     * Show the form content
     */
    public function onSend($param)
    {
        $data = $this->form->getData();

        // put the data back to the form
        $this->form->setData($data);



        // new TMessage('info', json_encode($data->text) );
        //     return false;


        if (!$data->text) {

            new TMessage('error', 'Por favor, digie uma mensagem!');
            return false;
        }
        // creates a string with the form element's values
        $message = 'Id: '           . $data->id . '<br>';
        $message .= 'Description : ' . $data->description . '<br>';
        // $message .= 'Password : '    . $data->password . '<br>';
        $message .= 'Created: '      . $data->created . '<br>';
        $message .= 'Expires: '      . $data->expires . '<br>';
        $message .= 'Value : '       . $data->number . '<br>';
        // $message .= 'Color : '       . $data->color . '<br>';
        // $message .= 'Weight : '      . $data->weight . '<br>';
        $message .= 'Type : '        . $data->type . '<br>';
        $message .= 'Text : '        . $data->text . '<br>';

        $text_send = json_encode($data->text);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://66.70.188.94:3401/sendText',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "session": "sessao779",
    "number" : '.$data->number.',
    "text" : '.$text_send.'
}',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'apitoken: 12345',
                'sessionkey: chave779'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;


        TTransaction::open('sistema'); // open transaction
        $object = new SisListaEnvios;


        $object->fromArray((array) $data);
        $object->store(); // store the object

        // new TMessage('info', 'Informações salvas com Sucesso!', new TAction(array('ListaPessoasDataGridView', 'onReload')));
        TTransaction::close(); // Closes the transaction

        // show the message 
        new TMessage('info', $message);
    }

    /**
     * Event executed when type is changed
     * a = individual | b = grupos | c = digitar contato
     */
    public static function onChangeType($param)
    {
        if ($param['type'] == 'b') {
            TQuickForm::showField('form_show_hide', 'group_id');
            // TQuickForm::showField('form_show_hide', 'units');
            // TQuickForm::hideField('form_show_hide', 'number');
            // TQuickForm::hideField('form_show_hide', 'hours');
        }
        elseif ($param['type'] == 'c') {
            TQuickForm::showField('form_show_hide', 'number');
            // TQuickForm::showField('form_show_hide', 'units');
            // TQuickForm::hideField('form_show_hide', 'number');
            // TQuickForm::hideField('form_show_hide', 'hours');
        }
         else {
            TQuickForm::hideField('form_show_hide', 'group_id');
            TQuickForm::hideField('form_show_hide', 'number');
            // TQuickForm::hideField('form_show_hide', 'units');
            // TQuickForm::showField('form_show_hide', 'hour_price');
            // TQuickForm::showField('form_show_hide', 'hours');
        }
    }
}
