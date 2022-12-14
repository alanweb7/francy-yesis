<?php
/**
 * FullCalendarDatabaseView
 *
 * @version    1.0
 * @package    sistema
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AgendaMunicipiosAssessoriaPage extends TPage
{
    private $fc;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->fc = new TFullCalendar(date('Y-m-d'), 'month');
        $this->fc->setReloadAction(new TAction(array($this, 'getEvents')));
        $this->fc->setDayClickAction(new TAction(array('CalendarEventForm', 'onStartEdit')));
        $this->fc->setEventClickAction(new TAction(array('CalendarEventForm', 'onEdit')));
        $this->fc->setEventUpdateAction(new TAction(array('CalendarEventForm', 'onUpdateEvent')));
        
        $this->fc->setOption('businessHours', [ [ 'dow' => [ 1, 2, 3, 4, 5 ], 'start' => '08:00', 'end' => '18:00' ]]);
        //$this->fc->setTimeRange('10:00', '18:00');
        //$this->fc->disableDragging();
        //$this->fc->disableResizing();
        parent::add( $this->fc );
    }
    
    /**
     * Output events as an json
     */
    public static function getEvents($param=NULL)
    {
        $return = array();
        try
        {
            TTransaction::open('sistema');
            
            $userId = TSession::getValue("userid");
            // if ((int) $userId !== 1) {
    
            //     $base_criteria = new TCriteria;
            //     $base_criteria->add(new TFilter('created_by', '=', $userId));
            //     $this->setCriteria($base_criteria); // define a standard filter
    
            // }


            // $criteria = new TCriteria();
            // $criteria->add(new TFilter('start_time', '>=', $param['start']));
            // $criteria->add(new TFilter('end_time',   '<=', $param['end']));
            
            // $criteria_ispublic = new TCriteria();
            // $criteria_ispublic->add(new TFilter('ispublic', '=', 'S'),  TExpression::OR_OPERATOR);
            // $criteria_ispublic->add(new TFilter('system_user_id', '=', TSession::getValue('login_id')));
            
            // $repository_activity = TRepository('SystemActivity');
            // $events = $repository_activity->load($criteria);




            $events = CalendarEvent::where('start_time', '<=', $param['end'])
                                   ->where('end_time',   '>=', $param['start'])->load();
            
            if ($events)
            {
                foreach ($events as $event)
                {
                    $event_array = $event->toArray();
                    $event_array['start'] = str_replace( ' ', 'T', $event_array['start_time']);
                    $event_array['end']   = str_replace( ' ', 'T', $event_array['end_time']);
                    
                    $popover_content = $event->render("<b>Title</b>: {title} <br> <b>Description</b>: {description}");
                    $event_array['title'] = TFullCalendar::renderPopover($event_array['title'], 'Popover title', $popover_content);
                    
                    $return[] = $event_array;
                }
            }
            TTransaction::close();
            echo json_encode($return);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Reconfigure the callendar
     */
    public function onReload($param = null)
    {
        if (isset($param['view']))
        {
            $this->fc->setCurrentView($param['view']);
        }
        
        if (isset($param['date']))
        {
            $this->fc->setCurrentDate($param['date']);
        }
    }
}