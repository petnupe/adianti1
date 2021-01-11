<?php

class TesteRola extends TPage 
{
    private $form, $container;
    protected $datagrid;
    protected $pageNavigation;
    
    public function __construct() {
    
        parent::__construct();
               
        $this->form = new BootstrapFormBuilder('form_Teste');
        $this->form->setFormTitle('Teste');
        
        $paciente_id = new TDBCombo('paciente_id', 'db', 'Paciente', 'id', 'nome');
        $this->form->addFields([new TLabel('Teste')], [$paciente_id]);
        
        $this->form->addAction('Find', new TAction([$this, 'onReload']), 'fa:search blue');
        
        $this->container = new TVBox;
        $this->container->style = 'width: 100%; margin-right:300px;';
        $this->container->add($this->form);
      

        
       

        $this->container->add($this->datagrid);

        parent::add($this->container);        
    
    }
    
    public function onReload($param = NULL)
    {

        try
        {
            $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
            $this->datagrid->createModel();
            
            
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
            $count = $repository->count($criteria);
                    $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

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

}