<?php

     const DIAS     = 28;
     const DIA_BASE = 14;
     const MEDICO   = 'Adriane Schneider';
     const CRM      = '32112';
     
class PacienteFormList extends TPage
{
    protected $form; // form
    protected $datagrid; // datagrid
    protected $pageNavigation;
    protected $loaded;
    public $Paciente;
    
    public function __construct( $param )
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Paciente');
        $this->form->setFormTitle('Cadastro de Pacientes');
       
        $id = new THidden('id');
        $nome = new TEntry('nome');
        
        $dataNasc = new TDate('dataNasc');
        $dataNasc->setValue('01/01/1950');
        $dataNasc->setMask('dd/mm/yyyy');
        
        $cartaoSus = new TEntry('cartaoSus');
        $rg = new TEntry('rg');
        $cpf = new TEntry('cpf');
        $cpf->setMask('000.000.000-00');
        $cpf->addValidation('CPF', new TCPFValidator);
        
        $dataEntrada = new TDate('dataEntrada');
        $dataEntrada->setMask('dd/mm/yyyy');
        $dataEntrada->setEditable(FALSE);
        $dataEntrada->setValue(date('d/m/Y'));
        
        $genero = new TCombo('genero');
        $genero->addItems(array('F' => 'Feminino', 'M' => 'Masculino'));
        $genero->setDefaultOption(false);
        
        $ativo = new TCombo('ativo');
        $ativo->addItems(array(1 => 'Ativo', 0 => 'Inativo'));
        $ativo->setDefaultOption(false);
        
        $alfabetizado = new TCombo('alfabetizado');
        $alfabetizado->addItems(array(1 => 'Sim', 0 => 'Não'));
        $alfabetizado->setDefaultOption(false);


        // add the fields
        $this->form->addFields([ $id ] );
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome ],[ new TLabel('Data nasc.') ], [ $dataNasc ] );
        $this->form->addFields( [ new TLabel('Cartao SUS') ], [ $cartaoSus ] ,[ new TLabel('RG') ], [ $rg ] );
        $this->form->addFields( [ new TLabel('CPF') ], [ $cpf ] , [ new TLabel('Cadastro') ], [ $dataEntrada ] );
        $this->form->addFields([ new TLabel('Genero') ], [ $genero ] , [ new TLabel('Ativo') ], [ $ativo ] );
        $this->form->addFields( [ new TLabel('Alfabetizado') ], [ $alfabetizado ] );

        $nome->addValidation('Nome', new TRequiredValidator);
        $dataNasc->addValidation('Data nasc.', new TRequiredValidator);
        $genero->addValidation('Genero', new TRequiredValidator);


        // set sizes
        $id->setSize('10%');
        $nome->setSize('75%');
        $dataNasc->setSize('75%');
        $cartaoSus->setSize('75%');
        $rg->setSize('75%');
        $cpf->setSize('75%');
        $dataEntrada->setSize('75%');
        $genero->setSize('75%');
        $ativo->setSize('75%');
        $alfabetizado->setSize('10%');

        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:eraser red');
       
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_dataNasc = new TDataGridColumn('dataNasc', 'Data nasc.', 'left');

        $column_cpf = new TDataGridColumn('cpf', 'CPF', 'left');
        $column_ativo = new TDataGridColumn('ativo', 'Ativo', 'left');
        $column_ativo->setTransformer(array($this, 'getNomeSituacao'));

        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_dataNasc);
        $this->datagrid->addColumn($column_cpf);
        $this->datagrid->addColumn($column_ativo);
        
        $action1 = new TDataGridAction([$this, 'onEdit']);
        $action1->setLabel(_t('Edit'));
        $action1->setImage('far:edit blue');
        $action1->setField('id');
        
        $action2 = new TDataGridAction([$this, 'onDelete']);
        $action2->setLabel(_t('Delete'));
        $action2->setImage('far:trash-alt red');
        $action2->setField('id');

        $action3 = new TDataGridAction([$this, 'goToPatologias']);
        $action3->setLabel('Patologias');
        $action3->setImage('fa: fa-bug red');
        $action3->setField('id');
        
        $action4 = new TDataGridAction([$this, 'onGenerateFolha']);
        $action4->setLabel('Folha cama');
        $action4->setImage('fa: fa-newspaper green');
        $action4->setField('id');
        
        $action5 =  new TDataGridAction([$this, 'onGenerateFolhaMedicacao']);
        $action5->setLabel('Folha medicação');
        $action5->setImage('fa: fa-newspaper green');
        $action5->setField('id');
       
        $actionGroup = new TDataGridActionGroup(null, 'fa:bars green');
        $actionGroup->addAction($action1);
        $actionGroup->addAction($action2);
        $actionGroup->addAction($action3);
        $actionGroup->addAction($action4);
        $actionGroup->addAction($action5);
        
        $this->datagrid->addActionGroup($actionGroup);
        $this->datagrid->createModel();
        
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($this->form);
        $container->add(TPanelGroup::pack('', $this->datagrid, $this->pageNavigation));
        parent::add($container);
    }
    
    public function onReload($param = NULL)
    {
        try
        {
            TTransaction::open('db');
            $repository = new TRepository('Paciente');
            $limit = 20;

            $criteria = new TCriteria;
            if (empty($param['order']))
            {
                $param['order'] = 'nome';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            $objects = $repository->load($criteria, FALSE);
            
            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    public static function onDelete($param)
    {
        $action = new TAction([__CLASS__, 'Delete']);
        $action->setParameters($param); // pass the key parameter ahead
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    public static function Delete($param)
    {
        try
        {
            $key = $param['key']; // get the parameter $key
            TTransaction::open('db'); // open a transaction with database
            $object = new Paciente($key, FALSE); // instantiates the Active Record
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
    
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('db'); // open a transaction
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            $data->nome = strtoupper($data->nome);
            $object = new Paciente;  // create an empty object
            
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            $data->id = $object->id;
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            $action = new TAction(array($this, 'onClear'));
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $action); // success message
            $this->onReload(); // reload the listing
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('db'); // open a transaction
                $object = new Paciente($key); // instantiates the Active Record
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
    
    public function getNomeSituacao($id) {
        $situacoes = [0 => 'Inativo', 1 => 'Ativo'];
        return $situacoes[$id];
    }
    
    public function onGenerateFolha($obj) {
        TTransaction::open('db');
        $Paciente = new Paciente($obj['id']);
        $designer = new TPDFDesigner;
        $designer->fromXml('app/reports/folha_paciente_cama.pdf.xml');
        $designer->replace('{nome}', utf8_decode($Paciente->nome));
        $designer->replace('{data_nasc}', $Paciente->dataNasc);
        $designer->replace('{idade}', $this->getIdade( $Paciente->dataNasc));
        $designer->replace('{cartao_sus}', $Paciente->cartaoSus);
        $designer->replace('{rg}', $Paciente->rg);
        $designer->replace('{cpf}', $Paciente->cpf);
        $patologias = implode(', ', $Paciente->getPatologias());
        $Responsavel = $Paciente->getResponsavel();
        TTransaction::close();
             
        if(count($Responsavel) > 0) {
            $Parentesco = $Responsavel[0]->getParentesco();
            $responsavel = $Responsavel[0]->nome.' ('.$Parentesco->nome.')';
            $fone_responsavel = $Responsavel[0]->telefone;
        }
        
        $designer->replace('{responsavel}', @$responsavel);
        $designer->replace('{fone_responsavel}', @$fone_responsavel);
        $designer->replace('{patologia}', utf8_decode($patologias));
        $this->form->setData($Paciente);    
        $designer->generate();
        $designer->save('app/output/folha_paciente.pdf');
        parent::openFile('app/output/folha_paciente.pdf');
    }
    
    public function show()
    {
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }
    
    public function getIdade($data) {
        list($dia, $mes, $ano) = explode('/', $data);

        $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
        $idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25);
        return $idade;
    }
    
    public function goToPatologias($obj) 
    {
        AdiantiCoreApplication::gotoPage('PatologiaPacienteListManual', 'onReload', $obj);
    }
    
    public function onGenerateFolhaMedicacao($obj) 
    {
        TTransaction::open('db');
        $this->Paciente = new Paciente($obj['id']);
        TTransaction::close();
        $table = new TTable;
        $table->border = 1;
        $table->cellpadding = 4;
        $table->style = 'border-collapse:collapse;';
        $table->width = "100%";
        $row = $table->addRow();
        $row->style='border:0px;';
        $cell = $row->addCell('<img src="./app/images/logo.png" width="30%">');
        $cell = $row->addCell('<b style="line-height:30px;"><font size="4">Residencial Geriátrico Recanto das Oliveiras CNPJ: 32.518.290/0001-56</font><br />
                                Rua Joaquim Nabuco, 320 - Jd. Aparecida Alvorada/RS CEP: 94856-610<br />        
                                Fone: 51 3101-2520</b>');
        $cell->colspan = 30;                        
        $row = $table->addRow();
        $row->style="border: 0px";
        $row->addCell('<b>Paciente : ' . $this->Paciente->nome . '</b>');
        $cell = $row->addCell('<b>DN : ' . $this->Paciente->dataNasc . '</b>');
        $cell->colspan = 6;
        $cell = $row->addCell('<b>Patologias : ' . implode(', ', $this->Paciente->getPatologias()) . '</b>');
        $cell->colspan = 15;
        
        $cell = $row->addCell('<b>'.$this->getPeriodo().'</b>');
        $cell->colspan = 8;
        $cell->style="border-right: 1px solid black;";
                
        $row = $table->addRow();
        $cell = $row->addCell('<b>Médico(a): ' . MEDICO . '</b>');
        $cell->style ="border: 0px";
        $cell = $row->addCell('<b>CRM: ' . CRM . '</b>');
        $cell->style ="border: 0px";
        $cell->colspan = 30;
        $row = $table->addRow();
        
        foreach ($this->getCabecalhoTabela() as $coluna) {
            $cell = $row->addCell($coluna);
            $cell->align = "center";
            $cell->style = "font-weight: bold; border: 1px solid black;";
        }

        foreach ($this->Paciente->getMedicamentosPaciente() as $Medicamento) {
            TTransaction::open('db');

            $row = $table->addRow();
            $conteudo = $Medicamento->get_medicamento()->nome;
            $conteudo .= ' ';
            $conteudo .= $Medicamento->miligramas .'MG ' ;
            $conteudo .= $Medicamento->quantidade . $Medicamento->get_medicamento()->getMedida()->sigla;
            $conteudo .= $Medicamento->sn === 'true' ? ' (SN) ' : '';
            $cell = $row->addCell( $conteudo );
            $cell->style = "font-weight: bold; border: 1px solid black;";

            for($i = 0; $i < DIAS; $i++) {
                $cell = $row->addCell($Medicamento->hora);
                $cell->style = "border: 1px solid black;";
                $cell->align = "center";
            }
            TTransaction::close();
        }
        
        $this->onGenerateArquivoFolhaMedicacao($table);
    }
   
    private function getCabecalhoTabela()
    {
        $diasMes = DIAS;    
        $diaBase = DIA_BASE; 
        $dias[] = 'Medicamento';

        for($x = 1; $x <= $diasMes; $x++) {
            if($diaBase > $diasMes) {
                $diaBase = 1;
            }
            $dias[] = $diaBase++;
        }
        return $dias;
    }
    
    private function onGenerateArquivoFolhaMedicacao($tabela) 
    {
        $tabela = "<div style=\"font-family: Arial, Helvetica, sans-serif;\">$tabela</div>";
        $html = new AdiantiHTMLDocumentParser('app/output/folha_medicacao.html');
        $html = AdiantiHTMLDocumentParser::newFromString($tabela);
        $document = 'tmp/'.uniqid().'.html'; 
        $html->save($document, 'A4', 'landscape');
        parent::openFile($document);
    }
    
    public function goToFolhaMedicacao($id) 
    {
        AdiantiCoreApplication::gotoPage('FolhaReceitaPaciente', '', $id);
    }
    
    public function getPeriodo() 
    {
        $nomeMes  = [1 => 'JAN', 2 => 'FEV', 3 => 'MAR', 4 => 'ABR', 5 => 'MAI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO', 9 => 'SET', 10 => 'OUT', 11 => 'NOV', 12 => 'DEZ'];
        $mes = +date('m');
        
        if(+date('d') > DIA_BASE) {
            $mes++;
        }
        return $nomeMes[$mes].'/'.$nomeMes[$mes+1].'/'.date('y');
   }
}
