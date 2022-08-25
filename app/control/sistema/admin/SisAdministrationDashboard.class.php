<?php

/**
 * SystemAdministrationDashboard
 *
 * @version    1.0
 * @package    control
 * @subpackage log
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SisAdministrationDashboard extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */

     private $iframe;

    function __construct()
    {
        parent::__construct();

        try {
            $html = new THtmlRenderer('app/resources/sis_admin_dashboard.html');

            TTransaction::open('permission');
            $indicator1 = new THtmlRenderer('app/resources/card-info-box.html');
            $indicator2 = new THtmlRenderer('app/resources/card-info-box.html');
            $indicator3 = new THtmlRenderer('app/resources/card-info-box.html');
            $indicator4 = new THtmlRenderer('app/resources/card-info-box.html');
            $indicator5 = new THtmlRenderer('app/resources/card-info-box.html');
            $indicator6 = new THtmlRenderer('app/resources/card-info-box.html');
            $indicator7 = new THtmlRenderer('app/resources/card-info-box.html');

            $indicator1->enableSection('main', ['title' => 'Pendente',              'icon' => 'regular fa-clock',       'background' => 'orange', 'value' => SystemUser::count()]);
            $indicator2->enableSection('main', ['title' => 'Agendado',              'icon' => 'calendar',      'background' => 'blue',   'value' => SystemGroup::count()]);
            $indicator3->enableSection('main', ['title' => 'Em fila de espera',     'icon' => 'sync', 'background' => 'purple', 'value' => SystemUnit::count()]);
            $indicator4->enableSection('main', ['title' => 'Enviado',               'icon' => 'solid fa-check',       'background' => 'green',  'value' => SystemProgram::count()]);
            $indicator5->enableSection('main', ['title' => 'Entregue',              'icon' => 'solid fa-check-double',       'background' => 'orange', 'value' => SystemUser::count()]);
            $indicator6->enableSection('main', ['title' => 'Faltando',              'icon' => 'solid fa-circle-minus',      'background' => 'blue',   'value' => SystemGroup::count()]);
            $indicator7->enableSection('main', ['title' => 'Recebido',              'icon' => 'university', 'background' => 'purple', 'value' => SystemUnit::count()]);


            $chart1 = new THtmlRenderer('app/resources/google_bar_chart.html');
            $data1 = [];
            $data1[] = ['Group', 'Users'];

            $stats1 = SystemUserGroup::groupBy('system_group_id')->countBy('system_user_id', 'count');
            if ($stats1) {
                foreach ($stats1 as $row) {
                    $data1[] = [SystemGroup::find($row->system_group_id)->name, (int) $row->count];
                }
            }

            // replace the main section variables
            $chart1->enableSection('main', [
                'data'   => json_encode($data1),
                'width'  => '100%',
                'height'  => '500px',
                'title'  => _t('Users by group'),
                'ytitle' => _t('Users'),
                'xtitle' => _t('Count'),
                'uniqid' => uniqid()
            ]);

            $chart2 = new THtmlRenderer('app/resources/google_pie_chart.html');
            $data2 = [];
            $data2[] = ['Unit', 'Users'];

            $stats2 = SystemUserUnit::groupBy('system_unit_id')->countBy('system_user_id', 'count');

            if ($stats2) {
                foreach ($stats2 as $row) {
                    $data2[] = [SystemUnit::find($row->system_unit_id)->name, (int) $row->count];
                }
            }
            // replace the main section variables
            $chart2->enableSection('main', [
                'data'   => json_encode($data2),
                'width'  => '100%',
                'height'  => '500px',
                'title'  => _t('Users by unit'),
                'ytitle' => _t('Users'),
                'xtitle' => _t('Count'),
                'uniqid' => uniqid()
            ]);

            $html->enableSection('main', [
                'indicator1' => $indicator1,
                'indicator2' => $indicator2,
                'indicator3' => $indicator3,
                'indicator4' => $indicator4,
                // 'indicator5' => $indicator5,
                // 'indicator6' => $indicator6,
                // 'indicator7' => $indicator7,

                //   'chart1'     => $chart1,
                // 'chart2'     => $chart2,
                'chart1'     => NULL,
                'chart2'     => NULL
            ]);

            $this->iframe = new TElement('iframe');
            $this->iframe->id = "iframe_external";
            $this->iframe->src = "http://66.70.188.94:3401/start?session=779"; 
            $this->iframe->frameborder = "0";
            $this->iframe->scrolling = "yes";
            $this->iframe->width = "100%";
            $this->iframe->height = "700px";

            $container = new TVBox;
            $container->style = 'width: 100%';
            $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
            $container->add($html);
            $container->add($this->iframe);

            parent::add($container);
            TTransaction::close();
        } catch (Exception $e) {
            parent::add($e->getMessage());
        }
    }
}
