<?php
/**
 * MedicamentoPacienteList Listing
 * @author  <your name here>
 */
class MedicamentoPacienteList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('db');            // defines the database
        $this->setActiveRecord('MedicamentoPaciente');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        $this->setLimit(100);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('paciente_id', '=', 'paciente_id'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_MedicamentoPaciente');
        $this->form->setFormTitle('Medicamentos para Paciente');

        $paciente_id = $this->getComboPacientesAtivos();
        $this->form->addFields( [ new TLabel('Paciente') ], [ $paciente_id ] );
        $paciente_id->setSize('100%');

        // keep the form filled during navigation with session data
       // $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['MedicamentoPacienteForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');

        // creates the datagrid columns
        $column_medicamento_id = new TDataGridColumn('medicamento_id', 'Medicamento', 'left');
        $column_medicamento_id->setTransformer(array($this, 'getNomeMedicamento'));
        $column_miligramas = new TDataGridColumn('miligramas', 'Miligramas', 'left');
        $column_hora = new TDataGridColumn('hora', 'Hora', 'left');
        $column_quantidade = new TDataGridColumn('quantidade', 'Quantidade', 'left');
        $column_quantidade->setTransformer(array($this, 'getMedida'), $column_medicamento_id);

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_medicamento_id);
        $this->datagrid->addColumn($column_miligramas);
        $this->datagrid->addColumn($column_quantidade);
        $this->datagrid->addColumn($column_hora);

        // creates the datagrid column actions
        $column_medicamento_id->setAction(new TAction([$this, 'onReload']), ['order' => 'medicamento_id']);
        $column_hora->setAction(new TAction([$this, 'onReload']), ['order' => 'hora']);

        
        $action1 = new TDataGridAction(['MedicamentoPacienteForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('', 'white');
        
        if(trim($this->form->getData()->paciente_id)) {
            $panel->add($this->datagrid);
            $panel->addFooter($this->pageNavigation);
        }
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
    
    public function getNomeMedicamento($id = null) {
        TTransaction::open('db');
        $Medicamento = new Medicamento($id);
        return $Medicamento->nome;
        TTransaction::close();
    }
    
    public function getMedida($id = null, $medicamento = null) {
        TTransaction::open('db');
        $Medicamento = new Medicamento($medicamento->medicamento_id);
        return $id.'  '.$Medicamento->getMedida()->sigla;
        TTransaction::close();
    }
    
    public function getComboPacientesAtivos() {
        TTransaction::open('db');
        $repo = new TRepository('Paciente');
        $Criteria = new TCriteria;
        $Criteria->add(new TFilter('ativo', '=', '1'));
        $obj = $repo->load($Criteria);
        TTransaction::close();
        $combo = new TCombo('paciente_id');
        $array = array();
        foreach($obj as $o) {
            $array[$o->id] = $o->nome;
        }
        $combo->addItems($array);
        return $combo;
    }
}


