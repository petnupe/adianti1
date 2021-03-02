<?php

    const DIAS     = 28;
    const DIA_BASE = 14;
    const MEDICO   = 'Adriane Schneider';
    const CRM      = '32112';

class FolhaReceitaPaciente extends TPage
{
    private Paciente $Paciente;
    
    public function __construct($param = null) 
    {
        parent::__construct();
        $this->getPaciente($param['key']);
        
        $table = new TTable;
        $table->border = 1;
        $table->cellpadding = 4;
        $table->style = 'border-collapse:collapse;';
        $table->width = "100%";

        $row = $table->addRow();
        $row->style='border:0px;';
        $cell = $row->addCell('<img src="./app/images/logo.png" width="50%">');
        $cell = $row->addCell('<b><font size="4">Residencial Geriátrico Recanto das Oliveiras CNPJ: 32.518.290/0001-56</font><br />
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
        $cell = $row->addCell('<b>JAN/FEV/21</b>');
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
        
        //parent::add($table);
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
        $html = new AdiantiHTMLDocumentParser('app/output/folha_medicacao.html');
        $html = AdiantiHTMLDocumentParser::newFromString($tabela);
        $document = 'tmp/'.uniqid().'.html'; 
        $html->save($document, 'A4', 'landscape');
        parent::openFile($document);
    }
}