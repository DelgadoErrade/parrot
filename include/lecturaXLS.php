<?php
function encontrar($matriz, $valor){
	$res = FALSE;
	foreach($matriz as $fila){
		extract($fila);
		if(is_array($valor)){
			if($valor[0] == $referencia and $valor[1] == $monto){
				$res = TRUE;
				break;
			}	
		}else{
			if($referencia == $valor or $monto == $valor){
				$res = TRUE;
			}
		}
		
	}
	return $res;
}

/** Error reporting */
error_reporting(E_ALL);
require_once("./cls_parrot.php");
$strDia_desde = "2016/12/01";
$strDia_hasta = "2016/12/31";
$idbanco = 1;
$sql = "SELECT fecha, referencia, descripcion, monto FROM tbl_mov_bancarios 
	WHERE fecha >=  '$strDia_desde'  
	AND fecha <= '$strDia_hasta'  
	AND idbanco = $idbanco 
	ORDER BY fecha, referencia";
$bd = new CLS_PARROT;
$REC = $bd->consultagenerica($sql);	
require_once("./form_items.php");
$valores = array("","Conciliado","Agregar","Borrar","Editar");
$items = array(0,1,2,3,4);
$cmb = frm_select("cmb[]",$valores,$items,0);
/** PHPExcel */
require_once '../PHPExcel/PHPExcel.php';
/** PHPExcel_IOFactory */
require_once '../PHPExcel/clases/IOFactory.php';
/** PHPExcel READER */
require_once '../PHPExcel/clases/Reader/Excel2007.php';
// Create new PHPExcel object
$objLector = new PHPExcel_Reader_Excel2007();
$objPHPExcel = $objLector->load("../xls/parrot14122016.xlsx");
$header = "<tr>";
$header .=  "<th>N.</th><th width='100px'>".utf8_encode($objPHPExcel->getActiveSheet()->getCell("A1")->getValue())."</th>";
$header .=  "<th width='100px'>".$objPHPExcel->getActiveSheet()->getCell("B1")->getValue()."</th>";
$header .=  "<th width='350px'>".$objPHPExcel->getActiveSheet()->getCell("C1")->getValue()."</th>";
$header .=  "<th width='100px'>BANCO</th>";//$objPHPExcel->getActiveSheet()->getCell("D1")->getValue()."
$header .=  "<th width='100px'>SISTEMA</th>";
$header .=  "<th width='100px'>CONDICION</th>";
$header .= "</tr>";
$cont = TRUE;
$i = 1;
$datos = "";
$REC_XLS = array();
while($cont){
	$i++;
	if(trim($objPHPExcel->getActiveSheet()->getCell("A$i")->getValue()) <> "" ){
		$fecha = date("d/m/Y",PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("A$i")->getValue()));
		$referencia = substr($objPHPExcel->getActiveSheet()->getCell("B$i")->getValue(),-6);
		$descripcion = $objPHPExcel->getActiveSheet()->getCell("C$i")->getValue();
		$banco = $objPHPExcel->getActiveSheet()->getCell("D$i")->getValue();

		$fila  = "<tr>";
		$fila .= "<td align='right'>$i</td>";
		$fila .=  "<td align='center'>$fecha</td>";
		$fila .=  "<td align='right'>$referencia</td>";
		$fila .=  "<td>$descripcion</td>";
		$monto_xls = $banco;
		$fila .=  "<td  align='right'>".number_format($banco,2, ",",".")."</td>";
		$fila .=  "<td  align='right'>".number_format(0,2, ",",".")."</td>";
		$condicion = 0;
		if(encontrar($REC, array($referencia, $banco))){
			$hlld = TRUE;	
			$condicion = 1;
		}else{
			if(encontrar($REC, $banco)){
				$condicion = 4;
			}else{
				$condicion = 2;
			}
			$hlld = FALSE;
		}		
		$cmb = frm_select("cmb[]",$valores,$items,$condicion);	
		$REC_XLS[]=array($fecha, $referencia, $descripcion, $monto_xls, $hlld); 
		$fila .=  "<td  align='right'>$cmb</td>";
		$fila .= "</tr>";
		$datos .= $fila;
		if($i==100)$cont = FALSE;	
	}else{
		$i--;
		$banco = $objPHPExcel->getActiveSheet()->getCell("E$i")->getValue();
		$fila  = "<tr>";
		$fila .= "<td align='right'></td>";
		$fila .=  "<td align='center'><strong>$fecha</strong></td>";
		$fila .=  "<td align='right'></td>";
		$fila .=  "<td><strong>SALDO A LA FECHA</strong></td>";
		$fila .=  "<td  align='right'><strong>".number_format($banco,2, ",",".")."</strong></td>";
		$fila .=  "<td  align='right'><strong>".number_format(0,2, ",",".")."</strong></td>";
		$fila .= "</tr>";
		$datos .= $fila;
		$cont = FALSE;
	}
}
$htm = "<table border='1' align='center'>";
$htm .= "<thead>$header</thead>";
$htm .= "<tfoot>$header</tfoot>";
$htm .= "<tbody>$datos</tbody>";
$htm .= "</table>";
echo $htm;

