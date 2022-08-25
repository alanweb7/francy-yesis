<?php

use Adianti\Database\TRecord;

/**
 * Customer Active Record
 * @author  <your-name-here>
 */
class SisZonaSecaoPessoas extends TRecord
{
    const TABLENAME = 'zonas';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    public function __construct($id = NULL)
    { 
        parent::__construct($id); 
        parent::addAttribute('zona');
        parent::addAttribute('secao');
    

    }
} 