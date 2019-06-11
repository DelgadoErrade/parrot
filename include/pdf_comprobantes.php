<?php
include_once("cls_parrot.php");
include_once("fpdf.php");
include_once("funciones_fecha.php");
include_once("form_items.php");
class pdfComprobante extends FPDF
{
	var $parrot;
	var $id_comprobante;
	var $registro;
	function __construct($comprobante){
		//$this->FPDF('L','mm',array(138.7,215.9));// Ver tamaño de media págica carta.
		$this->FPDF('P','mm','Letter');
		$this->SetMargins(20,20,20);
		$this->SetAutoPageBreak(FALSE);
		$this->parrot = new CLS_PARROT();
		$this->registro = $this->parrot->comprobantesRecords("n_comprobante = $comprobante");
		foreach($this->registro as $fila){
			extract($fila);
			$this->id_comprobante = $id_comprobantes;
		}	
	}
	function Header()
	{	
		$this->Image('../imagenes/parrotLogo.jpg',20,10,70,30, 'JPG');
	    $this->SetFont('Times','B',20);
	    $this->Cell(180,10,'COMPROBANTE DE PAGO', 0, 0, 'R');
	    $this->Ln(10);
	    //$numero = 84;
	    foreach($this->registro as $fila){
	    	extract($fila);
		}		    
	    $this->cell(179,10, "No.   ".$n_comprobante,0,0,"R");
	    $this->Line(20, 40, 200,40);
	    $this->Line(20, 41, 200,41);
	    $this->Line(20, 42, 200,42);
	}
	/*function Footer()
	{
	    $this->SetY(-25);
	    $this->SetFont('Times','B',10);
	    $this->Ln(5);
	    $this->Cell(25,8,"Cancelado por:", 0, 0);
	    foreach($this->registro as $fila){
	    	extract($fila);
		}
		$this->SetFont('');	// Elimina el Bold
	    $this->Cell(70,8,$cancela, 1,0,'C');
		$this->SetFont('Times','B',10);
	    $this->Cell(15,8,"Fecha:", 0, 0,"R");
	    $fecha = dMySQL_ES($fecha_cancela);
	    $this->Cell(70,8,$fecha, 1,0,'C');
	}*/	
	function body(){
	    foreach($this->registro as $fila){
	    	extract($fila);
		}			
		$this->SetFont('Times','B',10);
		$this->Ln(15);
		$this->Cell(25,8,"Beneficiario:", 0, 0);
	    $this->Cell(155,8,$beneficiario, 1,0,'C');
	    $this->Ln(10);
	    //$this->SetFont('Times','',10);
	    $this->Cell(15,8,"Factura:", 0, 0,"R");
	    $this->SetFont('Times','',10);
	    $this->Cell(70,8,$factura, 1,0,'C');
	    $this->SetFont('Times','B',10);
	    $this->Cell(25,8,"De fecha:", 0, 0,"R");
	    $this->SetFont('Times','',10);
	    $fecha = dMySQL_ES($fecha_factura);
	    $this->Cell(70,8,$fecha, 1,0,'C');
	    $this->Ln(10);
	    $this->cell(40);
		$this->SetFont('Times','B',10);
	    $this->Cell(40,8,"Forma de pago", 1,0,'C');
	    $this->Cell(40,8,"Fuente", 1,0,'C');
	    $this->Cell(20,8,"Ref. No.", 1,0,'C');
	    $this->Cell(20,8,"Monto", 1,0,'C');
	    //$this->Ln(8);
	    $this->SetFont('Times','',10);
	    $this->cell(40);
	    $bancoTrans = "";
	    $montoEfectivo = "";
	    $montoDebito = "";
	    $montoTransf = "";
	    $montoCheque = "";
	    $fuenteTransferencia = "";
	    $refDebito = "";
	    $refTrans = "";
	    $refCheque = "";
	    $registros = $this->parrot->pagosRecords("id_comprobantes = " . $this->id_comprobante);
	    $total = 0;
		foreach($registros as $registro){
			extract($registro);
			$total +=  $monto_pago;
			$this->Ln(8);
			switch($forma_pago){
				case "Efectivo":
					$montoEfectivo = numeroEspanol($monto_pago);
					$this->cell(40);
	    $this->Cell(40,8,"EFECTIVO", 1,0,'L');
	    $this->Cell(40,8,"CAJA", 1,0,'L');
	    $this->Cell(20,8,"", 1,0,'C');
	    $this->Cell(20,8,$montoEfectivo, 1,0,'R');
	    //$this->Ln(8);
					break;
				case "Debito":
					$refDebito = $referencia;
					$montoDebito = numeroEspanol($monto_pago);
	    $this->cell(40);
	    $this->Cell(40,8,"DEBITO", 1,0,'L');
	    $this->Cell(40,8,"BANESCO", 1,0,'L');
	    $this->Cell(20,8,$refDebito, 1,0,'C');
	    $this->Cell(20,8,$montoDebito, 1,0,'R');
	    //$this->Ln(8);
					break;
				case "Transferencia":
					$refTrans = $referencia;
					$montoTransf = numeroEspanol($monto_pago);
					$bancoTrans = strtoupper($banco);
	    $this->cell(40);
	    $this->Cell(40,8,"TRANSFERENCIA", 1,0,'L');
	    $this->Cell(40,8,$bancoTrans, 1,0,'L');
	    $this->Cell(20,8,$refTrans, 1,0,'C');
	    $this->Cell(20,8,$montoTransf, 1,0,'R');
	    //$this->Ln(8);
					break;
				case "Cheque":
					$refCheque = $referencia;
					$montoCheque = numeroEspanol($monto_pago);
	    $this->cell(40);
	    $this->Cell(40,8,"CHEQUE", 1,0,'L');
	    $this->Cell(40,8,"MERCANTIL", 1,0,'L');
	    $this->Cell(20,8,$refCheque, 1,0,'C');
	    $this->Cell(20,8,$montoCheque, 1,0,'R');
	    //$this->Ln(8);					
					break;		
			}
		}
	     $this->Ln(8);
	    $this->cell(40);
	    $this->SetFont('Times','B',10);
	    $this->Cell(100,8,"TOTAL", 1,0,'R');
	    $this->SetFont('Times','',10);
	    $this->Cell(20,8,numeroEspanol($total), 1,0,'R');
	    $this->SetFont('Times','B',10);
	    //$this->Ln(5);
		$this->Ln(10);
	    $this->Cell(25,8,"Cancelado por:", 0, 0);
	    foreach($this->registro as $fila){
	    	extract($fila);
		}
		$this->SetFont('');	// Elimina el Bold
	    $this->Cell(70,8,$cancela, 1,0,'C');
		$this->SetFont('Times','B',10);
	    $this->Cell(15,8,"Fecha:", 0, 0,"R");
	    $this->SetFont('Times','',10);
	    $fecha = dMySQL_ES($fecha_cancela);
	    $this->Cell(70,8,$fecha, 1,0,'C');	    
	}
}
sleep(3);
$numero = $_COOKIE["id"];
$reporte = new pdfComprobante($numero);
$reporte->AddPage();
$reporte->body();
$reporte->SetDisplayMode("default");
$reporte->Output();
$reporte->close();

?>