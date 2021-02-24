<?php

class PatologiaPacienteListManual extends TPage 
{
    protected $form, $datagrid;
        
    public function __construct()
    {
        parent::__construct();
        $this->form = new BootstrapFormBuilder('form_PatologiaPaciente');
        $this->form->setFormTitle('Titulo do form');
        
        $paciente_id = new TDBCombo('paciente_id', 'db', 'Paciente', 'id', 'nome', 'nome');
        
        $change_action = new TAction(array($this, 'onChangePaciente'));
        $paciente_id->setChangeAction($change_action);
        
        $this->form->addFields( [ new TLabel('Paciente') ], [ $paciente_id ] );
        
        $btnFind = $this->form->addAction(_t('Find'), new TAction([$this, 'onReload']), 'fa:search');
        
        $btnNew = $this->form->addAction(_t('New'), new TAction(['PatologiaPacienteForm', 'onReload']), 'fa:eraser red');
        

        $this->createDataGrid();
        $panel = new TPanel('', 'white');
        $panel->add($this->datagrid);

        $container = new TVBox();
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add($panel);
        parent::add($container);
    }
    
    function createDataGrid()
    {
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        $column_patologia_id = new TDataGridColumn('patologia_id', 'Patologia', 'left');
        $column_patologia_id->setTransformer([$this, 'getNomePatologia']);
        $this->datagrid->addColumn($column_patologia_id);

        $action1 = new TDataGridAction(['PatologiaPacienteForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        $this->datagrid->createModel();
    }
    
    function onReload($param = null)
    {
        $dados = $this->form->getData();
        $dados->paciente_id =  empty($param['paciente_id']) ? $param[1]['key'] : $param['paciente_id'];
        
        $this->form->setData($dados);
       
        if(trim($dados->paciente_id)) {
            TTransaction::open('db');
                $repo = new TRepository('PatologiaPaciente');
                $Criteria = new TCriteria;
                $Criteria->add(new TFilter('paciente_id', '=', $dados->paciente_id));
                $objects = $repo->load($Criteria);
               
                foreach($objects as $object) {
                    $this->datagrid->addItem($object);            
                }
            TTransaction::close();
        }
    }
    
    public function show($parms = null)
    {
        parent::show($parms);
    }
    
    public function getNomePatologia($id = null) 
    {
        TTransaction::open('db');
            $Patologia = new Patologia($id);
        TTransaction::close();
        return $Patologia->nome;    
    }
    
    function onDelete($obj = null) {
    
    parent::onDelete($obj);
    
    }
    
    public function onEdit(){}
    
    public static function onChangePaciente($obj) {
       AdiantiCoreApplication::gotoPage(__CLASS__, 'onReload', $obj);
    }
}





















