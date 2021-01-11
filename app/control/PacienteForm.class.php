<?php
/**
 * PacienteForm Master/Detail
 * @author  <your name here>
 */
class PacienteForm extends TPage
{
    protected $form; // form
    protected $detail_list;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Paciente');
        $this->form->setFormTitle('Paciente');
        
        // master fields
        $nome = new TDBCombo('nome', 'db', 'Paciente', 'id', 'nome');

        // detail fields
        $detail_uniqid = new THidden('detail_uniqid');
        $detail_id = new THidden('detail_id');
        
        $detail_medicamento_id = new TDBCombo('detail_medicamento_id', 'db', 'Medicamento', 'id', 'nome');
        $detail_medicamento_id->setChangeAction(new TAction(array($this, 'onChangeMedicamento')));
        
        $detail_quantidade = new TEntry('detail_quantidade');
        $detail_quantidade->setSize('30%');
        $detail_hora = new TEntry('detail_hora');
        $detail_hora->setSize('20%');
        $detail_miligramas = new TEntry('detail_miligramas');
        $detail_miligramas->setSize('30%');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        // master fields
        $this->form->addFields( [new TLabel('Nome')], [$nome] );
        
        // detail fields
        $this->form->addContent( ['<h4>Medicamentos</h4><hr>'] );
        $this->form->addFields( [$detail_uniqid] );
        $this->form->addFields( [$detail_id] );
        
        $this->form->addFields( [new TLabel('Medicamento')], [$detail_medicamento_id] );
        $this->form->addFields( [], [$detail_miligramas],[new TLabel('MGs')] );        
        
        $lbQuantidade = new TLabel('Quantidade');
        $lbQuantidade->setId('lbQuantidade');
        
        $this->form->addFields([], [$detail_quantidade] ,[$lbQuantidade] );
        $this->form->addFields( [new TLabel('Hora')], [$detail_hora] );


        $add = TButton::create('add', [$this, 'onDetailAdd'], 'Register', 'fa:plus-circle green');
        $add->getAction()->setParameter('static','1');
        $this->form->addFields( [], [$add] );
        
        $this->detail_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->detail_list->setId('MedicamentoPaciente_list');
        $this->detail_list->generateHiddenFields();
        $this->detail_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        
        // items
        $this->detail_list->addColumn( new TDataGridColumn('uniqid', 'Uniqid', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('id', 'Id', 'center') )->setVisibility(false);
        $this->detail_list->addColumn( new TDataGridColumn('medicamento_id', 'Medicamento Id', 'left', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('quantidade', 'Quantidade', 'left', 100) );
        $this->detail_list->addColumn( new TDataGridColumn('hora', 'Hora', 'left', 100) );

        // detail actions
        $action1 = new TDataGridAction([$this, 'onDetailEdit'] );
        $action1->setFields( ['uniqid', '*'] );
        
        $action2 = new TDataGridAction([$this, 'onDetailDelete']);
        $action2->setField('uniqid');
        
        // add the actions to the datagrid
        $this->detail_list->addAction($action1, _t('Edit'), 'fa:edit blue');
        $this->detail_list->addAction($action2, _t('Delete'), 'far:trash-alt red');
        
        $this->detail_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->detail_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
        
        $this->form->addAction( 'Save',  new TAction([$this, 'onSave'], ['static'=>'1']), 'fa:save green');
        $this->form->addAction( 'Clear', new TAction([$this, 'onClear']), 'fa:eraser red');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    static public function onChangeMedicamento($obj = null) {
         TTransaction::open('db');
         $Medicamento = new Medicamento($obj['detail_medicamento_id']);
         
         //var_dump();
         
         TScript::create("$('#lbQuantidade').html('".$Medicamento->getMedida()->sigla."');");
         TTransaction::close();
    }
    
    /**
     * Clear form
     * @param $param URL parameters
     */
    public function onClear($param)
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Add detail item
     * @param $param URL parameters
     */
    public function onDetailAdd( $param )
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            /** validation sample
            if (empty($data->fieldX))
            {
                throw new Exception('The field fieldX is required');
            }
            **/
            
            $uniqid = !empty($data->detail_uniqid) ? $data->detail_uniqid : uniqid();
            
            $grid_data = [];
            $grid_data['uniqid'] = $uniqid;
            $grid_data['id'] = $data->detail_id;
            $grid_data['medicamento_id'] = $data->detail_medicamento_id;
            $grid_data['quantidade'] = $data->detail_quantidade;
            $grid_data['hora'] = $data->detail_hora;
            
            // insert row dynamically
            $row = $this->detail_list->addItem( (object) $grid_data );
            $row->id = $uniqid;
            
            TDataGrid::replaceRowById('MedicamentoPaciente_list', $uniqid, $row);
            
            // clear detail form fields
            $data->detail_uniqid = '';
            $data->detail_id = '';
            $data->detail_medicamento_id = '';
            $data->detail_quantidade = '';
            $data->detail_hora = '';
            
            // send data, do not fire change/exit events
            TForm::sendData( 'form_Paciente', $data, false, false );
        }
        catch (Exception $e)
        {
            $this->form->setData( $this->form->getData());
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Edit detail item
     * @param $param URL parameters
     */
    public static function onDetailEdit( $param )
    {
        $data = new stdClass;
        $data->detail_uniqid = $param['uniqid'];
        $data->detail_id = $param['id'];
        $data->detail_medicamento_id = $param['medicamento_id'];
        $data->detail_quantidade = $param['quantidade'];
        $data->detail_hora = $param['hora'];
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Paciente', $data, false, false );
    }
    
    /**
     * Delete detail item
     * @param $param URL parameters
     */
    public static function onDetailDelete( $param )
    {
        // clear detail form fields
        $data = new stdClass;
        $data->detail_uniqid = '';
        $data->detail_id = '';
        $data->detail_medicamento_id = '';
        $data->detail_quantidade = '';
        $data->detail_hora = '';
        
        // send data, do not fire change/exit events
        TForm::sendData( 'form_Paciente', $data, false, false );
        
        // remove row
        TDataGrid::removeRowById('MedicamentoPaciente_list', $param['uniqid']);
    }
    
    /**
     * Load Master/Detail data from database to form
     */
    public function onEdit($param)
    {
        try
        {
            TTransaction::open('db');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Paciente($key);
                $items  = MedicamentoPaciente::where('paciente_id', '=', $key)->load();
                
                foreach( $items as $item )
                {
                    $item->uniqid = uniqid();
                    $row = $this->detail_list->addItem( $item );
                    $row->id = $item->uniqid;
                }
                $this->form->setData($object);
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the Master/Detail data from form to database
     */
    public function onSave($param)
    {
        try
        {
            // open a transaction with database
            TTransaction::open('db');
            
            $data = $this->form->getData();
            $this->form->validate();
            
            $master = new Paciente;
            $master->fromArray( (array) $data);
            $master->store();
            
            MedicamentoPaciente::where('paciente_id', '=', $master->id)->delete();
            
            if( $param['MedicamentoPaciente_list_medicamento_id'] )
            {
                foreach( $param['MedicamentoPaciente_list_medicamento_id'] as $key => $item_id )
                {
                    $detail = new MedicamentoPaciente;
                    $detail->medicamento_id  = $param['MedicamentoPaciente_list_medicamento_id'][$key];
                    $detail->quantidade  = $param['MedicamentoPaciente_list_quantidade'][$key];
                    $detail->hora  = $param['MedicamentoPaciente_list_hora'][$key];
                    $detail->paciente_id = $master->id;
                    $detail->store();
                }
            }
            TTransaction::close(); // close the transaction
            
            TForm::sendData('form_Paciente', (object) ['id' => $master->id]);
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback();
        }
    }
    
    public function getNomeMedicamento($id) {
    
    TTransaction::open('db');
        
        $Medicamento = new Medicamento($id);
        
        print_r($Medicamento);
        
        //return
    TTransaction::close();
    
    
    }
}
