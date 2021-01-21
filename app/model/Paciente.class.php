<?php
/**
 * Paciente Active Record
 * @author  <your-name-here>
 */
class Paciente extends TRecord
{
    const TABLENAME = 'paciente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('dataNasc');
        parent::addAttribute('cartaoSus');
        parent::addAttribute('rg');
        parent::addAttribute('cpf');
        parent::addAttribute('dataEntrada');
        parent::addAttribute('genero');
        parent::addAttribute('ativo');
        parent::addAttribute('alfabetizado');
    }
    
    public function getPatologias() {
        TTransaction::open('db');
        $Repo = new TRepository('PatologiaPaciente');
        $Criteria =  new TCriteria;
        $Criteria->add(new TFilter('paciente_id', '=' , $this->id));
        $Patologias = $Repo->load($Criteria, false);

        foreach ($Patologias as $Patologia) {
            $a[] = $Patologia->Patologia->nome;    
        }
        return $a;
        TTransaction::close();
    }
}