<?php

use Adianti\Database\TRecord;

/**
 * Customer Active Record
 * @author  <your-name-here>
 */
class SisMultiplicadores extends TRecord
{
    const TABLENAME = 'sis_multiplicadores';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('unit_id');
        parent::addAttribute('group_id');
        parent::addAttribute('level_id');


        parent::addAttribute('data_cadastro');
        parent::addAttribute('nome');
        parent::addAttribute('data_nascimento');
        parent::addAttribute('nome_mae');
        parent::addAttribute('cep');
        parent::addAttribute('endereco');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('cidade');
        parent::addAttribute('fone1');
        parent::addAttribute('fone2');
        parent::addAttribute('fone3');
        parent::addAttribute('email');
        parent::addAttribute('zona');
        parent::addAttribute('secao');
        parent::addAttribute('indicacao');
        parent::addAttribute('usuario_cad');
        parent::addAttribute('usuario_upd');
        parent::addAttribute('candidato');
        parent::addAttribute('total_presente');

        parent::addAttribute('sent_message');
        parent::addAttribute('sexo');
        parent::addAttribute('zona');
        parent::addAttribute('secao');

     
        // parent::addAttribute('localizacao');
        // parent::addAttribute('uf');
        // parent::addAttribute('observacoes');

    }
}

