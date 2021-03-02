<?php
/**
 * MedicamentoPacienteForm Form
 * @author  <your name here>
 */
class MedicamentoPacienteForm extends TPage
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
        $this->form = new BootstrapFormBuilder('form_MedicamentoPaciente');
        $this->form->setFormTitle('Cadastro de Medicamento para Pacientes');
        

        // create the form fields
        $paciente_id = new TDBCombo('paciente_id', 'db', 'Paciente', 'id', 'nome', 'nome');
        $medicamento_id = new TDBCombo('medicamento_id', 'db', 'Medicamento', 'id', 'nome', 'nome');
        $medicamento_id->setChangeAction(new TAction(array($this, 'getMedidaMedicamento')));
        $miligramas = new TEntry('miligramas');
        $quantidade = new TEntry('quantidade');
        $hora = new TEntry('hora');
        
        $sn = new TCombo('sn');
        $sn->setDefaultOption(false);
        $sn->addItems(['false' => 'Não', 'true' => 'Sim']);

        // add the fields
        $this->form->addFields( [ new TLabel('Paciente') ], [ $paciente_id ] );
        $this->form->addFields( [ new TLabel('Medicamento') ], [ $medicamento_id ] );
        $this->form->addFields( [ new TLabel('Miligramas') ], [ $miligramas ] );
        
        $lbMedida = new TLabel('');
        $lbMedida->setId('lbMedida');
        
        $this->form->addFields( [ new TLabel('Quantidade') ], [ $quantidade ], [$lbMedida] );
        $this->form->addFields( [ new TLabel('Hora') ], [ $hora ], [new TLabel('Se necessário')], [$sn]);
        //$this->form->addFields( );

        // set sizes
        $paciente_id->setSize('75%');
        $medicamento_id->setSize('75%');
        $miligramas->setSize('10%');
        $quantidade->setSize('75%');
        $hora->setSize('10%');

        if (!empty($paciente_id))
        {
            $paciente_id->setEditable(true);
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
            
            $object = new MedicamentoPaciente;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated paciente_id
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
                $object = new MedicamentoPaciente($key); // instantiates the Active Record
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
    
    public static function getMedidaMedicamento($medicamento = null) {
        TTransaction::open('db');
        $Medicamento =  new Medicamento($medicamento['medicamento_id']);
        TScript::create("$('#lbMedida').html('".$Medicamento->getMedida()->descricao."');");
        TTransaction::close();
    }
}
