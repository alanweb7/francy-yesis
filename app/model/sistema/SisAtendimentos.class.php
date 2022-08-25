<?php

use Adianti\Database\TRecord;

/**
 * SisAtendimentos Active Record
 * @author  <your-name-here>
 */
class SisAtendimentos extends TRecord
{
    const TABLENAME = 'sis_atendimento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $sispessoas;
    
    /**
     * Constructor method 
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);

        
        parent::addAttribute('pessoa_id');
        parent::addAttribute('unit_id');
        parent::addAttribute('segmento');
        parent::addAttribute('endereco');
        parent::addAttribute('cidade');
        parent::addAttribute('bairro');
        parent::addAttribute('local_atendimento');
        parent::addAttribute('data_marcada');
        parent::addAttribute('solicitacao');
        parent::addAttribute('resultado');
        parent::addAttribute('observacoes');
        parent::addAttribute('operador_id');
        parent::addAttribute('created_by');
        parent::addAttribute('updated_by');
        parent::addAttribute('created_date');
        parent::addAttribute('update_date');
    
    }


    /**
     * Method set_sispessoas
     * Sample of usage: $municipio->sispessoas = $object;
     * @param $object Instance of sispessoas
     */
    public function set_sispessoas(SisPessoas $object)
    {
        $this->sispessoas = $object;
        $this->idsispessoas = $object->id; 
    }
    
    /**
     * Method get_sispessoas
     * Sample of usage: $municipio->sispessoas->attribute;
     * @returns sispessoas instance
     */
    public function get_sispessoas()
    {
        // loads the associated object
        if (empty($this->sispessoas))
            $this->sispessoas = new SisPessoas($this->pessoa_id);
    
        // returns the associated object
        return $this->sispessoas;
    }
    




}
