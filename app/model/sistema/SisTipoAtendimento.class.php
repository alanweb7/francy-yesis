<?php

use Adianti\Database\TRecord;

/**
 * SisAtendimentos Active Record
 * @author  <your-name-here>
 */
class SisTipoAtendimento extends TRecord
{
    const TABLENAME = 'sis_tipo_atendimento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method 
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        
        parent::addAttribute('nome');
        parent::addAttribute('categoria');
        parent::addAttribute('meta_key');
        parent::addAttribute('meta_value');
        parent::addAttribute('unit_id');
        parent::addAttribute('group_id');
        parent::addAttribute('ordem_id');

     
    
    }



}
