<?php

use Adianti\Database\TRecord;

/**
 * Customer Active Record
 * @author  <your-name-here>
 */
class SisLocaisVotacaoView extends TRecord
{
    const TABLENAME = 'locais_votacao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    public function __construct($id = NULL)
    { 
        parent::__construct($id); 
        parent::addAttribute('municipio');
        parent::addAttribute('zona');
        parent::addAttribute('secao');
        parent::addAttribute('local_votacao');
        parent::addAttribute('endereco');
        parent::addAttribute('bairro');
        parent::addAttribute('qt_eleitor');

    }
} 



