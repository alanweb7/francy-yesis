<?php

use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Wrapper\TDBCombo;

/**
 * SystemUserList Listing
 * @author  <your name here>
 */
class ListaAtendimentosSaudeDataGridView extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $listPessoasAtendimentos;

    use Adianti\base\AdiantiStandardListTrait;

    /**
     * Page constructor 
     */
    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('sistema');            // defines the database
        // $this->setActiveRecord('SisPessoas');   // defines the active record
        $this->setActiveRecord('SisAtendimentoSaude');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        $this->setLimit(30);
        // $this->setCriteria($criteria) // define a standard filter


        $criteria_default = new TCriteria;


        if (TSession::getValue("userid") != 1) {

            $criteria_default->add(new TFilter('unit_id', '=', 3));
        }


        // $criteria_default->add(new TFilter('segmento', '=', 2));
        // $criteria_default->add(new TFilter('segmento', '=', 6)); //liderancas
        $this->setCriteria($criteria_default); // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('nome', 'LIKE', 'nome'); // filterField, operator, formField
        $this->addFilterField('indicacao', 'LIKE', 'indicacao'); // filterField, operator, formField
        $this->addFilterField('cidade', 'LIKE', 'cidade'); // filterField, operator, formField
        $this->addFilterField('bairro', 'LIKE', 'bairro'); // filterField, operator, formField
        $this->addFilterField('user_id', '=', 'user_id'); // filterField, operator, formField

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_SystemUser');
        $this->form->setFormTitle('Buscar Atendimento');
        $this->form->setFieldSizes('100%');
        $this->form->generateAria(); // automatic aria-label

        // $expander = new TExpander('<i class="fas fa-search"></i> Buscar Cadastro');
        // $expander->setButtonProperty('class', 'btn btn-primary btn-sm');
        // $expander->add($this->form);


        // create the form fields
        $id = new TEntry('id');
        // $nome = new TEntry('nome');
        $nome = new TEntry('nome');
        $indicacao = new TEntry('indicacao');
        $cidade = new TEntry('cidade');
        $bairro = new TEntry('bairro');
        $contato = new TCombo('fone1');



        $row = $this->form->addFields(
            [new TLabel('Nome'),    $nome],
            [new TLabel('Indicação'),    $indicacao],
            [new TLabel('Cidade'),    $cidade],
            [new TLabel('Bairro'),    $bairro]
        );

        $row->layout = ['col-sm-6', 'col-sm-6', 'col-sm-6', 'col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        $btnClear = $this->form->addAction('Todos', new TAction([$this, 'onClear']), 'fa:search');
        $btnClear->class = 'btn btn-sm btn-danger';

        // $btnTeste = $this->form->addAction('Teste', new TAction([$this, 'onTeste']), 'fa:search');
        // $btnTeste->class = 'btn btn-sm btn-danger';


        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');


        // creates the datagrid columns
        // $this->datagrid->generateHiddenFields();

        $colums = [
            'id' => new TDataGridColumn('id', 'Id', 'right', '10%'),
            'nome' => new TDataGridColumn('nome', 'Nome', 'left', '40%'),
            // 'pessoa_id' => new TDataGridColumn('user_id', 'Atendente', 'left'),
            'telefone' => new TDataGridColumn('fone1', 'Contato', 'left', '15%'),
            'bairro' => new TDataGridColumn('bairro', 'Bairro', 'center'),
            'situacao' => new TDataGridColumn('situacao', 'Status', 'center')
        ];



        // add the columns to the DataGrid
        foreach ($colums as $key => $colum) {

            $this->datagrid->addColumn($colum);

            if ($key === 'pessoa_id') {
                $colum->setAction(new TAction([$this, 'onReload']), ['order' => 'pessoa_id']);
            }
            if ($key === 'cidade') {
                $colum->setAction(new TAction([$this, 'onReload']), ['order' => 'cidade']);
            }

            if ($key === 'situacao') {


                // define the transformer method over image
                $colum->setTransformer(function ($value, $object, $row) {

                    if (!$value) return;
                    $label = "danger";

                    if ($value === "resolvido") {

                        $label = "success";
                    }

                    $texto = strtoupper($object->situacao);
                    $div = new TElement('span');
                    $div->class = "label label-{$label}";
                    $div->style = "text-shadow:none; font-size:12px";
                    $div->add($texto);
                    return $div;
                });
            }




            // if ($key === 'fone1') {
            //     $colum->setTransformer(function ($value) {
            //         if ($value) {
            //             $value = $this->checkAndFixPhoneNumber($value);
            //             $value = str_replace([' ', '-', '(', ')'], ['', '', '', ''], $value);
            //             $icon  = "<i class='fab fa-whatsapp' aria-hidden='true'></i>";
            //             return "{$icon} <a target='newwindow' href='https://api.whatsapp.com/send?phone=55{$value}&text=Olá'> {$value} </a>";
            //         }
            //         return $value;
            //     });
            // }

            if ($key === 'created_date') {
                $colum->setTransformer([$this, "formatDate"]);
                $colum->setAction(new TAction([$this, 'onReload']), ['order' => 'created_date']);
            }

            if ($key === 'telefone') {
                $colum->setTransformer([$this, "checkAndFixPhoneNumber"]);
            }
        }


        $action1 = new TDataGridAction(['CadastroSisAtendimentoSaudeForm', 'onEdit'], ['id' => '{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id' => '{id}']);

        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2, _t('Delete'), 'far:trash-alt red');

        // create the datagrid model 
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));


        $label = new TLabel('ATENDIMENTOS', '#7D78B6', 12, 'bi');
        $label->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';

        $panel = new TPanelGroup('', 'white');

        $panel->add($label);
        $panel->add($this->datagrid);
        // $panel->addHeaderActionLink('ENVIAR WHATSAPP', new TAction([$this, 'sendMessageWhatsapp'], ['register_state' => 'false']), 'far:file-pdf red');
        $panel->addFooter($this->pageNavigation);

        // header actions
        $newRegister = new TActionLink('Novo Atendimento', new TAction(array('CadastroSisAtendimentoSaudeForm', 'onLoad'), ['type_register' => 6]), 'green', 10, null, 'fa:plus-circle');
        $newRegister->class = 'btn btn-default';
        $newRegister->style .= ';margin-top: 4px';

        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        // $dropdown->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static' => '1']), 'fa:table blue');
        $dropdown->addAction(_t('Save as PDF'), new TAction([$this, 'exportAsPDF'], ['register_state' => 'false', 'static' => '1']), 'far:file-pdf red');

        // inserir no formulario

        // $panel->addHeaderWidget( $expander);
        // $panel->addHeaderWidget( $newRegister);
        // $panel->addHeaderWidget( $dropdown );
        $this->form->addHeaderWidget($newRegister);
        $this->form->addHeaderWidget($dropdown);


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
    }

    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead

        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }

    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try {
            $key = $param['key']; // get the parameter $key
            TTransaction::open('unit_a'); // open a transaction with database
            $object = new SisAtendimentoSaude($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction

            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function getPessoasAtendidas()
    {

        try {


            TTransaction::open('sistema');

            $criteria = new TCriteria;
            // $criteria->add( new TFilter( 'id', 'IN', $listIds ));
            $data = SisAtendimentos::getObjects($criteria);
            $listIds = [];
            if ($data) {
                foreach ($data as $registro) {
                    array_push($listIds, $registro->pessoa_id);
                }
            }

            $criteria = new TCriteria;
            $criteria->add(new TFilter('id', 'IN', $listIds));

            $objects = SisPessoas::getObjects($criteria);

            $list = [];
            foreach ($objects as $key => $pessoa) {
                $list[$pessoa->id] = $pessoa->nome;
            }
            TTransaction::close();

            return $list;
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function transformar($param)
    {
        // echo "onclear";
        // TTransaction::open('unit_a'); // open a transaction with database
        // $object = new SisPessoas($param, FALSE); // instantiates the Active Record
        // TTransaction::close(); // close the transaction

        return $param;
    }

    public function onClear($param)
    {
        // echo "onclear";
        $fields = $this->form->getFields();
        foreach ($fields as $field) {
            TSession::setValue($this->activeRecord . '_filter_' . $field->getName(), NULL);
            TSession::setValue($this->activeRecord . '_filter_data', NULL);
        }
        $this->form->clear();
        $this->onReload();
    }

    public static function onResponse($param)
    {

        echo "<pre>";
        var_dump($param);
        echo "</pre>";

        $action = NULL;
        // $action = new TAction(array('ListaPessoasGroupView', 'onReload'));
        new TMessage('info', 'Registro Salvo com sucesso!', $action);
    }

    /**
     * Export datagrid as PDF
     */
    public function exportAsPDF($param)
    {
        try {

            // string with HTML contents
            $html = clone $this->datagrid;
            $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();

            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $file = 'app/output/datagrid-export.pdf';

            // write and open file
            file_put_contents($file, $dompdf->output());

            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file;
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $object->add('O navegador não suporta a exibição deste conteúdo, <a style="color:#007bff;" target=_newwindow href="' . $object->data . '"> clique aqui para baixar</a>...');

            $window->add($object);
            $window->show();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }



    public function formatDate($date, $object, $row)
    {

        // var_dump($date);
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
    }

    public function checkAndFixPhoneNumber($number, $object, $row)
    {


        if (!$number) return;
        $phone_number = trim($number);

        $ddd_cliente = "91";

        $phone_number = preg_replace("/[^0-9]/", "", $phone_number);

        $numero_cliente = $phone_number;

        //pega o DDD
        if (strlen($phone_number) >= 10) {

            $ddd_cliente = preg_replace('/\A.{2}?\K[\d]+/', '', $phone_number);

            // remove o digito verificador (9)
            $numero_cliente = preg_replace('/^\d{2}/', '', $phone_number);
        }


        //quando está sem 9
        if (strlen($numero_cliente) == 8) $numero_cliente = "9" . $numero_cliente;

        $number = $ddd_cliente . $numero_cliente;


        $icon  = "<i class='fab fa-whatsapp' aria-hidden='true'></i>";
        return "{$icon} <a target='newwindow' href='https://api.whatsapp.com/send?phone=55{$number}&text=Olá'> {$number} </a>";

        // return $number;

    }


    public function sendMessageWhatsapp($param)
    {
        try {

            $data = $this->datagrid->getItems();

            if ($data) {
                foreach ($data as $key => $item) {
                    $listSent[$key]['id'] = $item->id;
                    $listSent[$key]['nome'] = $item->nome;

                    // $str = '(31)9915-2855';
                    // $str = trim($item->telefone);
                    // $novo = substr_replace($str, '9', 5, 0);

                    $phone_number = trim($item->fone1);

                    $phone_number = preg_replace("/[^0-9]/", "", $phone_number);

                    $numero_cliente = $phone_number;

                    if (strlen($phone_number) >= 10) $ddd_cliente = preg_replace('/\A.{2}?\K[\d]+/', '', $phone_number);
                    if (strlen($phone_number) >= 10) $numero_cliente = preg_replace('/^\d{2}/', '', $phone_number);
                    if (strlen($numero_cliente) == 9) $numero_cliente = preg_replace('/^\d{1}/', '', $numero_cliente);

                    // $listSent[$key]['telefone'] = $ddd_cliente.$numero_cliente;
                    $listSent[$key]['telefone'] = $ddd_cliente . $numero_cliente;
                }
            }

            TSession::setValue('filter_list_to_send', $listSent);
            // new TMessage('info', 'Informações salvas com Sucesso!', new TAction(array('SistemaSendWhatsappWindow', 'onLoad')));
            AdiantiCoreApplication::loadPage('SistemaSendWhatsappWindow', 'onLoad');
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }


    /**
     * Executed when the user clicks at the view button
     */
    public static function onView($param)
    {
        // get the parameter and shows the message
        $code = $param['code'];
        $name = $param['name'];
        new TMessage('info', "The code is: <b>$code</b> <br> The name is : <b>$name</b>");
    }
}
