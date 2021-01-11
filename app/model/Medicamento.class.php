<?php

class Medicamento extends TRecord
{
    const TABLENAME = 'medicamento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('descricao');
        parent::addAttribute('medidas_id');
    }
    
    public function getMedida() {
        return new Medidas($this->medidas_id);
    }
}