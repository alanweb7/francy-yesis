<?php

use Adianti\Database\TRecord;

/**
 * SisAtendimentos Active Record
 * @author  <your-name-here>
 */
class SisAtendimentoSaude extends TRecord
{
    const TABLENAME = 'sis_atendimento_saude';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}


    /**
     * Constructor method 
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);


        parent::addAttribute('unit_id');
        parent::addAttribute('group_id');
        parent::addAttribute('user_id');
        parent::addAttribute('nome');
        parent::addAttribute('t_eleitor');
        parent::addAttribute('zona');
        parent::addAttribute('secao');
        parent::addAttribute('local_consulta');
        parent::addAttribute('endereco_consulta');
        parent::addAttribute('cns');
        parent::addAttribute('solicitacao');
        parent::addAttribute('solicitante');
        parent::addAttribute('indicacao');
        parent::addAttribute('descricao');
        parent::addAttribute('fone1');
        parent::addAttribute('fone2');
        parent::addAttribute('cpf');
        parent::addAttribute('est_civil');
        parent::addAttribute('endereco');
        parent::addAttribute('cidade');
        parent::addAttribute('bairro');
        parent::addAttribute('situacao');
        parent::addAttribute('observacao');
        parent::addAttribute('data_consulta');
        parent::addAttribute('hora_consulta');
        parent::addAttribute('data_registro');
        parent::addAttribute('data_update'); 
    }
}
