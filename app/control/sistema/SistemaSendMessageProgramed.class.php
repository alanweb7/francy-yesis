<?php


class SistemaSendMessageProgramed
{
    private $form;
    protected $dataSents;

    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
    
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

        $data->type = "a";
        $listToSend = [];
        // if ($data->type == "c") {

        //     $number = preg_replace("/[^0-9]/", "", $data->number);

        //     $listToSend = [
        //         ["id"] => null,
        //         ["nome"] => "ALAN TESTE 2",
        //         ["telefone"] => $number
        //     ];
        // } else {

            $listToSend = TSession::getValue("filter_list_to_send"); //filter_list_to_send

            // var_dump($listToSend);
        // }

        // echo "<pre>";
        // var_dump($listToSend);
        // echo "</pre>";
        // return;

        // new TMessage('info', json_encode($data->text) );
        //     return false;


        if (!$data->text  || empty($data->text)) {

            new TMessage('error', 'Por favor, digie uma mensagem!');
            return false;
        }




        $message = "";
        // creates a string with the form element's values
        // $message = 'Id: '           . $data->id . '<br>';
        // $message .= 'Description : ' . $data->description . '<br>';
        // $message .= 'Password : '    . $data->password . '<br>';
        // $message .= 'Created: '      . $data->created . '<br>';
        // $message .= 'Expires: '      . $data->expires . '<br>';
        // $message .= 'Value : '       . $data->number . '<br>';
        // $message .= 'Color : '       . $data->color . '<br>';
        // $message .= 'Weight : '      . $data->weight . '<br>';
        $message .= 'Type : '        . $data->type . '<br>';
        $message .= 'Text : '        . $data->text . '<br>';

        $text_send = json_encode($data->text);

        foreach ($listToSend as $key => $contact) {


            $name = $contact['nome'];
            $number_send = $contact['telefone'];

            $nameArr = explode(" ", $name);

            $name = $nameArr[0];

            // echo "Enviando para {$name}<br>";

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
              CURLOPT_POSTFIELDS =>'{
                "session": "sessao779",
                "number" : "55'.$number_send.'", 
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

        }
        // TTransaction::open('sistema'); // open transaction
        // $object = new SisListaEnvios;


        // $object->fromArray((array) $data);
        // $object->store(); // store the object

        // // new TMessage('info', 'Informações salvas com Sucesso!', new TAction(array('ListaPessoasDataGridView', 'onReload')));
        // TTransaction::close(); // Closes the transaction

        // show the message 
        // new TMessage('info', $message);
        $this->form->setData($data);
        new TMessage('info', "Enviado com sucesso!");
    }






    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    public function sendMessage($data)
    {
        try {



            // echo "<pre>";
            // var_dump($listToSend);
            // echo "</pre>";
            // return;

            // new TMessage('info', json_encode($data->text) );
            //     return false;


            if (!$data->text) {

                new TMessage('error', 'Por favor, digie uma mensagem!');
                return false;
            }
            $message = "";
            // creates a string with the form element's values
            // $message = 'Id: '           . $data->id . '<br>';
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
                                            "number" : 55' . trim($data->number) . ',
                                            "text" : ' . $text_send . '
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


            // TTransaction::open('sistema'); // open transaction
            // $object = new SisListaEnvios;


            // $object->fromArray((array) $data);
            // $object->store(); // store the object

            // // new TMessage('info', 'Informações salvas com Sucesso!', new TAction(array('ListaPessoasDataGridView', 'onReload')));
            // TTransaction::close(); // Closes the transaction

            // show the message 
            // new TMessage('info', $message);
            $this->form->setData($data);
            new TMessage('info', "Enviado com sucesso!");
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }


    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    public function onEdit($param)
    {
        try {

            // echo "<pre>";
            // var_dump($param);
            // echo "</pre>";


            if (isset($param['key'])) {
                // get the parameter $key
                $key = $param['key'];
                // echo "keuy" . $key;
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onLoad($param)
    {


        $listToSend = TSession::getValue('filter_list_to_send');

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
        } elseif ($param['type'] == 'c') {
            TQuickForm::showField('form_show_hide', 'number');
            // TQuickForm::showField('form_show_hide', 'units');
            // TQuickForm::hideField('form_show_hide', 'number');
            // TQuickForm::hideField('form_show_hide', 'hours');
        } else {
            TQuickForm::hideField('form_show_hide', 'group_id');
            TQuickForm::hideField('form_show_hide', 'number');
            // TQuickForm::hideField('form_show_hide', 'units');
            // TQuickForm::showField('form_show_hide', 'hour_price');
            // TQuickForm::showField('form_show_hide', 'hours');
        }
    }


    public function searchWord($str, $word)
    {

        // $str = 'Vamos testar o strpos';
        if (strpos($str, 'testar') !== false) {
            echo "Existe {$word} na string";
        }
    }

    public function testeClass()
    {

        // $str = 'Vamos testar o strpos';

        $DateAndTime = date('m-d-Y h:i:s a', time());  
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
          CURLOPT_POSTFIELDS =>'{
            "session": "sessao779",
            "number" : "5591983763092", 
            "text" : "Testando envio programado ás '.$DateAndTime.'"
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

    }



}
