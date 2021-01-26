<?php
/**
 * Responsavel Active Record
 * @author  <your-name-here>
 */
class Responsavel extends TRecord
{
    const TABLENAME = 'responsavel';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('parentesco_id');
        parent::addAttribute('paciente_id');
        parent::addAttribute('telefone');
    }
    
    public function getParentesco() {
        
        TTransaction::open('db');
        $Parentesco = new Parentesco($this->parentesco_id);
        TTransaction::close();
        return $Parentesco;
    }
}
