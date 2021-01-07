<?php
/**
 * MedicamentoPaciente Active Record
 * @author  <your-name-here>
 */
class MedicamentoPaciente extends TRecord
{
    const TABLENAME = 'medicamento_paciente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $paciente;
    private $medicamento;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('medicamento_id');
        parent::addAttribute('paciente_id');
        parent::addAttribute('mes_referente');
        parent::addAttribute('quantidade');
    }

    
    /**
     * Method set_paciente
     * Sample of usage: $medicamento_paciente->paciente = $object;
     * @param $object Instance of Paciente
     */
    public function set_paciente(Paciente $object)
    {
        $this->paciente = $object;
        $this->paciente_id = $object->id;
    }
    
    /**
     * Method get_paciente
     * Sample of usage: $medicamento_paciente->paciente->attribute;
     * @returns Paciente instance
     */
    public function get_paciente()
    {
        // loads the associated object
        if (empty($this->paciente))
            $this->paciente = new Paciente($this->paciente_id);
    
        // returns the associated object
        return $this->paciente;
    }
    
    
    /**
     * Method set_medicamento
     * Sample of usage: $medicamento_paciente->medicamento = $object;
     * @param $object Instance of Medicamento
     */
    public function set_medicamento(Medicamento $object)
    {
        $this->medicamento = $object;
        $this->medicamento_id = $object->id;
    }
    
    /**
     * Method get_medicamento
     * Sample of usage: $medicamento_paciente->medicamento->attribute;
     * @returns Medicamento instance
     */
    public function get_medicamento()
    {
        // loads the associated object
        if (empty($this->medicamento))
            $this->medicamento = new Medicamento($this->medicamento_id);
    
        // returns the associated object
        return $this->medicamento;
    }
    


}
