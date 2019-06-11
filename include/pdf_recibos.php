<?php
include_once("cls_parrot.php");
include_once("fpdf.php");
include_once("funciones_fecha.php");
include_once("form_items.php");
class pdf_recibos extends FPDF
{
	var $parrot;
	var $id_quincenas;
	var $registro;
	var $sql1;
	var $sql2;
	var $sql3;
	var $n_recibo;
	var $desde;
	var $hasta;
	
	function __construct($id_quincenas){
		//$this->FPDF('L','mm',array(138.7,215.9));// Ver tamaño de media págica carta.
		$this->FPDF('P','mm','Letter');
		$this->SetMargins(20,10,20);
		$this->SetAutoPageBreak(FALSE);
		$this->parrot = new CLS_PARROT();
		$this->registro = $this->parrot->quincenasRecords("id_quincenas = $id_quincenas");
		foreach($this->registro as $fila){
			extract($fila);
			$this->id_quincenas = $id_quincenas;
			$this->n_recibo = $n_recibo;
			$this->desde = dMySQL_ES($desde);
			$this->hasta = dMySQL_ES($hasta);
		}	
	//		CONSULTAS.
		$this->sql1 = "SELECT cedula, apellidos, nombres, fecha_ingreso, cargo, sueldo_mensual  FROM empleados INNER JOIN 
			(SELECT cargo, sueldo_mensual FROM sueldos WHERE cedula = $cedula ORDER BY fecha DESC LIMIT 0,1) AS X
	  		WHERE cedula = $cedula";
	  	$this->sql2 = "SELECT fecha, desde, hasta, tipo, descripcion, dias, monto
			FROM quincenas INNER JOIN nomina ON quincenas.id_quincenas = nomina.id_quincena
			INNER JOIN asig_dedu ON nomina.id_asig_dedu = asig_dedu.id_asig_dedu WHERE id_quincenas = $id_quincenas";
		$this->sql3 = "SELECT forma_pago, banco, referencia, monto_pago FROM pagos
			INNER JOIN comprobantes ON comprobantes.id_comprobantes = pagos.id_comprobantes
			WHERE n_comprobante = $n_comprobante";	
	}
	function Header()
	{	
		$this->Image('../imagenes/parrotLogo.jpg',17,5,60,25, 'JPG');
	    $this->SetFont('Times','B',20);
	    $this->Cell(180,10,'RECIBO DE PAGO', 0, 0, 'R');
	    $this->Ln(10);
	    $this->cell(179,10, "No.   ".$this->n_recibo,0,0,"R");
	}
	
	function body(){
		$consulta1 = $this->parrot->consultagenerica($this->sql1);
	    foreach($consulta1 as $fila){
	    	extract($fila);
		}			
		$this->SetFont('Times','B',10);
		$this->Ln(10);
		$this->Cell(35,6,"Nombres y Apellidos:", 1,0,'C');
	    $this->Cell(95,6,"$nombres $apellidos", 1,0,'C');
	    $this->Cell(25,6,"Cedula", 1,0,'C');
	    $this->Cell(25,6,numeroEspanol($cedula,0), 1,0,'C');
	    $this->Ln(6);
	    $this->Cell(35,6,"Fecha de Ingreso", 1,0,'C');
		$this->SetFont('Times','',10);
	    $this->Cell(25,6,dMySQL_ES($fecha_ingreso), 1,0,'C');
		$this->SetFont('Times','B',10);
	    $this->Cell(50,6,"Cargo", 1,0,'C');
		$this->SetFont('Times','',10);
	    $this->Cell(70,6,"$cargo", 1,0,'C');
	    $this->Ln(6);
		$this->SetFont('Times','B',10);
	    $this->Cell(35,6,"Periodo Desde:", 1,0,'C');
		$this->SetFont('Times','',10);
	    $this->Cell(25,6,$this->desde, 1,0,'C');
		$this->SetFont('Times','B',10);
	    $this->Cell(25,6,"Hasta", 1,0,'C');
		$this->SetFont('Times','',10);
	    $this->Cell(25,6,$this->hasta, 1,0,'C');
		$this->SetFont('Times','B',10);
	    $this->Cell(35,6,"Sueldo Mensual", 1,0,'C');
		$this->SetFont('Times','',10);
	    $this->Cell(35,6,numeroEspanol($sueldo_mensual), 1,0,'C');
	    $this->Ln(6);
		$this->SetFont('Times','B',10);
	    $this->Cell(60,6,"Descripcion", 1,0,'C');
	    $this->Cell(25,6,"Diario", 1,0,'C');
	    $this->Cell(25,6,"Dias", 1,0,'C');
	    $this->Cell(35,6,"Asignaciones", 1,0,'C');
	    $this->Cell(35,6,"Deducciones", 1,0,'C');
		$this->SetFont('Times','',10);
	    $consulta2 =  $this->parrot->consultagenerica($this->sql2);
	    $sumaAsignaciones = 0;
	    $sumaDeducciones = 0;
	    foreach($consulta2 as $fila){
	    	extract($fila);
	    	if($tipo == "ASIGNACION"){
	    		$asignacion = numeroEspanol($monto * $dias);
	    		$sumaAsignaciones += $monto * $dias;
	    		$deduccion = "";
			}else{
				$deduccion = numeroEspanol($monto * $dias);
				$sumaDeducciones += $monto * $dias;
				$asignacion = "";
			}
		    $this->Ln(6);
		    $this->Cell(60,6,"$descripcion", 1,0,'L');
		    $this->Cell(25,6,numeroEspanol($monto), 1,0,'C');
		    $this->Cell(25,6,"$dias", 1,0,'C');
		    $this->Cell(35,6,"$asignacion", 1,0,'R');
		    $this->Cell(35,6,"$deduccion", 1,0,'R');	
		}	
		$this->Ln(6);
		$this->SetFont('Times','B',10);
		$this->Cell(110,6,"Total Asignaciones y deducciones", 1,0,'L');
		$this->SetFont('Times','',10);
	    $this->Cell(35,6,numeroEspanol($sumaAsignaciones), 1,0,'R');
	    $this->Cell(35,6,numeroEspanol($sumaDeducciones), 1,0,'R'); 
	    $this->Ln(6);
		$this->SetFont('Times','B',10);
		$this->Cell(145,6,"Total a Cancelar en Bs.", 1,0,'R');
		$this->SetFont('Times','',10);
	    $this->Cell(35,6,numeroEspanol($sumaAsignaciones-$sumaDeducciones), 1,0,'R'); 
	    $this->Ln(6);
		$this->SetFont('Times','B',10);
	    $this->Cell(35,6,"Forma de pago", 1,0,'C');
	    $this->Cell(25,6,"Fuente", 1,0,'C');
	    $this->Cell(25,6,"Referencia", 1,0,'C');
	    $this->Cell(25,6,"Monto", 1,0,'C');
		$this->SetFont('Times','',10);
	    $this->Cell(70,6,"Recibi Conforme","R",0,'C');
	    $consulta3 = $this->parrot->consultagenerica($this->sql3);
	    $n = count($consulta3);
	    $i = 0;
	    foreach($consulta3 as $fila){
			extract($fila);	
			$this->Ln(6);
			$this->Cell(35,6,"$forma_pago", 1,0,'L');
		    $this->Cell(25,6,"$banco", 1,0,'L');
		    $this->Cell(25,6,"$referencia", 1,0,'C');
		    $this->Cell(25,6,numeroEspanol($monto_pago), 1,0,'R');
		    $this->Cell(70,6,"","R" ,0,'C');	
		}
		if($n==1){
			$this->Ln(6);
			$this->Cell(35,6,"", 1,0,'C');
		    $this->Cell(25,6,"", 1,0,'C');
		    $this->Cell(25,6,"", 1,0,'C');
		    $this->Cell(25,6,"", 1,0,'C');
		    $this->Cell(70,6,"","R" ,0,'C');
		}
		$this->Ln(6);
		$this->SetFont('Times','B',10);
		$this->Cell(35,6,"Fecha de pago", 1,0,'C');
		$this->SetFont('Times','',10);
	    $this->Cell(25,6,dMySQL_ES($fecha), 1,0,'C');
		$this->SetFont('Times','B',10);
	    $this->Cell(25,6,"Total", 1,0,'C');
		$this->SetFont('Times','',10);
		$this->Cell(25,6,numeroEspanol($sumaAsignaciones-$sumaDeducciones), 1,0,'R');	
	    $this->Cell(70,6,"C.I.:____________________","BR" ,0,'C');	
	}
}
//sleep(1);
$numero = $_COOKIE["id_quincenas"];
//$numero = 84;
$reporte = new pdf_recibos($numero);
$reporte->AddPage();
$reporte->body();
$reporte->SetDisplayMode("default");
$reporte->Output();

?>