<?php
/**
 * Medidas Active Record
 * @author  <your-name-here>
 */
class Medidas extends TRecord
{
    const TABLENAME = 'medidas';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('sigla');
        parent::addAttribute('descricao');
    }


}
