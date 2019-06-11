<?php
include_once("cls_parrot.php");
include_once("fpdf.php");
include_once("funciones_fecha.php");
include_once("form_items.php");
class pdf_bancos extends FPDF
{
	var $registros;
	var $banco;	
	var $periodo;
//------------------------------------------------------------------------------------	
	function __construct($idbanco, $nDias){
		$this->FPDF('P','mm','Letter');
		$this->SetMargins(20,10,20);
		$this->SetAutoPageBreak(FALSE);
		$parrot = new CLS_PARROT();
		$sql = "SELECT banco FROM tbl_bancos WHERE idbanco = $idbanco";
		$registro = $parrot->consultagenerica($sql);
		$this->banco = $registro[0]["banco"];
		switch($nDias){
			case 1:
			$dia = $_COOKIE["dia"];
			$strDia = str_replace("/", ",", $dia);
				/* Consulta para una determinada fecha*/
				$sql = "(SELECT STR_TO_DATE('$strDia', '%Y,%m,%d') AS fecha, '' AS referencia, 'SALDO ANTERIOR' AS descripcion, 
					SUM(monto) AS monto FROM tbl_mov_bancarios WHERE fecha < '$dia' AND idbanco = $idbanco) UNION
					(SELECT fecha, referencia, descripcion, monto FROM tbl_mov_bancarios
					WHERE fecha = '$dia' AND idbanco = $idbanco) ORDER BY fecha, referencia";
					$e_dia = dMySQL_ES($dia);	
					$this->periodo = "Movimientos del dia $e_dia";		
				break;
			case 2:
			case 3:
				$dia_desde = $_COOKIE["dia_desde"];
				$strDia_desde =  str_replace("/", ",", $dia_desde);
				$dia_hasta = $_COOKIE["dia_hasta"];
				$strDia_hasta = str_replace("/", ",", $dia_hasta);
				/* Consulta entre fechas	*/
				$sql = "(SELECT STR_TO_DATE('$strDia_desde', '%Y,%m,%d') AS fecha, '' AS referencia, 'SALDO ANTERIOR' AS descripcion, 
					SUM(monto) AS monto FROM tbl_mov_bancarios WHERE fecha <  STR_TO_DATE('$strDia_desde', '%Y,%m,%d')  AND idbanco = $idbanco)
					UNION
					(SELECT fecha, referencia, descripcion, monto FROM tbl_mov_bancarios
					WHERE fecha >=  STR_TO_DATE('$strDia_desde', '%Y,%m,%d')  AND fecha <=  STR_TO_DATE('$strDia_hasta', '%Y,%m,%d')  AND idbanco = $idbanco 
					)ORDER BY fecha, referencia";
					$dia_desde = dMySQL_ES($dia_desde);
					$dia_hasta = dMySQL_ES($dia_hasta);	
					$this->periodo = "Movimientos desde $dia_desde hasta $dia_hasta"; 		
				break;
		} 		
		$this->registros = $parrot->consultagenerica($sql);
	}
//------------------------------------------------------------------------------------	
	function Header(){	
		$this->Image('../imagenes/parrotLogo.jpg',17,5,60,25, 'JPG');
	    $this->SetFont('Times','B',12);
	    $this->Cell(180, 10, $this->banco, 0, 0, 'C');
	    $this->Ln(16);
	    $this->cell(180, 10, $this->periodo ,0,0,"C");// FECHA O PERIODO
	 	$columnas = array(26, 26, 76, 26, 26);	
		$encabezados = array("FECHA", "REFERENCIA", "DESCRIPCION", "MONTO", "SALDO");
		$variables = array("fecha", "referencia", "descripcion", "monto");
		$alineacion = array("C", "C", "L", "R", "R");
		$this->SetFont('Times','B',8);
		$this->setY(31);
		$this->Ln(10);
		for($i=0; $i<5;$i++){
			$this->Cell($columnas[$i], 8, $encabezados[$i], 1, 0, 'C');
		}   
	}
//------------------------------------------------------------------------------------	
	function body(){
		$columnas = array(26, 26, 76, 26, 26);	
		$encabezados = array("FECHA", "REFERENCIA", "DESCRIPCION", "MONTO", "SALDO");
		$variables = array("fecha", "referencia", "descripcion", "monto");
		$alineacion = array("C", "C", "L", "R", "R");
		$this->SetFont('Times','B',8);
		$this->setY(30);
		$this->Ln(12);
/*		for($i=0; $i<5;$i++){
			$this->Cell($columnas[$i], 8, $encabezados[$i], 1, 0, 'C');
		}*/
		$saldo = 0;
	    foreach($this->registros as $fila){
				if(is_null($fila["monto"])){
					$saldo += 0; 
				}else{
					$saldo += $fila["monto"];
				}
				$this->Ln(6);
				for($i=0; $i<4; $i++){
					if($i == 0){
						$fila["fecha"] = dMySQL_ES($fila["fecha"]);
				}
					$this->Cell($columnas[$i] , 6, ($i < 3 ) ? $fila[$variables[$i]]: numeroEspanol($fila[$variables[$i]]), "LRB",0,$alineacion[$i]);
				}
				$this->Cell($columnas[4] , 6, numeroEspanol($saldo), "LRB",0,'R');	
			}	    	
	}
//------------------------------------------------------------------------------------	
	function Footer(){
	    // Posición: a 1,5 cm del final
	    $this->SetY(-15);
	    // Arial italic 8
	    $this->SetFont('Arial','I',8);
	    // Número de página
	    $this->Cell(0,10,$this->PageNo(),0,0,'C');
	   
	}
//------------------------------------------------------------------------------------	
	function AcceptPageBreak(){
		$this->addPage();
 		$this->setY(50);
 		
	}
}

	$idbanco = $_COOKIE["idbanco"];
	$nDias   = $_COOKIE["nDias"];
	$reporte = new pdf_bancos($idbanco, $nDias);
	$reporte->AddPage();
	$reporte->SetAutoPageBreak(TRUE, 20);
	$reporte->body();
	$reporte->SetDisplayMode("default");
	$reporte->Output();

?>