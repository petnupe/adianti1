<?php
/**
 * PatologiaPaciente Active Record
 * @author  <your-name-here>
 */
class PatologiaPaciente extends TRecord
{
    const TABLENAME = 'patologia_paciente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    private $paciente;
    private $patologia;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('patologia_id');
        parent::addAttribute('paciente_id');
    }

    
    /**
     * Method set_paciente
     * Sample of usage: $patologia_paciente->paciente = $object;
     * @param $object Instance of Paciente
     */
    public function set_paciente(Paciente $object)
    {
        $this->paciente = $object;
        $this->paciente_id = $object->id;
    }
    
    /**
     * Method get_paciente
     * Sample of usage: $patologia_paciente->paciente->attribute;
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
     * Method set_patologia
     * Sample of usage: $patologia_paciente->patologia = $object;
     * @param $object Instance of Patologia
     */
    public function set_patologia(Patologia $object)
    {
        $this->patologia = $object;
        $this->patologia_id = $object->id;
    }
    
    /**
     * Method get_patologia
     * Sample of usage: $patologia_paciente->patologia->attribute;
     * @returns Patologia instance
     */
    public function get_patologia()
    {
        // loads the associated object
        if (empty($this->patologia))
            $this->patologia = new Patologia($this->patologia_id);
    
        // returns the associated object
        return $this->patologia;
    }
    


}
