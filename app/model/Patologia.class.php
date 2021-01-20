<?php
/**
 * Patologia Active Record
 * @author  <your-name-here>
 */
class Patologia extends TRecord
{
    const TABLENAME = 'patologia';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('descricao');
        parent::addAttribute('cid');
    }


}
