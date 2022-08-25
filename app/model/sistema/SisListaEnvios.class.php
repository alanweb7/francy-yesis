<?php

use Adianti\Database\TRecord;

/**
 * SisAtendimentos Active Record
 * @author  <your-name-here>
 */
class SisListaEnvios extends TRecord
{
    const TABLENAME = 'sis_lista_envios';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method 
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('group_id');
        parent::addAttribute('message_id');
        parent::addAttribute('expires');
        parent::addAttribute('programmed');
        parent::addAttribute('send_date');
        parent::addAttribute('created');
        parent::addAttribute('sent');

    }

    						
}
