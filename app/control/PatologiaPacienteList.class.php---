<?php
/**
 * PatologiaPacienteList Listing
 * @author  <your name here>
 */
class PatologiaPacienteList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    public function __construct()
    {
        parent::__construct();
        $this->setDatabase('db');            // defines the database
        $this->setActiveRecord('PatologiaPaciente');   // defines the active record
        $this->setDefaultOrder('id', 'asc');         // defines the default order
        $this->setLimit(100);

        $this->addFilterField('paciente_id', '=', 'paciente_id'); // filterField, operator, formField
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_PatologiaPaciente');
        $this->form->setFormTitle('Patologias do paciente');
        

        // create the form fields
        $paciente_id = new TDBCombo('paciente_id', 'db', 'Paciente', 'id', 'nome', 'nome');

        // add the fields
        $this->form->addFields( [ new TLabel('Paciente') ], [ $paciente_id ] );

        // set sizes
        $paciente_id->setSize('100%');

        // keep the form filled during navigation with session data
        //$this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['PatologiaPacienteForm', 'onEdit']), 'fa:plus green');
        
        $this->createDatagrid();
        
        $panel = new TPanelGroup('', 'white');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
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
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
    
    public function createDatagrid () {
            // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';

        $column_patologia_id = new TDataGridColumn('patologia_id', 'Patologia', 'left');
        $column_patologia_id->setTransformer([$this, 'getNomePatologia']);
        // add the columns to the DataGrid
        
        $this->datagrid->addColumn($column_patologia_id);
        $action1 = new TDataGridAction(['PatologiaPacienteForm', 'onEdit'], ['id'=>'{id}']);
        $action2 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}']);
        
        $this->datagrid->addAction($action1, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action2 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
    
    }
    
    public function getNomePatologia($id = null) {
        TTransaction::open('db');
            $Patologia = new Patologia($id);
        TTransaction::close();
        return $Patologia->nome;    
    }
    
    public function onReload($param = null) {
        $data = $this->form->getData();
        $data->paciente_id = $data->paciente_id;
        $this->form->setData($data);
        $this->datagrid->clear();
        $this->datagrid->createModel();

    }

    public function show() {
        
        new TMessage('info', __FUNCTION__);
        parent::show();
    }
 
}
