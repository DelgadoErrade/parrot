<?php
include_once("cls_parrot.php");
include_once("fpdf.php");
include_once("funciones_fecha.php");
include_once("form_items.php");
//============================================================================================================
class pdf_bancos extends FPDF
{
	var $registros;
	var $banco;	
	var $periodo;
	var $str_tipoMovimiento;
//------------------------------------------------------------------------------------------------------------	
	function __construct($idbanco, $nDias, $tipoMovimiento){
		$this->FPDF('P','mm','Letter');
		$this->SetMargins(20,10,20);
		$this->SetAutoPageBreak(FALSE);
		$parrot = new CLS_PARROT();
		$sql = "SELECT banco FROM tbl_bancos WHERE idbanco = $idbanco";
		$registro = $parrot->consultagenerica($sql);
		$this->banco = $registro[0]["banco"];
		$sql = "SELECT tipo_movimiento FROM tbl_tipo_movimientos WHERE id_tipo_movimiento = $tipoMovimiento";
		$registro = $parrot->consultagenerica($sql);
		$this->str_tipoMovimiento = $registro[0]["tipo_movimiento"];
		switch($nDias){
			case 1:
				$dia    = $_COOKIE["dia"];
				$strDia = str_replace("/", ",", $dia);
				$sql = "SELECT date_format(fecha, '%d-%m-%Y') AS _Fecha, 
					referencia, 
					descripcion, 
					monto 
				FROM tbl_mov_bancarios
				WHERE tbl_mov_bancarios.id_tipo_movimiento = $tipoMovimiento 
					AND fecha ='$dia' 
					AND idbanco = $idbanco
					ORDER BY fecha";
					$e_dia = dMySQL_ES($dia);	
					$this->periodo = "Movimientos del dia $e_dia";		
				break;
			case 2:
			case 3:
				$dia_desde = $_COOKIE["dia_desde"];
				$strDia_desde =  str_replace("/", ",", $dia_desde);
				$dia_hasta = $_COOKIE["dia_hasta"];
				$strDia_hasta = str_replace("/", ",", $dia_hasta);
				$sql = "SELECT date_format(fecha, '%d-%m-%Y') AS _Fecha, 
						referencia, 
						descripcion, 
						monto 
					FROM tbl_mov_bancarios
					WHERE tbl_mov_bancarios.id_tipo_movimiento = $tipoMovimiento
					AND fecha BETWEEN '$dia_desde' AND '$dia_hasta' 
					AND idbanco = $idbanco
					ORDER BY fecha";
					$dia_desde = dMySQL_ES($dia_desde);
					$dia_hasta = dMySQL_ES($dia_hasta);	
					$this->periodo = "Movimientos desde $dia_desde hasta $dia_hasta"; 		
				break;
		} 		
		$this->registros = $parrot->consultagenerica($sql);
	}
//------------------------------------------------------------------------------------------------------------	
	function Header(){	
		$this->Image('../imagenes/parrotLogo.jpg',17,5,60,25, 'JPG');
		if($this->banco == "BANESCO"){
			$this->Image('../imagenes/banesco.jpg',170,5,25,25, 'JPG');	
		}else{
			$this->Image('../imagenes/mercantil.jpg',160,5,40,25, 'JPG');	
		}
	    $this->SetFont('Times','B',12);
	    $this->Cell(180, 10, $this->banco, 0, 0, 'C');
	    $this->Ln(16);
	    $this->cell(180, 10, $this->periodo ,0,0,"C");// FECHA O PERIODO
	    $this->Ln(16);
	    $this->cell(180, 10, $this->str_tipoMovimiento ,0,0,"C");// FECHA O PERIODO
	}
//------------------------------------------------------------------------------------------------------------	
	function body(){
		$columnas = array(26, 26, 102, 26);	
		$encabezados = array("FECHA", "REFERENCIA", "DESCRIPCION", "MONTO");
		$variables = array("_Fecha", "referencia", "descripcion", "monto");
		$alineacion = array("C", "L", "L", "R");
		$this->SetFont('Times','B',8);
		$this->SetFillColor(223,223,223);
		$this->Ln(12);
		for($i=0; $i<4;$i++){
			$this->Cell($columnas[$i], 8, $encabezados[$i], 1, 0, 'C',1);
			$r = true;
		}
		$saldo = 0;
	    foreach($this->registros as $fila){
			if($r){
				$color = 0;
			}else{
				$color = 1;
			}
			$r = !$r;	    	
			if(is_null($fila["monto"])){
				$saldo += 0; 
			}else{
				$saldo += $fila["monto"];
			}
			$this->Ln(8);
			for($i=0; $i<4; $i++){
				$this->Cell($columnas[$i] , 8, ($i < 3 ) ? $fila[$variables[$i]]: numeroEspanol($fila[$variables[$i]]), "LRB",0,$alineacion[$i], $color);
			}
		}	
		if($r){
			$color = 0;
		}else{
			$color = 1;
		}		
		$this->Ln(8);
		$this->Cell(154, 8, "TOTAL $this->str_tipoMovimiento", 1, 0, 'R', $color);
		$this->Cell(26, 8, numeroEspanol($saldo), 1, 0, 'R', $color);
	}
//------------------------------------------------------------------------------------------------------------	
}
//============================================================================================================
$idbanco = $_COOKIE["idbanco"];
$nDias   = $_COOKIE["nDias"];
$tipoMovimiento = $_COOKIE["tipoMovimiento"];
$reporte = new pdf_bancos($idbanco, $nDias, $tipoMovimiento);
$reporte->AddPage();
$reporte->body();
$reporte->SetDisplayMode("default");
$reporte->Output();
//============================================================================================================
?>