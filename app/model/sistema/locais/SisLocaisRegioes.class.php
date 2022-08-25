<?php

use Adianti\Database\TRecord;

/**
 * SisAtendimentos Active Record
 * @author  <your-name-here>
 */
class SisLocaisRegioes extends TRecord
{
    const TABLENAME = 'sis_regioes_para';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}


    /**
     * Constructor method 
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);


        parent::addAttribute('regiao_id');
        parent::addAttribute('municipio');
        parent::addAttribute('regiao');
    
    }
}
