<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Registry\TSession;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Util\TImage;

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

class User
{
    public $id;
    public $nome;
    public $fone1;
}

class SistemaSendWhatsappWindow extends TPage
{
    private $form;
    protected $dataSents;

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
        $this->form->setFieldSizes('100%');
        $this->form->generateAria(); // automatic aria-label

        $html = new THtmlRenderer('app/resources/sis_send_whatsapp_script.html');
        $progressBar = new THtmlRenderer('app/resources/sistema_progress_bar_bootstrap.html');
        $bootstrapModal = new THtmlRenderer('app/resources/sistema_modal_bootstrap.html');

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
        $link        = new TEntry('link');
        $group_id    = new TCombo('group_id');
        $session_key        = new TEntry('session_key');
        $session_name        = new TEntry('session_name');
        $type        = new TCombo('type');
        $typeContent        = new TCombo('type_content');
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

        $typeContent->addItems([
            'text' => 'Normal',
            'image' => 'Imagem',
            'link' => 'Link'
        ]);

        $typeContent->setValue("text");

        $type->addItems([
            'a' => 'Individual',
            'b' => 'Gupos',
            'c' => 'Digitar Contato'
        ]);

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


        $row = $this->form->addFields(
            [],
            [new TLabel('Tipo de Conteúdo'), $typeContent],
            [new TLabel('Sessão'), $session_name],
            [new TLabel('Chave da Sessão'), $session_key]
        );

        $row->layout = ['col-sm-2 control-label', 'col-sm-4', 'col-sm-3', 'col-sm-3'];

        // $row = $this->form->addFields(
        //     [new TLabel('Grupo')],
        //     [$group_id]
        // );

        // $row->layout = ['col-sm-2 control-label', 'col-sm-3'];


        // $this->form->addFields(
        //     [new TLabel('Fone (Whatsapp')],
        //     [$number]
        // );

        // $row->layout = ['col-sm-2 control-label', 'col-sm-3'];

        // default Type value
        $type->setValue('a');

        // fire change event

        $lideranca->placeholder = 'Description placeholder';
        $lideranca->setTip('Tip for description');

        $colletion->placeholder = 'Description placeholder';
        $colletion->setTip('Tip for description');

        $label = new TLabel('Mensagem', '#7D78B6', 12, 'bi');
        $label->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';
        $this->form->addContent([$label]);

        $this->form->addFields([new TLabel('Texto')], [$text]);
        $text->setSize('100%', 80);
        $text->id = 'text_to_send';


        $session_key->id = 'session_key';
        $session_name->id = 'session_name';

        $this->form->addFields([new TLabel('Link')], [$link]);
        $text->setSize('100%', 80);
        $link->id = 'link_to_send';

        // define the form action 
        $this->form->addAction('Enviar', new TAction(array($this, 'onSend')), 'far:check-circle green');
        // $this->form->addHeaderAction('Enviar', new TAction(array($this, 'onSend')), 'fa:rocket orange');

        // extra dropdown.
        $dropdown = new TDropDown('Opções', 'fa:th blue');
        $dropdown->addPostAction('PostAction', new TAction(array($this, 'onSend')), $this->form->getName(), 'far:check-circle');
        $dropdown->addAction('Shortcut to customers', new TAction(array('ListaColaboradoresDataGridView', 'onReload')), 'fa:link');
        // $this->form->addFooterWidget($dropdown);
        // $this->form->addHeaderWidget($dropdown);

        // wrap the page content using vertical box

        $listToSend = $this->getListToSend();

        // echo "<pre>";
        // var_dump($listToSend);
        // echo "</pre>";

        $jsonText = addslashes(json_encode($listToSend));

        $html->enableSection('main', [
            'SecretKey' => '12345',
            'session' => 'session77999',
            'sessionkey' => 'session77999',
            'listToSend' => $jsonText
        ]);


        $info = [
            'title' => 'Modal',
            'header' => '',
            'sub_title' => "",
            'content' => 'Aqui vai o conteúdo'
        ];


        $replace = array();
        $replace['contacts'] = $listToSend;

        $progressBar->enableSection('main', $replace);
        $bootstrapModal->enableSection('main', $info);


        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        // $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($html);
        $vbox->add($this->form);
        $vbox->add($progressBar);
        $vbox->add($bootstrapModal);
        parent::add($vbox);
    }

    /**
     * Simulates an save button
     * Show the form content
     */
    public static function onSend($param)
    {


        $data = (object) $param;

        if(!$data->text || empty($data->text)){


            new TMessage('error', "Escreva um texto...");
             return;
        }

        // new TMessage('info', "Ok texto...". json_encode($data));

        // new TMessage('info', 'Você vai enviar para x números!');
        // return false;

        // $data = $this->form->getData();

        // $this->form->setData($data);
        // new TMessage('info', "Enviado com sucesso!");
    }



    public static function getListToSend()
    {
        try {

            TTransaction::open('unit_a'); // open a transaction with database
            // query criteria
            $criteria = new TCriteria;
            // $criteria->add(new TFilter('gender', '=', 'F'));
            // $criteria->add(new TFilter('status', '=', 'M'));

            // load using repository
            $repository = new TRepository('SisPessoas');
            $customers = $repository->load($criteria);

            // if (isset($_GET["teste"]) && !empty($_GET["teste"])) {

            //     $user1 = new User();
            //     $user1->nome = 'ALAN SILVA';
            //     $user1->fone1 = '91983763092';

            //     $user2 = new User();
            //     $user2->nome = 'DIONE SILVA';
            //     $user2->fone1 = '9189234848';

            //     $userGiz = new User();
            //     $userGiz->nome = 'ASCOM Francy';
            //     $userGiz->fone1 = '9191785522';

            //     $user31 = new User();
            //     $user31->nome = 'SEM-DDD-SEM-9';
            //     $user31->fone1 = '89234848';

            //     $user3 = new User();
            //     $user3->nome = 'SEM-DDD-COM-9';
            //     $user3->fone1 = '983763092';

            //     $user4 = new User();
            //     $user4->nome = 'COM-DDD-SEM-9';
            //     $user4->fone1 = '9183763092';

            //     $user5 = new User();
            //     $user5->nome = 'COM-DDD-COM-9';
            //     $user5->fone1 = '91989234848';

            //     $user6 = new User();
            //     $user6->nome = 'FIXO-SEM-DDD';
            //     $user6->fone1 = '32124567';

            //     $user7 = new User();
            //     $user7->nome = 'FIXO-COM-DDD';
            //     $user7->fone1 = '9132124567';


            //     $customers = array($user1, $user2, $user31, $user3, $user4, $user5, $user6, $user7);
            //     // return $listTest;
            // }


            if (isset($_GET["teste"]) && !empty($_GET["teste"])) {

                $user1 = new User();
                $user1->nome = 'ALAN SILVA';
                $user1->fone1 = '91983763092';

                $user11 = new User();
                $user1->nome = 'ALAN SILVA';
                $user1->fone1 = '9183763092';

                // $user2 = new User();
                // $user2->nome = 'DIONE SILVA';
                // $user2->fone1 = '9189234848';

                // $userGiz = new User();
                // $userGiz->nome = 'ASCOM Francy';
                // $userGiz->fone1 = '9191785522';

                // $user31 = new User();
                // $user31->nome = 'SEM-DDD-SEM-9';
                // $user31->fone1 = '89234848';

                // $user3 = new User();
                // $user3->nome = 'SEM-DDD-COM-9';
                // $user3->fone1 = '983763092';

                // $user4 = new User();
                // $user4->nome = 'COM-DDD-SEM-9';
                // $user4->fone1 = '9183763092';

                // $user5 = new User();
                // $user5->nome = 'COM-DDD-COM-9';
                // $user5->fone1 = '91989234848';

                // $user6 = new User();
                // $user6->nome = 'FIXO-SEM-DDD';
                // $user6->fone1 = '32124567';

                // $user7 = new User();
                // $user7->nome = 'FIXO-COM-DDD';
                // $user7->fone1 = '9132124567';


                $customers = array($user1);
                // return $listTest;
            }

            $listToSender = [];
            $ID = 1;
            foreach ($customers as $customer) {
                // echo $customer->id . ' - ' . $customer->name . '<br>';
                if ($customer->fone1) {


                    $phoneNumber = preg_replace('/[\/]+/', '|', $customer->fone1);


                    $nome = "";

                    if ($customer->nome) {

                        $titulos = ["pastor", "pr.", 'pr', "pra.", 'pra', "pastora", "vereador", "ver", "ver.", "prof.", "prof", "professora", "professor", "sgt", "senhor", "sra.", "sra", "sr.", "sr"];

                        $nome = explode(" ", $customer->nome);

                        if (in_array(strtolower($nome[0]), $titulos)) {
                            $nome = "{$nome[0]} {$nome[1]}";
                        } else {
                            $nome = $nome[0];
                        }
                    }

                    if (strpos($phoneNumber, '|') !== false) {
                        $phoneNumber = (explode("|", $phoneNumber))[0];
                    }

                    $phoneNumber = self::checkAndFixPhoneNumber($phoneNumber);

                    if ($phoneNumber) {

                        $listToSender[] = [
                            "id" => $ID,
                            "telefone" => $phoneNumber,
                            "nome" => $nome
                        ];
                    }
                }

                $ID++;
            }
            TTransaction::close(); // close the transaction


            return $listToSender;
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }



    public function onLoad($param)
    {


        // $listToSend = TSession::getValue('filter_list_to_send');

        // echo "<pre> arquivos recebidos no load...";
        // var_dump($listToSend);
        // echo "</pre>";

        // new TMessage('info', json_encode($param) );

        //   if (!isset($result)) 
        //         $result = new stdClass();

        //   $result->id_chamado = $param['id_chamado'];
        //   $result->titulo = $param['titulo'];
        //   $result->id_situacao = $param['id_situacao'];

        //   TForm::sendData('form_Acompanhamento', $result);

    }



    public function searchWord($str, $word)
    {

        // $str = 'Vamos testar o strpos';
        if (strpos($str, 'testar') !== false) {
            echo "Existe {$word} na string";
        }
    }




    public static function checkAndFixPhoneNumber($number)
    {
        /**
         * FORMATO DO NUMERO DE TELEFONE
         * SEM DDD E SEM 9 - 8 DIGITOS
         * SEM DDD E COM 9 - 9 DIGITOS
         * COM DDD E SEM 9 - 10 DIGITOS
         * COM DDD E COM 9 - 11 DIGITOS
         * NUMERO CELULAR: 91987654321
         * NUMERO FIXO: 9132124567
         * sem DDD - 8 DIGITOS
         * COM DDD - 10 DIGITOS
         * 
         */

        $phone_number = trim($number);

        $ddd_cliente = "91";

        $phone_number = preg_replace("/[^0-9]/", "", $phone_number);

        $numero_cliente = $phone_number;

        if (strlen($numero_cliente) < 8) return false;

        if (strlen($numero_cliente) == 8) return $ddd_cliente . $numero_cliente;


        //pega o DDD ---- 91983763092 --- 9183763092
        if (strlen($phone_number) >= 10) {

            $ddd_cliente = preg_replace('/\A.{2}?\K[\d]+/', '', $phone_number);

            $numero_cliente = preg_replace('/^\d{2}/', '', $phone_number);
        }


        // remove o digito verificador (9)
        if (strlen($numero_cliente) == 9) $numero_cliente = preg_replace('/^\d{1}/', '', $numero_cliente);


        /**  OBS: PARA O SISPARO DO WHATSAP, NAO DEVE SE ESTAR COM O 9 */
        //quando está sem 9

        // if (strlen($numero_cliente) < 9) {

        //     $numero_cliente = "9" . $numero_cliente;
        // }

        //COM-DDD-SEM-9
        // if (strlen($numero_cliente) <= 8) {

        //     $numero_cliente = $numero_cliente;
        // }

        $number = $ddd_cliente . $numero_cliente;

        return $number;
    }
}
