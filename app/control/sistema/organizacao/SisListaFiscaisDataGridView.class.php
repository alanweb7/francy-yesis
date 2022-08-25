<?php

use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Wrapper\TDBCombo;

require 'app/lib/phpexcel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



/**
 * SystemUserList Listing
 * @author  <your name here>
 */
class SisListaFiscaisDataGridView extends TPage
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
        $this->setActiveRecord('SisPessoasFiscais');   // defines the active record
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
        $this->addFilterField('regiao', 'LIKE', 'regiao'); // filterField, operator, formField
        $this->addFilterField('user_id', '=', 'user_id'); // filterField, operator, formField
        $this->addFilterField('zona', '=', 'zona'); // filterField, operator, formField
        $this->addFilterField('secao', '=', 'secao'); // filterField, operator, formField

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
        $cidade = new TDBCombo('cidade', 'sistema', 'SisCidadesVw', 'nome', 'nome', null);

        $row = $this->form->addFields(
            [new TLabel('Nome'),    $nome],
            [new TLabel('Indicação'),    $indicacao],
            [new TLabel('Cidade'),    $cidade]
        );

        $row->layout = ['col-sm-6', 'col-sm-6', 'col-sm-6', 'col-sm-6'];


        /**
         * layout fields
         */

        $cidade->enableSearch('true');

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
            'nome' => new TDataGridColumn('nome', 'Nome', 'left', '30%'),
            'telefone' => new TDataGridColumn('fone1', 'Contato', 'left', '15%'),
            'cidade' => new TDataGridColumn('cidade', 'Cidade', 'left'),
            'zona' => new TDataGridColumn('zona', 'Zona', 'center'),
            'secao' => new TDataGridColumn('secao', 'Seção', 'center'),
            'posicao' => new TDataGridColumn('posicao', 'Posicao', 'center')
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

            if ($key === 'posicao') {


                // define the transformer method over image
                $colum->setTransformer(function ($value, $object, $row) {

                    if (!$value) return;
                    $label = "warning";

                    if ($value === "titular") {

                        $label = "success";
                    }

                    $texto = strtoupper($object->posicao);
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


        $action1 = new TDataGridAction(['SisCadastroFiscaisForm', 'onEdit'], ['id' => '{id}']);
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
        $newRegister = new TActionLink('Novo Cadastro', new TAction(array('SisCadastroFiscaisForm', 'onLoad'), ['type_register' => 7]), 'green', 10, null, 'fa:plus-circle');
        $newRegister->class = 'btn btn-default';
        $newRegister->style .= ';margin-top: 4px';

        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        // $dropdown->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static' => '1']), 'fa:table blue');
        // $dropdown->addAction(_t('Save as PDF'), new TAction([$this, 'exportAsPDF'], ['register_state' => 'false', 'static' => '1']), 'far:file-pdf red');
        $dropdown->addAction('Save as XLS (Modelo)', new TAction([$this, 'onExportExcel'], ['type' => 'modelo', 'register_state' => 'false', 'static' => '1']), 'far:file-pdf red');

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

        parent::add($container);;
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
            $object = new SisPessoasFiscais($key, FALSE); // instantiates the Active Record
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


    public static function downloadFile($arquivo)
    {

        $path_parts = pathinfo($arquivo);

        $ext =  $path_parts['extension'];

        $fileName  = $path_parts['filename']; // desde o PHP 5.2.0

        $bloqueados = array('php', 'html', 'htm', 'asp');

        if (!in_array($ext, $bloqueados)) {

            if (isset($arquivo) && file_exists($arquivo)) {
                // faz o teste se a variavel não esta vazia e se o arquivo realmente existe

                // $handler = fopen($arquivo, 'w');

                // fclose($handler);
                parent::openFile($arquivo);
            }
        } else {
            echo "Erro!";
            exit;
        }
    }



    public function onExportExcel($param)
    {


        $type = $param["type"];

        // Write an .xlsx file  
        $date = date('d-m-y-' . substr((string)microtime(), 1, 8));
        $date = str_replace(".", "", $date);
        $filename = "export_" . $date . ".xlsx";
        $filePath = "app/output/" . $filename; //make sure you set the right permissions and change this to the path you want


        try {

            if ($type === "modelo") {

                /**
                 * para abrir e editar um modelo excel 
                 */
                // $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('app/resources/modelo1.xls');



                $spreadsheet = new Spreadsheet();

                $sheet = $spreadsheet->getActiveSheet();


                // // DEFININDO A LARGURA DAS COLUNAS
                $spreadsheet->getActiveSheet()->getColumnDimension('a')->setWidth(8);
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(45);
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(22);
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(19);

                /**
                 * mesclando celulas
                 */
                // $spreadsheet->getActiveSheet()->mergeCells('A1:B1');
                // $spreadsheet->getActiveSheet()->mergeCells('C1:D1');

                // // 
                // // $worksheet = $spreadsheet->getActiveSheet();

                $SubHeaderLocal = ['SEÇÃO',   'FISCAL',   'CONTATO',   'INDICAÇÃO'];

                $arrayData = [
                    ["LOCAL DE VOTAÇÃO", "", "RUA CHICO MENDES", ""],
                    $SubHeaderLocal,
                    ['Q1',   12,   15,   21],
                    ['Q2',   56,   73,   86],
                    ['Q3',   52,   61,   69],
                    ['Q4',   30,   32,    0],
                    ['Q5',   30,   32,    0],
                    ['Q6',   30,   32,    0],
                ];

                $arrayData1 = [
                    ["LOCAL DE VOTAÇÃO", "", "RUA CHICO MENDES", ""],
                    $SubHeaderLocal,
                    ['Q1',   12,   15,   21],
                    ['Q2',   56,   73,   86],
                    ['Q3',   52,   61,   69],
                    ['Q4',   30,   32,    0],
                ];




                // $spreadsheet->getActiveSheet()
                //     ->fromArray(
                //         $arrayData,  // The data to set
                //         NULL,        // Array values with this value will not be set
                //         'A1'         // Top left coordinate of the worksheet range where
                //         //    we want to set these values (default is A1)
                //     );

                // // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');

                $headers = ["SEÇÃO", "FISCAL", "CONTATO", "INDICAÇÃO"];
                $headers2 = ["SEÇÃO", "FISCAL", "CONTATO", "INDICAÇÃO"];

                $ListaFiscais = array(


                    [
                        ["460", "", "", ""],
                        ["467", "", "", ""],
                        ["468", "", "", ""],
                        ["469", "", "", ""],

                    ],
                    [
                        ["470", "", "", ""],
                        ["471", "", "", ""],
                        ["472", "", "", ""],
                        ["470", "", "", ""],
                        ["473", "", "", ""],
                        ["474", "", "", ""],
                        ["475", "", "", ""],
                        ["476", "", "", ""],

                    ],
                    [
                        ["470", "", "", ""],
                        ["471", "", "", ""],

                    ],
                    [
                        ["470", "", "", ""],
                        ["471", "", "", ""],

                    ],
                    [
                        ["470", "", "", ""],
                        ["471", "", "", ""],

                    ],
                    [
                        ["470", "", "", ""],
                        ["471", "", "", ""],

                    ],

                );

                $i = 1;
                $line = 1;
                $pos = 1;
                $NumHeaders = 1;
                $space = 2;

                $lastCellValue = null;

                /**
                 * pegando um intervalo de celulas e retornado uma coordenada
                 */

                foreach ($ListaFiscais as $fiscal) {

                    // $sheet->setCellValueByColumnAndRow($i, 1, $header);
                    $this->setValueByCoordinates($line, $headers, $sheet);
                    $currentCoord =  $sheet->getCellByColumnAndRow(1, $line+$NumHeaders)->getCoordinate();

                    $spreadsheet->getActiveSheet()
                        ->fromArray(
                            $fiscal,  // The data to set
                            NULL,        // Array values with this value will not be set
                            $currentCoord         // Top left coordinate of the worksheet range where
                            //    we want to set these values (default is A1)
                        );

                    $pos++;
                    $line = count($fiscal) + $NumHeaders + $line + $space;
                }



                // foreach ($headers as $header) {
                //     // $sheet->setCellValueByColumnAndRow($i, 1, $header);
                //     $this->setValueByCoordinates($i, 1, $header, $sheet);
                //     $lastCellAddress =  $sheet->getCellByColumnAndRow($i, 1)->getCoordinate();
                //     $lastRowAddress =   $sheet->getCellByColumnAndRow(1, 1)->getCoordinate();

                //     echo $lastRowAddress . "<br>";
                //     $i++;
                // }

                // $spreadsheet->getActiveSheet()->getStyle('A1:' . $lastCellAddress)->getFill()
                //     ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                //     ->getStartColor()->setARGB('FFFF00');

                // $spreadsheet->getActiveSheet()->getStyle('B1:' . $lastCellAddress)->getFill()
                //     ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                //     ->getStartColor()->setARGB('FFFF00');

                /**
                 * salvando o arquivo
                 */

                $writer = new Xlsx($spreadsheet);
                $writer->save($filePath);

                self::downloadFile($filePath);

                return;
            }

            if ($type === "one") {

                $spreadsheet = new Spreadsheet();
                $writer = new Xls($spreadsheet);
                // $writer->save('app/output/hello world.xlsx');   

                $sheet = $spreadsheet->getActiveSheet();
                // $sheet->setCellValue('A1', 'Hello World !');



                $sheet->setCellValue('A1', 'ID');
                $sheet->setCellValue('B1', 'Name');
                $sheet->setCellValue('C1', 'Name2');
                $sheet->setCellValue('D1', 'Name3');
                $sheet->setCellValue('E1', 'Type for conditional');
            }


            if ($type == "html") {


                $htmlString = '<table>
                <tr style="color:#2C57E9;line-higth 30px;">
                    <td>Hello World</td>
                </tr>
                <tr>
                    <td>Hello<br />World</td>
                </tr>
                <tr>
                    <td>Hello<br>World</td>
                </tr>
            </table>';

                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
                $spreadsheet = $reader->loadFromString($htmlString);

                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                // $writer->save('app/output/write.xls');

                // return;
            }


            // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile("05featuredemo.xlsx");
            $writer->save($filePath);

            // redirect output to client browser
            // $writer = new Xlsx($spreadsheet);
            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // header('Content-Disposition: attachment; filename="' . urlencode($filePath) . '"');
            // $writer->save('php://output');

        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }



    /**
     * method setValueByCoordinates()
     * inserir valores nas coordenadas
     */

    public function setValueByCoordinates($line, $fiscal, $sheet)
    {

        $col = 1;
        foreach ($fiscal as $info) {
            $sheet->setCellValueByColumnAndRow($col, $line, $info);
            $col++;
        }
    }



    /**
     * method onGenerate()
     * Executed whenever the user clicks at the generate button
     */
    function onGenerate()
    {
        try {
            // open a transaction with database 'samples'
            TTransaction::open('sistema');

            // get the form data into
            $data = $this->form->getData();

            $repository = new TRepository('sisPessoasFiscais');
            $criteria   = new TCriteria;
            if ($data->cidade) {
                $criteria->add(new TFilter('cidade', 'like', "%{$data->cidade}%"));
            }

            // if ($data->city_id) {
            //     $criteria->add(new TFilter('city_id', '=', $data->city_id));
            // }

            // if ($data->category_id) {
            //     $criteria->add(new TFilter('category_id', '=', $data->category_id));
            // }

            $customers = $repository->load($criteria);
            // $format  = $data->output_type;
            $format  = 'pdf';

            if ($customers) {
                $widths = array(40, 200, 80, 120, 80);

                switch ($format) {
                    case 'html':
                        $table = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $table = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        $table = new TTableWriterRTF($widths);
                        break;
                    case 'xls':
                        $table = new TTableWriterXLS($widths);
                        break;
                }

                if (!empty($table)) {
                    // create the document styles
                    $table->addStyle('header', 'Helvetica', '16', 'B', '#ffffff', '#4B5D8E');
                    $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                    $table->addStyle('title2',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                    $table->addStyle('datap',  'Helvetica', '25', '',  '#000000', '#E3E3E3', 'LR');
                    $table->addStyle('datai',  'Helvetica', '10', '',  '#000000', '#ffffff', 'LR');
                    $table->addStyle('footer', 'Helvetica', '10', '',  '#2B2B2B', '#B4CAFF');

                    $table->setHeaderCallback(function ($table) {
                        $table->addRow(null, null, 20);
                        $table->addCell('Customers', 'center', 'header', 5);

                        $table->addRow();

                        $table->addCell('Code',      'center', 'title2');
                        $table->addCell('Name',      'left',   'title');
                        $table->addCell('Category',  'center', 'title');
                        $table->addCell('Email',     'left',   'title');
                        $table->addCell('Birthdate', 'center', 'title');
                    });

                    $table->setFooterCallback(function ($table) {
                        $table->addRow();
                        $table->addCell(date('Y-m-d h:i:s'), 'center', 'footer', 5);
                    });

                    // controls the background filling
                    $colour = FALSE;
                    $heigth = FALSE;

                    // data rows
                    foreach ($customers as $customer) {
                        $style = $colour ? 'datap' : 'datai';
                        $table->addRow();


                        $table->addCell($customer->id,             'center', $style);
                        $table->addCell($customer->name,           'left',   'footer');
                        $table->addCell($customer->category_name,  'center', $style);
                        $table->addCell($customer->email,          'left',   $style);
                        $table->addCell($customer->birthdate,      'center', $style);

                        $colour = !$colour;
                    }

                    $output = "app/output/tabular.{$format}";

                    // stores the file
                    if (!file_exists($output) or is_writable($output)) {
                        $table->save($output);
                        parent::openFile($output);
                    } else {
                        throw new Exception(_t('Permission denied') . ': ' . $output);
                    }

                    // shows the success message
                    // new TMessage('info', "Report generated. Please, enable popups in the browser. <br> <a href='$output'>Click here for download</a>");
                }
            } else {
                new TMessage('error', 'No records found');
            }

            // fill the form with the active record data
            $this->form->setData($data);

            // close the transaction
            TTransaction::close();
        } catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
