<?php

use Adianti\Database\TRecord;

/**
 * Customer Active Record
 * @author  <your-name-here>
 */
class SisCidadesVw extends TRecord
{
    const TABLENAME = 'sis_cidades_vw';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    public function __construct($id = NULL)
    { 
        parent::__construct($id); 
        parent::addAttribute('nome');

    }
} 