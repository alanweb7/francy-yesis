<?php

use Adianti\Control\TPage;

class SisLevelUserManager extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'sistema';
    private static $activeRecord = 'SisGeneralField';
    private static $primaryKey = 'id';
    private static $formName = 'SisLevelUserForm';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Níveis de Usuários");
        $this->limit = 20;

        $id = new TEntry('id');
        $code = new TEntry('code');
        $name = new TEntry('name');
        $category_id = new TEntry('level');


        $code->setMaxLength(50);
        $name->setMaxLength(255);
        $category_id->setMaxLength(11);

        $id->setSize(100);
        $code->setSize('100%');
        $name->setSize('100%');
        $category_id->setSize('100%');


        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'), $id], [new TLabel("Código:", null, '14px', null, '100%'), $code]);
        $row1->layout = ['col-sm-6', 'col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Nome:", null, '14px', null, '100%'), $name], [new TLabel("Categoria:", null, '14px', null, '100%'), $category_id]);
        $row2->layout = ['col-sm-6', 'col-sm-6'];


        // keep the form filled during navigation with session data
        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        $startHidden = true;

        if (TSession::getValue('ProdutosList_expand_start_hidden') === false) {
            $startHidden = false;
        } elseif (TSession::getValue('ProdutosList_expand_start_hidden') === true) {
            $startHidden = true;
        }
        $expandButton = $this->form->addExpandButton("Expandir", 'fas:expand #000000', $startHidden);
        $expandButton->addStyleClass('btn-default');
        $expandButton->setAction(new TAction([$this, 'onExpandForm'], ['static' => 1]), "Expandir");
        $this->form->addField($expandButton);

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary');

        $btn_produtosform = $this->form->addAction("NOVO NÍVEL", new TAction(["SisLevelUserForm", 'onLoad']), 'fas:certificate #000000');
        $this->btn_produtosform = $btn_produtosform;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();

        $this->datagrid_form = new TForm('datagrid_' . self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "Id", 'center', '70px');
        $column_name = new TDataGridColumn('name', "Nome", 'left'); 
        $column_code = new TDataGridColumn('meta_value', "Nível", 'left');
        $column_level_id = new TDataGridColumn('meta_value', "Permissões", 'left');


        $column_level_id->setTransformer(function ($value, $object, $row) {
            //code here
            // $value = '<img src="'.$value.'" width="40" heidht="auto" />';

            $levelOptions = [
                1 => 'VISUALIZAR',
                2 => 'EDITAR',
                3 => 'CRIAR / EDITAR',
                4 => 'CRIAR / EDITAR / EXCLUIR'
            ];
            $indice = (int)$value;

            if ($indice) {

                $value = "<span>$levelOptions[$indice]</span>";
            }

            return $value;
        });

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_code);
        $this->datagrid->addColumn($column_level_id);


        $action_onDelete = new TDataGridAction(array($this, 'onDelete'), ['register_state' => 'false']);
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);


        $action_onEdit = new TDataGridAction(array("SisLevelUserForm", 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Excluir");
        $action_onEdit->setImage('fas:edit blue');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);
        $this->datagrid->addAction($action_onDelete);


        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'background-color:#fff; justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $panel->getBody()->insert(0, $headerActions);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction("CSV", new TAction([$this, 'onExportCsv'], ['static' => 1]), 'datagrid_' . self::$formName, 'fas:table #00b894');
        $dropdown_button_exportar->addPostAction("PDF", new TAction([$this, 'onExportPdf'], ['static' => 1]), 'datagrid_' . self::$formName, 'far:file-pdf #e74c3c');
        $dropdown_button_exportar->addPostAction("XML", new TAction([$this, 'onExportXml'], ['static' => 1]), 'datagrid_' . self::$formName, 'far:file-code #95a5a6');

        $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(["Calendario Reuniões", "Produtos"]));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
    }

    public function onDelete($param = null)
    {
        if (isset($param['delete']) && $param['delete'] == 1) {
            try {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);

                // instantiates object
                $object = new SisGeneralField($key, FALSE);

                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload($param);
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            } catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        } else {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
        }
    }
    public function onExportCsv($param = null)
    {
        try {
            $output = 'app/output/' . uniqid() . '.csv';

            if ((!file_exists($output) && is_writable(dirname($output))) or is_writable($output)) {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects) {
                    $handler = fopen($output, 'w');
                    TTransaction::open(self::$database);

                    foreach ($objects as $object) {
                        $row = [];
                        foreach ($this->datagrid->getColumns() as $column) {
                            $column_name = $column->getName();

                            if (isset($object->$column_name)) {
                                $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';
                            } else if (method_exists($object, 'render')) {
                                $column_name = (strpos($column_name, '{') === FALSE) ? ('{' . $column_name . '}') : $column_name;
                                $row[] = $object->render($column_name);
                            }
                        }

                        fputcsv($handler, $row);
                    }

                    fclose($handler);
                    TTransaction::close();
                } else {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            } else {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportPdf($param = null)
    {
        try {
            $output = 'app/output/' . uniqid() . '.pdf';

            if ((!file_exists($output) && is_writable(dirname($output))) or is_writable($output)) {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('object');
                $object->data  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();
            } else {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXml($param = null)
    {
        try {
            $output = 'app/output/' . uniqid() . '.xml';

            if ((!file_exists($output) && is_writable(dirname($output))) or is_writable($output)) {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects) {
                    TTransaction::open(self::$database);

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild($dom->createElement('dataset'));

                    foreach ($objects as $object) {
                        $row = $dataset->appendChild($dom->createElement(self::$activeRecord));

                        foreach ($this->datagrid->getColumns() as $column) {
                            $column_name = $column->getName();
                            $column_name_raw = str_replace(['(', '{', '->', '-', '>', '}', ')', ' '], ['', '', '_', '', '', '', '', '_'], $column_name);

                            if (isset($object->$column_name)) {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                $row->appendChild($dom->createElement($column_name_raw, $value));
                            } else if (method_exists($object, 'render')) {
                                $column_name = (strpos($column_name, '{') === FALSE) ? ('{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                                $row->appendChild($dom->createElement($column_name_raw, $value));
                            }
                        }
                    }

                    $dom->save($output);

                    TTransaction::close();
                } else {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            } else {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    public function ProdutosForm($param = null)
    {
        try {
            //code here



        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__ . '_filter_data', NULL);
        TSession::setValue(__CLASS__ . '_filters', NULL);

        if (isset($data->id) and ((is_scalar($data->id) and $data->id !== '') or (is_array($data->id) and (!empty($data->id))))) {

            $filters[] = new TFilter('id', '=', $data->id); // create the filter 
        }

        if (isset($data->code) and ((is_scalar($data->code) and $data->code !== '') or (is_array($data->code) and (!empty($data->code))))) {

            $filters[] = new TFilter('code', 'like', "%{$data->code}%"); // create the filter 
        }

        if (isset($data->name) and ((is_scalar($data->name) and $data->name !== '') or (is_array($data->name) and (!empty($data->name))))) {

            $filters[] = new TFilter('name', 'like', "%{$data->name}%"); // create the filter 
        }

        if (isset($data->category_id) and ((is_scalar($data->category_id) and $data->category_id !== '') or (is_array($data->category_id) and (!empty($data->category_id))))) {

            $filters[] = new TFilter('category_id', '=', $data->category_id); // create the filter 
        }

        if (isset($data->price) and ((is_scalar($data->price) and $data->price !== '') or (is_array($data->price) and (!empty($data->price))))) {

            $filters[] = new TFilter('price', '=', $data->price); // create the filter 
        }

        if (isset($data->image) and ((is_scalar($data->image) and $data->image !== '') or (is_array($data->image) and (!empty($data->image))))) {

            $filters[] = new TFilter('image', 'like', "%{$data->image}%"); // create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__ . '_filter_data', $data);
        TSession::setValue(__CLASS__ . '_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try {
            // open a transaction with database 'shekinah_pdv'
            TTransaction::open(self::$database);

            // creates a repository for Produtos
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order'])) {
                $param['order'] = 'id';
            }

            if (empty($param['direction'])) {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if ($filters = TSession::getValue(__CLASS__ . '_filters')) {
                foreach ($filters as $filter) {
                    $criteria->add($filter);
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects) {
                // iterate the collection of active records
                foreach ($objects as $object) {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";
                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        } catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public static function onExpandForm($param = null)
    {
        try {
            $startHidden = true;

            if (TSession::getValue('ProdutosList_expand_start_hidden') === false) {
                TSession::setValue('ProdutosList_expand_start_hidden', true);
            } elseif (TSession::getValue('ProdutosList_expand_start_hidden') === true) {
                TSession::setValue('ProdutosList_expand_start_hidden', false);
            } else {
                TSession::setValue('ProdutosList_expand_start_hidden', !$startHidden);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {
    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded and (!isset($_GET['method']) or !(in_array($_GET['method'],  $this->showMethods)))) {
            if (func_num_args() > 0) {
                $this->onReload(func_get_arg(0));
            } else {
                $this->onReload();
            }
        }
        parent::show();
    }
}
