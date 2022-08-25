<?php

use Adianti\Database\TRecord;

/**
 * Customer Active Record
 * @author  <your-name-here>
 */
class sisGeneralField extends TRecord
{
    const TABLENAME = 'sis_meta_field';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    public function __construct($id = NULL)
    { 
        parent::__construct($id); 
        parent::addAttribute('name');
        parent::addAttribute('user_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('group_id');
        parent::addAttribute('type');
        parent::addAttribute('meta_key');
        parent::addAttribute('meta_value');

        

    				
    }
}