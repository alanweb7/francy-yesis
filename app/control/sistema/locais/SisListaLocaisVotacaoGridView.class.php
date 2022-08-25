<?php

use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Wrapper\TDBCombo;
use LDAP\Result;

require 'app/lib/phpexcel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * SystemUserList Listing
 * @author  <your name here>
 */
class SisListaLocaisVotacaoGridView extends TPage
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
        $this->setActiveRecord('SisLocaisVotacaoTRecord');   // defines the active record
        $this->setDefaultOrder('municipio', 'asc');         // defines the default order
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
        $this->addFilterField('id', '=', 'municipio'); // filterField, operator, formField
        $this->addFilterField('bairro', 'LIKE', 'bairro'); // filterField, operator, formField


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
        $nome = new TDBCombo('municipio', 'sistema', 'SisLocaisVotacaoTRecord', 'municipio', 'municipio', null);
        $regiao = new TDBCombo('regiao', 'sistema', 'SisLocaisRegioes', 'regiao', 'regiao', null);
        $indicacao = new TEntry('indicacao');
        $cidade = new TEntry('cidade');
        $bairro = new TDBCombo('bairro', 'sistema', 'SisLocaisVotacaoTRecord', 'bairro', 'bairro', null);
        $contato = new TCombo('fone1');


        $nome->enableSearch('true');
        $regiao->enableSearch('true');

        $row = $this->form->addFields(
            [new TLabel('Municipio'),    $nome],
            [new TLabel('Bairro'),    $bairro]
        );

        $row->layout = ['col-sm-6', 'col-sm-6'];

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
            'local_votacao' => new TDataGridColumn('local_votacao', 'Local', 'left', '40%'),
            'municipio' => new TDataGridColumn('municipio', 'Município', 'left'),
            'bairro' => new TDataGridColumn('bairro', 'Bairro', 'left', '40%'),
        ];



        // add the columns to the DataGrid
        foreach ($colums as $key => $colum) {

            $this->datagrid->addColumn($colum);

            if ($key === 'municipio') {
                $colum->setAction(new TAction([$this, 'onReload']), ['order' => 'municipio']);
            }
            if ($key === 'bairro') {
                $colum->setAction(new TAction([$this, 'onReload']), ['order' => 'bairro']);
            }

            // if ($key === 'situacao') {


            //     // define the transformer method over image
            //     $colum->setTransformer(function ($value, $object, $row) {

            //         if (!$value) return;
            //         $label = "danger";

            //         if ($value === "resolvido") {

            //             $label = "success";
            //         }

            //         $texto = strtoupper($object->situacao);
            //         $div = new TElement('span');
            //         $div->class = "label label-{$label}";
            //         $div->style = "text-shadow:none; font-size:12px";
            //         $div->add($texto);
            //         return $div;
            //     });
            // }


        }


        $action1 = new TDataGridAction(['SisCadastroLocaisVotacaoForm', 'onEdit'], ['id' => '{id}']);
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
        $newRegister = new TActionLink('Novo Atendimento', new TAction(array('SisCadastroLocaisVotacaoForm', 'onLoad'), ['type_register' => 6]), 'green', 10, null, 'fa:plus-circle');
        $newRegister->class = 'btn btn-default';
        $newRegister->style .= ';margin-top: 4px';

        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        // $dropdown->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static' => '1']), 'fa:table blue');
        $dropdown->addAction('Exportar XLS (Excel)', new TAction([$this, 'onExportExcel'], ['type' => 'xls', 'register_state' => 'false', 'static' => '1']), 'far:file-pdf red');

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
            $object = new SisLocaisVotacaoTRecord($key, FALSE); // instantiates the Active Record
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


    public function formatDate($date, $object, $row)
    {

        // var_dump($date);
        $dt = new DateTime($date);
        return $dt->format('d/m/Y');
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



    public static function onExportExcel($param)
    {


        $type = $param['type'];

        // Write an .xlsx file  
        $date = date('d-m-y-' . substr((string)microtime(), 1, 8));
        $date = str_replace(".", "", $date);
        $filename = "export_" . $date . ".xlsx";
        $filePath = "app/output/" . $filename; //make sure you set the right permissions and change this to the path you want


        try {

            if ($type === "xls") {

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

                $arrayData = (new SisListaLocaisVotacaoGridView)->getInfoLocaisVotacao();


                $locais = array();

                $secoes = array();

                foreach ($arrayData as $key => $local) {

                    $locais[] = [

                        "id" => $local->id,
                        "municipio" => $local->municipio,
                        "zona"  => $local->zona,
                        "secao" => $local->secao,
                        "local_votacao" => $local->local_votacao,
                        "endereco" => $local->endereco,
                        "bairro" => $local->bairro,
                        "qt_eleitor" => $local->qt_eleitor,

                    ];

                    array_push($secoes, $local->secao);
                }

                $arrayLocais = [

                    'headers' => [],
                    'data' => $locais

                ];


                $getLocais = (new SisListaLocaisVotacaoGridView)->group_by("endereco", $locais);



                $fsicaisSecao = (new SisListaLocaisVotacaoGridView)->getFiscalBySecao($getLocais);


                echo "<pre>";
                var_dump($getLocais);
                echo "</pre>";
                echo "<pre>";
                echo "fiscais:";
                var_dump($fsicaisSecao);
                echo "</pre>";


                return;
                // echo "<pre>";
                // var_dump($getLocais);
                // echo "</pre>";
                /**
                 *     array(8) {
                 * ["id"]=>
                 * string(1) "2"
                 * ["municipio"]=>
                 * string(10) "ANANINDEUA"
                 * ["zona"]=>
                 * string(2) "43"
                 * ["secao"]=>
                 * string(3) "597"
                 * ["local_votacao"]=>
                 * string(18) "JARDIM AMAZÔNIA 1"
                 * ["endereco"]=>
                 * string(30) "CJ JD AMAZONIA 1, R. COIMBRA 2"
                 * ["bairro"]=>
                 * string(12) "ÁG. BRANCAS"
                 * ["qt_eleitor"]=>
                 * string(3) "398"
                 * }
                 */


                $arrayLocais = [

                    'data' => $getLocais

                ];


                $i = 1;
                $line = 1;
                $pos = 1;
                $NumHeaders = 1;
                $space = 2;

                $lastCellValue = null;

                /**
                 * pegando um intervalo de celulas e retornado uma coordenada
                 */

                foreach ($arrayLocais['data'] as $fiscal) {

                    // $sheet->setCellValueByColumnAndRow($i, 1, $header);
                    (new SisListaLocaisVotacaoGridView)->setValueByCoordinates($line, $headers, $sheet);
                    $currentCoord =  $sheet->getCellByColumnAndRow(1, $line + $NumHeaders)->getCoordinate();

                    $fiscalInfo = [];

                    foreach ($fiscal as $key => $value) {

                        $fiscalInfo[] =   [$value['secao']];
                    }


                    // (new SisListaLocaisVotacaoGridView)->setPopulateTable($fiscal);

                    $spreadsheet->getActiveSheet()
                        ->fromArray(
                            $fiscalInfo,  // The data to set
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
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }




    public function getFiscalBySecao($arraySecoes)
    {
        try {

            // open a transaction with database 'samples'
            TTransaction::open('sistema');

            $repository = new TRepository('SisPessoasFiscais');
            $criteria   = new TCriteria;

            if($arraySecoes) $criteria->add(new TFilter('secao','IN',     array($arraySecoes))); 

            // if ($data->cidade) {
            //     $criteria->add(new TFilter('cidade', 'like', "%{$data->cidade}%"));
            // }

            // if ($data->city_id) {
            //     $criteria->add(new TFilter('city_id', '=', $data->city_id));
            // }

            // if ($data->category_id) {
            //     $criteria->add(new TFilter('category_id', '=', $data->category_id));
            // }

            $response = $repository->load($criteria);
 

            TTransaction::close();


            $data = array();


            foreach ($response as $key => $value) {
                
                $data[] = [

                    'id'=> $value->id,
                    'nome'=> $value->nome
        

                ];
                
            }

            return $data;

        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function getInfoLocaisVotacao()
    {

        $nome = false;
        $bairro = false;
        $regiao = false;
        $cidade = false;

        $data = TSession::getValue('SisLocaisVotacaoTRecord_filter_data') ?? false;

        // put the data back to the form
        $this->form->setData($data);

        // creates a string with the form element's values

        if (!empty($data) && $data != null) {

            // $nome =     $data->nome;
            // $regiao =   $data->regiao;
            $bairro =   $data->bairro;
            // $cidade =   $data->cidade;

        }

        try {

            TTransaction::open('sistema'); // open transaction

            // query criteria
            $criteria = new TCriteria;
            if ($bairro) $criteria->add(new TFilter('bairro', 'LIKE', $bairro));
            // $criteria->add(new TFilter('status', '=', 'M'));

            // load using repository
            $repository = new TRepository('SisLocaisVotacaoTRecord');
            $locais = $repository->load($criteria);


            $response = [];
            foreach ($locais as $local) {

                $response[] = $local;
            }

            TTransaction::close(); // close transaction


            return $response;

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

            $response['data'] = $ListaFiscais;
            return $response;
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


    public function group_by($key, $data)
    {

        $result = array();

        $chave = 0;

        foreach ($data as $val) {


            if (array_key_exists($key, $val)) {

                $result[$val[$key]][] = $val;
            } else {

                $result[""][] = $val;
            }
        }



        $response = array();
        $i = 0;
        foreach ($result as $key => $value) {
            $response[$i] = $value;

            $i++;
        }

        return $response;
    }
}
