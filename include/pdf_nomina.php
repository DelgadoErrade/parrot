<?php
include_once("cls_parrot.php");
include_once("fpdf.php");
include_once("funciones_fecha.php");
include_once("form_items.php");
class pdf_nomina extends FPDF
{
	var $parrot;
	var $fecha;
	var $registros;
	
	function __construct($fecha){
		$this->FPDF('L','mm','Letter');
		$this->SetMargins(20,20,20);
		$this->SetAutoPageBreak(FALSE);
		$this->parrot = new CLS_PARROT();
		//$fecha = d_ES_MYSQL($fecha);
		$this->fecha = $fecha;
		$sql = "SELECT  x.cedula, CONCAT(apellidos,', ',nombres) AS nombre, 
			x.quincena*2 AS sueldo, 
			x.quincena, x.transporte, x.alimentacion, 
			quincena+transporte+alimentacion AS sumaA,
			FAOV, RPE, IVSS,  FAOV + RPE + IVSS AS sumaD,
            quincena+transporte+alimentacion - ( FAOV + RPE + IVSS) as total FROM 
			(SELECT cedula, fecha, 
			SUM(CASE WHEN id_asig_dedu = 1 THEN dias * monto ELSE 0 END) AS 	quincena,
			SUM(CASE WHEN id_asig_dedu = 2 THEN dias * monto ELSE 0 END) AS 	transporte,
			SUM(CASE WHEN id_asig_dedu = 3 THEN dias * monto ELSE 0 END) AS 	alimentacion,
			SUM(CASE WHEN id_asig_dedu = 4 THEN dias * monto ELSE 0 END) AS 	FAOV,
			SUM(CASE WHEN id_asig_dedu = 5 THEN dias * monto ELSE 0 END) AS 	RPE,
			SUM(CASE WHEN id_asig_dedu = 6 THEN dias * monto ELSE 0 END) AS 	IVSS
			FROM quincenas INNER JOIN nomina ON quincenas.id_quincenas = nomina.id_quincena
			WHERE fecha = '$fecha'
			GROUP BY cedula, fecha) as X
            INNER JOIN empleados ON x.cedula = empleados.cedula
            ORDER BY apellidos, nombres";
		$this->registros = $this->parrot->consultaGenerica($sql);
		/*foreach($this->registro as $fila){
			extract($fila);
			$this->id_comprobante = $id_comprobantes;
		}*/	
	}
	function Header()
	{	
		$this->Image('../imagenes/parrotLogo.jpg',20,10,70,30, 'JPG');
	    $this->SetFont('Times','B',20);
	    $this->Cell(180,10,'NOMINA DE PAGO', 0, 0, 'R');
	    $this->Ln(10);
	    $this->Line(20, 40, 262,40);
	    $this->Line(20, 41, 262,41);
	    $this->Line(20, 42, 262,42);
	}
	
	function body(){
	    $registro = $this->parrot->quincenasRecords("fecha='$this->fecha'", "","",0,1);
	    foreach($registro as $fila){
	    	extract($fila);
		}		    
					
		$this->Ln(15);
		$this->SetFont('Times','B',12);
		$this->Cell(40,12,"FECHA DE PAGO:", 0, 0);
		$this->SetFont('Times','',12);
	    $this->Cell(35,12,dMySQL_ES($this->fecha), 0,0);
		$this->SetFont('Times','B',12);
		$this->Cell(70,12,"PERIODO A CANCELAR:   DESDE", 0, 0);
		$this->SetFont('Times','',12);
	    $this->Cell(25,12,dMySQL_ES($desde), 0,0,'C');
		$this->SetFont('Times','B',12);
		$this->Cell(25,12,"AL", 0, 0,"C");
		$this->SetFont('Times','',12);
	    $this->Cell(25,12,dMySQL_ES($hasta), 0,0,'C');
	   	$this->Line(20, 56, 262,56);
	    $this->Line(20, 57, 262,57);
	    $this->Line(20, 58, 262,58);
	    //   Encabezado de la tabla.
	    $this->SetFont('Times','B',10);
	    $this->Ln(20);
	    $this->Cell(50, 20, "APELLIDOS Y NOMBRES", 1, 0, "C");
	    $this->Cell(19, 20, "CEDULA", 1, 0, "C");
	    $this->Cell(17, 20, "SUELDO", 1, 0, "C");
	    $this->Cell(77, 10, "ASIGNACIONES", 1, 0, "C");
	    $this->Cell(58, 10, "DEDUCCIONES", 1, 0, "C");
	    $this->Cell(21, 10, "TOTAL A", "TR", 0, "C");
	    $this->Ln(10);
	    $this->Cell(86, 10, "", 0, 0, "C");
	    $this->Cell(18, 10, "Quincena", "BR", 0, "C");
	    $this->Cell(20, 10, "Transporte", "BR", 0, "C");
	    $this->Cell(22, 10, "Alimentacion", "BR", 0, "C");
	    $this->Cell(17, 10, "Total", "BR", 0, "C");
	    $this->Cell(15, 10, "F.A.O.V.", "BR", 0, "C");
	    $this->Cell(13, 10, "R.P.E.", "BR", 0, "C");
	    $this->Cell(15, 10, "I.V.S.S.", "BR", 0, "C");
	    $this->Cell(15, 10, "TOTAL", "BR", 0, "C");
	    $this->Cell(21, 10, "CANCELAR", "BR", 0, "C");
	    //  Iniciar acumuladores.
	    $sumaQuincena = 0;
	    $sumaTransporte = 0;
	    $sumaAlimentacion = 0;
	    $sumaFAOV = 0;
	    $sumaRPE = 0;
	    $sumaIVSS = 0;
	    $sumaAsignaciones = 0;
	    $sumaDeducciones = 0;
	    $sumaTotal = 0;
	    //	Lectura de los datos de la  base de datos.
	    $this->SetFont('Times','',10);
	    foreach($this->registros as $fila){
		    //  Llenado de la tabla
		    extract($fila);
		    $total = $sumaA - $sumaD;
		    $sueldo = $quincena * 2;
	    	$this->Ln(10);
		    $this->Cell(50, 10, "$nombre", 1, 0, "L");
		    $this->Cell(19, 10, numeroEspanol($cedula,0), 1, 0, "R");
		    $this->Cell(17, 10, numeroEspanol($sueldo), 1, 0, "R");
		    $this->Cell(18, 10, numeroEspanol($quincena), "BR", 0, "R");
		    $this->Cell(20, 10, numeroEspanol($transporte), "BR", 0, "R");
		    $this->Cell(22, 10, numeroEspanol($alimentacion), "BR", 0, "R");
		    $this->Cell(17, 10, numeroEspanol($sumaA), "BR", 0, "R");
		    $this->Cell(15, 10, numeroEspanol($FAOV), "BR", 0, "R");
		    $this->Cell(13, 10, numeroEspanol($RPE), "BR", 0, "R");
		    $this->Cell(15, 10, numeroEspanol($IVSS), "BR", 0, "R");
	    	$this->Cell(15, 10, numeroEspanol($sumaD), "BR", 0, "R");
	    	$this->Cell(21, 10, numeroEspanol($total), "BR", 0, "C");
	 		
			$sumaQuincena += $quincena;
		    $sumaTransporte += $transporte;
		    $sumaAlimentacion += $alimentacion;
		    $sumaFAOV += $FAOV;
		    $sumaRPE += $RPE;
		    $sumaIVSS += $IVSS;
		    $sumaAsignaciones += $sumaA;
		    $sumaDeducciones += $sumaD;
		    $sumaTotal += $total;	 
	    	
		}
		$this->Ln(10);
		$this->SetFont('Times','B',10);
	    $this->Cell(86, 10, "TOTALES", 1, 0, "R");
	    $this->Cell(18, 10, numeroEspanol($sumaQuincena), "BR", 0, "R");
	    $this->Cell(20, 10, numeroEspanol($sumaTransporte), "BR", 0, "R");
	    $this->Cell(22, 10, numeroEspanol($sumaAlimentacion), "BR", 0, "R");
	    $this->Cell(17, 10, numeroEspanol($sumaAsignaciones), "BR", 0, "R");
	    $this->Cell(15, 10, numeroEspanol($sumaFAOV), "BR", 0, "R");
	    $this->Cell(13, 10, numeroEspanol($sumaRPE), "BR", 0, "R");
	    $this->Cell(15, 10, numeroEspanol($sumaIVSS), "BR", 0, "R");
    	$this->Cell(15, 10, numeroEspanol($sumaDeducciones), "BR", 0, "R");
    	$this->Cell(21, 10, numeroEspanol($sumaTotal), "BR", 0, "C");
	}
}
sleep(3);
//$numero = $_COOKIE["fecha"];
$fecha = $_COOKIE["fecha"]; //"29/05/2015";
$reporte = new pdf_nomina($fecha);
$reporte->AddPage();
$reporte->body();
$reporte->SetDisplayMode("default");
$reporte->Output();

?>