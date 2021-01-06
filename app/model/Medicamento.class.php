<?php
/**
 * Medicamento Active Record
 * @author  <your-name-here>
 */
class Medicamento extends TRecord
{
    const TABLENAME = 'medicamento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('descricao');
        parent::addAttribute('medidas_id');
    }


}
