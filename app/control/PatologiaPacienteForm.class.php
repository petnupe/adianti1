<?php
/**
 * PatologiaPacienteForm Form
 * @author  <your name here>
 */
class PatologiaPacienteForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_PatologiaPaciente');
        $this->form->setFormTitle('Patologias por paciente');
        

        // create the form fields
        $paciente_id = new TDBCombo('paciente_id', 'db', 'Paciente', 'id', 'nome', 'nome');
        $patologia_id = new TDBCombo('patologia_id', 'db', 'Patologia', 'id', 'nome', 'nome');


        // add the fields
        $this->form->addFields( [ new TLabel('Paciente') ], [ $paciente_id ] );
        $this->form->addFields( [ new TLabel('Patologia') ], [ $patologia_id ] );

        $paciente_id->addValidation('Paciente', new TRequiredValidator);
        $patologia_id->addValidation('Patologia', new TRequiredValidator);


        // set sizes
        $paciente_id->setSize('100%');
        $patologia_id->setSize('100%');



        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( '100%' ); // set size
         **/
         
        // create the form actions
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('db'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            $object = new PatologiaPaciente;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            //$data->id = $object->id;
            
            $data->paciente_id = $object->paciente_id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
            
            $this->onReload($param);
            
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('db'); // open a transaction
                $object = new PatologiaPaciente($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    public function onReload($param = null) {
        $paciente_id = $this->form->getData()->paciente_id;
        $data = new StdClass();
        $data->paciente_id = $paciente_id;
        $this->form->clear();
        $this->form->setData($data);
    }

}
