<?php

use Adianti\Database\TRecord;

/**
 * SisAtendimentos Active Record
 * @author  <your-name-here>
 */
class SisLocaisVotacaoTRecord extends TRecord
{
    const TABLENAME = 'sis_locais_votacao_resumido';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}


    /**
     * Constructor method 
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);


        parent::addAttribute('municipio');
        parent::addAttribute('zona');
        parent::addAttribute('secao');
        parent::addAttribute('local_votacao');
        parent::addAttribute('endereco');
        parent::addAttribute('bairro');
        parent::addAttribute('qt_eleitor');
    }
}
