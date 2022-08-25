<?php

use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Wrapper\TDBCombo;

/**
 * SystemUserList Listing
 * @author  <your name here>
 */
class ListaUpdateGeolocation extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;

    use Adianti\base\AdiantiStandardListTrait;

    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setDatabase('unit_a');            // defines the database
        $this->setActiveRecord('SisPessoas');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        $this->setLimit(30);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('nome', 'like', 'nome'); // filterField, operator, formField
        $this->addFilterField('bairro', 'like', 'bairro'); // filterField, operator, formField
        $this->addFilterField('cidade', 'like', 'cidade'); // filterField, operator, formField
        $this->addFilterField('indicacao', 'like', 'indicacao'); // filterField, operator, formField

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_SystemUser');
        $this->form->setFormTitle('Buscar Cadastro');
        $this->form->setFieldSizes('100%');
        $this->form->generateAria(); // automatic aria-label

        // $expander = new TExpander('<i class="fas fa-search"></i> Buscar Cadastro');
        // $expander->setButtonProperty('class', 'btn btn-primary btn-sm');
        // $expander->add($this->form);


        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $endereco = new TEntry('endereco');
        $multiplicador = new TEntry('multiplicador');
        $bairro = new TEntry('bairro');
        $cidade = new TEntry('cidade');

        $criteria_zona = new TCriteria;
        // $criteria_zona->add(new TFilter('zona', 'IS NOT', NULL));
        // $criteria_zona->add(new TFilter('zona', '<>', ''));

        $zona = new TDBCombo('zona', 'sistema', 'SisZonaSecaoPessoas', 'zona', 'zona', NULL,  $criteria_zona);


        $criteria_secao = new TCriteria;
        // $criteria_secao->add(new TFilter('secao', 'IS NOT', NULL));
        // $criteria_secao->add(new TFilter('secao', '<>', ''));
        $secao = new TDBCombo('secao', 'sistema', 'SisZonaSecaoPessoas', 'secao', 'secao', NULL,  $criteria_secao);


        $zona->enableSearch();
        $secao->enableSearch();


        // add the fields
        $row = $this->form->addFields(
            [new TLabel('Nome'),                $nome],
            [new TLabel('Zona'),                $zona],
            [new TLabel('Seção'),               $secao],
            [new TLabel('Multiplicador'),      $multiplicador]
        );
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-2', 'col-sm-4'];

        $row = $this->form->addFields(
            [new TLabel('Endereço'),    $endereco],
            [new TLabel('Bairro'),      $bairro],
            [new TLabel('Cidade'),      $cidade]
        );

        $row->layout = ['col-sm-6', 'col-sm-3', 'col-sm-3'];

        // keep the form filled during navigation with session data
        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        $btnClear = $this->form->addAction('Todos', new TAction([$this, 'onClear']), 'fa:search');
        $btnClear->class = 'btn btn-sm btn-danger';

        $btn = $this->form->addAction('Atualizar Localizadores', new TAction([$this, 'onUpdate']), 'fa:search');
        $btn->class = 'btn btn-sm btn-info';

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');


        // creates the datagrid columns


        $colums = [
            'id' => new TDataGridColumn('id', 'Id', 'right', '10%'),
            'nome' => new TDataGridColumn('nome', 'Nome', 'left', '40%'),
            'telefone' => new TDataGridColumn('telefone', 'Contato', 'left'),
            'bairro' => new TDataGridColumn('bairro', 'Bairro', 'left'),
            'cidade' => new TDataGridColumn('cidade', 'Cidade', 'left'),
            'multiplicador' => new TDataGridColumn('multiplicador', 'Multiplicador', 'left')
        ];



        // add the columns to the DataGrid
        foreach ($colums as $key => $colum) {

            $this->datagrid->addColumn($colum);

            if ($key === 'nome') {
                $colum->setAction(new TAction([$this, 'onReload']), ['order' => 'nome']);
            }
            if ($key === 'telefone') {
                $colum->setTransformer(function ($value) {
                    if ($value) {
                        $value = str_replace([' ', '-', '(', ')'], ['', '', '', ''], $value);
                        $icon  = "<i class='fab fa-whatsapp' aria-hidden='true'></i>";
                        return "{$icon} <a target='newwindow' href='https://api.whatsapp.com/send?phone=55{$value}&text=Olá'> {$value} </a>";
                    }
                    return $value;
                });
            }
        }


        $action1 = new TDataGridAction(['CadastroColaboradoresForm', 'onEdit'], ['id' => '{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id' => '{id}']);

        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2, _t('Delete'), 'far:trash-alt red');

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));


        $label = new TLabel('LISTA DE COLABORADORES', '#7D78B6', 12, 'bi');
        $label->style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';

        $panel = new TPanelGroup('', 'white');

        $panel->add($label);
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);

        // header actions
        $newRegister = new TActionLink('Novo Cadastro', new TAction(array('CadastroColaboradoresForm', 'onLoad')), 'green', 10, null, 'fa:plus-circle');
        $newRegister->class = 'btn btn-default';
        $newRegister->style .= ';margin-top: 4px';



        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static' => '1']), 'fa:table blue');
        $dropdown->addAction(_t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static' => '1']), 'far:file-pdf red');

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
    public static function OnUpdate($param)
    {
        try {

            TTransaction::open('unit_a'); // open a transaction with database

            $criteria = new TCriteria;
            $colaboradores = new TRepository('SisPessoas');
            $colaboradores = $colaboradores->load($criteria);


            foreach ($colaboradores as $key => $colaborador) {




                if ($colaborador->data_cadastro == 0) {

                    $data = new DateTime();

                    $newDate = date_format($data, 'Y-m-d H:i:s');

                    $colaborador->data_cadastro = $newDate;

                    $colaborador->store();
                }
            }

            TTransaction::close(); // close the transaction

            // $pos_action = new TAction([__CLASS__, 'onReload']);
            // new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
            $number = count($colaboradores);

            // $data = date_create_from_format('Ymd', '20170422');


            new TMessage('info', "Foram encontrados {$number} registros"); // success message


        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }


    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try {
            $key = $param['key']; // get the parameter $key
            TTransaction::open('unit_a'); // open a transaction with database
            $object = new SisPessoas($key, FALSE); // instantiates the Active Record
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

    public function onClear($param)
    {
        $fields = $this->form->getFields();
        foreach ($fields as $field) {
            TSession::setValue($this->activeRecord . '_filter_' . $field->getName(), NULL);
            TSession::setValue($this->activeRecord . '_filter_data', NULL);
        }
        $this->form->clear();
        $this->onReload();
    }

    private function onTeste()
    {
    }
}
