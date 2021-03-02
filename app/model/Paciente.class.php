<?php

class Paciente extends TRecord
{
    const TABLENAME = 'paciente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}

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

        $a = array();

        foreach ($Patologias as $Patologia) {
            $a[] = $Patologia->Patologia->nome;    
        }
        return $a;
        TTransaction::close();
    }
    
    public function getResponsavel() 
    {
        TTransaction::open('db');
        $Repo = new TRepository('Responsavel');
        $Criteria = new TCriteria;
        $Criteria->add(new TFilter('paciente_id', '=', $this->id));
        $Responsavel = $Repo->load($Criteria);
        TTransaction::close();
        return $Responsavel;            
    }
    
    public function getMedicamentosPaciente() 
    {
        TTransaction::open('db');
        $Repo = new TRepository('MedicamentoPaciente');
        $Criteria = new TCriteria;
        $Criteria->setProperty('order', 'hora');
        $Criteria->setProperty('direction','asc');
        $Criteria->add(new TFilter('paciente_id', '=', $this->id));
        $Medicamentos = $Repo->load($Criteria);
        TTransaction::close();
        return $Medicamentos;
    }
}