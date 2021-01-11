<?php
/**
 * MedicamentoPacienteFormList Form List
 * @author  <your name here>
 */
class MedicamentoPacienteFormList extends TPage
{
    protected $form; // form
    protected $datagrid; // datagrid
    protected $pageNavigation;
    protected $loaded;
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_MedicamentoPaciente');
        $this->form->setFormTitle('MedicamentoPaciente');

        // create the form fields
        //$id = new TEntry('id');
        
        $paciente_id = new TDBCombo('paciente_id', 'db', 'Paciente', 'id', 'nome');
        $paciente_id->setChangeAction(new TAction(null, 'getMedicamentosPaciente'));
        
        $medicamento_id = new TDBCombo('medicamento_id', 'db', 'Medicamento', 'id', 'nome');
        $quantidade = new TEntry('quantidade');

        $hora = new TEntry('hora');
        $miligramas = new TEntry('miligramas');


        // add the fields
        $this->form->addFields( [ new TLabel('Paciente') ], [ $paciente_id ] );
        $this->form->addFields( [ new TLabel('Medicamento') ], [ $medicamento_id ] );
        $this->form->addFields( [ new TLabel('Miligramas') ], [ $miligramas ] );
        $this->form->addFields([new TLabel('Quantidade')], [ $quantidade ], [new TLabel('')]);
        $this->form->addFields( [ new TLabel('Hora') ], [ $hora ] );


        // set sizes
        $medicamento_id->setSize('100%');
        $paciente_id->setSize('100%');
        //$mes_referente->setSize('100%');
        $quantidade->setSize('30%');
        $hora->setSize('10%');


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
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
       

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_paciente_id = new TDataGridColumn('paciente_id', 'Paciente', 'left');
        $column_medicamento_id = new TDataGridColumn('medicamento_id', 'Medicamento', 'left');
        $column_quantidade = new TDataGridColumn('quantidade', 'Qtd', 'left');
        $column_hora = new TDataGridColumn('hora', 'Hora', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_medicamento_id);
        
        $column_paciente_id->setTransformer(array($this, 'getNomePaciente'));
        $column_medicamento_id->setTransformer(array($this, 'getNomeMedicamento'));
        
        
        $this->datagrid->addColumn($column_paciente_id);
        //$this->datagrid->addColumn($column_mes_referente);
        $this->datagrid->addColumn($column_quantidade);
        $this->datagrid->addColumn($column_hora);

        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEdit']);
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue');
        $action1->setField('id');
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');
        
        // add the actions to the datagrid
        $this->datagrid->addAction($action1);
        $this->datagrid->addAction($action2);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        if(isset($param['paciente_id'])) {
            $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        }
        
        parent::add($container);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'db'
            TTransaction::open('db');
            
            // creates a repository for MedicamentoPaciente
            $repository = new TRepository('MedicamentoPaciente');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            $criteria->add(new TFilter('paciente_id', '=', $this->form->getData()->paciente_id));
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public static function onDelete($param)
    {
        // define the delete action
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public static function Delete($param)
    {
        try
        {
            $key = $param['key']; // get the parameter $key
            TTransaction::open('db'); // open a transaction with database
            $object = new MedicamentoPaciente($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            
            $pos_action = new TAction([__CLASS__, 'onReload']);
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $pos_action); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
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
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved')); // success message
            $this->onReload(); // reload the listing
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
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
    
    public function getNomePaciente($id) {
        TTransaction::open('db');
            $Paciente = new Paciente($id);
        TTransaction::close();
        return $Paciente->nome;
    
    }
    
    public function getNomeMedicamento($id) {
        TTransaction::open('db');
            $Medicamento = new Medicamento($id);
        TTransaction::close();
        return $Medicamento->nome;
    }

    static function getMedicamentoPaciente() {
        $this->onReload();
    }

}
