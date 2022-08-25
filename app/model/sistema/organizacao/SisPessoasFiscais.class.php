<?php

use Adianti\Database\TRecord;

/**
 * Customer Active Record
 * @author  <your-name-here>
 */
class sisPessoasFiscais extends TRecord
{
    const TABLENAME = 'sis_pessoas_fiscais';
    const PRIMARYKEY = 'id';
    const IDPOLICY =  'max'; // {max, serial}

    private $sisregioes;
    private $idsisregioes;

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
        parent::addAttribute('local_votacao');

        parent::addAttribute('naturalidade');
        parent::addAttribute('pessoas_casa');
        parent::addAttribute('endereco');
        parent::addAttribute('localizacao');
        parent::addAttribute('uf');
        parent::addAttribute('regiao_id');
        parent::addAttribute('cidade');
        parent::addAttribute('bairro');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('cep');
        parent::addAttribute('email');
        parent::addAttribute('data_cadastro');
        parent::addAttribute('local_atendimento');

        parent::addAttribute('solicitacao');
        parent::addAttribute('posicao');
        parent::addAttribute('indicacao');
        parent::addAttribute('user_id');
        parent::addAttribute('operador_id');
        parent::addAttribute('created_by');
        parent::addAttribute('updated_by');
        parent::addAttribute('observacoes');
        parent::addAttribute('fone1');
    }





    /**
     * Method set_sisregioes
     * Sample of usage: $municipio->sisregioes = $object;
     * @param $object Instance of sisregioes
     */
    public function set_sisregioes(SisLocaisRegioes $object)
    {
        $this->sisregioes = $object;
        $this->idsisregioes = $object->id;
    }

    /**
     * Method get_sisregioes
     * Sample of usage: $municipio->sisregioes->attribute;
     * @returns sisregioes instance
     */
    public function get_sisregioes()
    {
        // loads the associated object
        if (empty($this->sisregioes))
            $this->sisregioes = new SisLocaisRegioes($this->regiao_id);

        // returns the associated object
        return $this->sisregioes;
    }
}
