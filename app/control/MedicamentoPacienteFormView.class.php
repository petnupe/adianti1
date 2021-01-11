<?php
/**
 * MedicamentoPacienteFormView Form
 * @author  <your name here>
 */
class MedicamentoPacienteFormView extends TPage
{
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        
        $this->form = new BootstrapFormBuilder('form_MedicamentoPaciente_View');
        
        $this->form->setFormTitle('MedicamentoPaciente');
        $this->form->setColumnClasses(2, ['col-sm-3', 'col-sm-9']);
        $this->form->addHeaderActionLink( _t('Print'), new TAction([$this, 'onPrint'], ['key'=>$param['key'], 'static' => '1']), 'far:file-pdf red');
        //$this->form->addHeaderActionLink( _t('Edit'), new TAction(['MedicamentoPacienteForm', 'onEdit'], ['key'=>$param['key'], 'register_state'=>'true']), 'far:edit blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    /**
     * Show data
     */
    public function onEdit( $param )
    {
        try
        {
            TTransaction::open('db');
        
            $object = new MedicamentoPaciente($param['key']);
            
            $label_id = new TLabel('Id:', '#333333', '', 'B');
            $label_medicamento_id = new TLabel('Medicamento Id:', '#333333', '', 'B');
            $label_paciente_id = new TLabel('Paciente Id:', '#333333', '', 'B');
            $label_quantidade = new TLabel('Quantidade:', '#333333', '', 'B');
            $label_hora = new TLabel('Hora:', '#333333', '', 'B');
            $label_miligramas = new TLabel('Miligramas:', '#333333', '', 'B');

            $text_id  = new TTextDisplay($object->id, '#333333', '', '');
            $text_medicamento_id  = new TTextDisplay($object->medicamento_id, '#333333', '', '');
            $text_paciente_id  = new TTextDisplay($object->paciente_id, '#333333', '', '');
            $text_quantidade  = new TTextDisplay($object->quantidade, '#333333', '', '');
            $text_hora  = new TTextDisplay($object->hora, '#333333', '', '');
            $text_miligramas  = new TTextDisplay($object->miligramas, '#333333', '', '');

            $this->form->addFields([$label_id],[$text_id]);
            $this->form->addFields([$label_medicamento_id],[$text_medicamento_id]);
            $this->form->addFields([$label_paciente_id],[$text_paciente_id]);
            $this->form->addFields([$label_quantidade],[$text_quantidade]);
            $this->form->addFields([$label_hora],[$text_hora]);
            $this->form->addFields([$label_miligramas],[$text_miligramas]);

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Print view
     */
    public function onPrint($param)
    {
        try
        {
            $this->onEdit($param);
            
            // string with HTML contents
            $html = clone $this->form;
            $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();
            
            // converts the HTML template into PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($contents);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $file = 'app/output/MedicamentoPaciente-export.pdf';
            
            // write and open file
            file_put_contents($file, $dompdf->output());
            
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $file.'?rndval='.uniqid();
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $window->add($object);
            $window->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
