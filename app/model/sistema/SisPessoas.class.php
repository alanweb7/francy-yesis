<?php

use Adianti\Database\TRecord;

/**
 * Customer Active Record
 * @author  <your-name-here>
 */
class SisPessoas extends TRecord
{
    const TABLENAME = 'sis_pessoas';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('nome');
        parent::addAttribute('cns');
        parent::addAttribute('unit_id');
        parent::addAttribute('group_id');
        parent::addAttribute('level_id');
        parent::addAttribute('segmento');
        parent::addAttribute('lotacao_id');
        parent::addAttribute('lotacao_local');
    
        parent::addAttribute('nascimento');
        parent::addAttribute('nome_mae');

        parent::addAttribute('sent_message');
        parent::addAttribute('sexo');
        parent::addAttribute('est_civil');
        parent::addAttribute('cpf');
        parent::addAttribute('rg');
        parent::addAttribute('t_eleitor');
        parent::addAttribute('zona');
        parent::addAttribute('secao');
        
        parent::addAttribute('naturalidade');
        parent::addAttribute('pessoas_casa');
        parent::addAttribute('endereco');
        parent::addAttribute('localizacao');
        parent::addAttribute('uf');
        parent::addAttribute('cidade');
        parent::addAttribute('bairro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('cep');
        parent::addAttribute('email');
        parent::addAttribute('data_cadastro');
        parent::addAttribute('local_atendimento');
        
        parent::addAttribute('solicitacao');
        parent::addAttribute('resultado');
        parent::addAttribute('indicacao');
        parent::addAttribute('user_id');
        parent::addAttribute('operador_id');
        parent::addAttribute('created_by');
        parent::addAttribute('updated_by');
        parent::addAttribute('observacoes');
        parent::addAttribute('fone1');
    }
}
