<?php
function procXLS($frm){
	extract($frm);
	require_once("./funciones_fecha.php");
	require_once("./cls_parrot.php");
	$bd = new CLS_PARROT;
	$registros = count($condicion);
	$j = 0;
	for($i = 0; $i < $registros; $i++){
		if($condicion[$i] == 1){
			// agregar.
			$fecha[$i] = d_ES_MYSQL($fecha[$i]); 
			$r = $bd->tbl_mov_bancariosInsert(1, $tipo_movimiento[$i], 0, $fecha[$i], $referencia[$i], $descripcion[$i], $monto[$i] );
			if($r){
				$j++;
			}
		}
	}
	$xr = new xajaxResponse();
	$xr->alert("Se grabaron $j registros");
	return $xr;	
}

function buscarArchivo(){
	require_once("./form_items.php");
	require_once("./funciones_xajax.php");
	$estilo = "style ='font-size:1.5em;'";
	$accion1 = 'onclick="document.getElementById(\'datos\').innerHTML=\'CARGANDO...\';xajax_mostrarArchivo(xajax.getFormValues(\'frm01\'))"';
	$clase = "class=\"file file-loading\"";
	$otro = "data-show-preview='false' data-show-upload='false' data-show-remove='false' placeholder='Seleccione archivo' accept='.xlsx'";
	$txtInputFile = frm_file("archivo", "$clase $otro");	
	$htm =  '<div id="cont01" class="padre container">';
	$htm .= '   <div class="row">';
	$htm .= '		<div class="col-sm-offset-4 col-sm-4">';
	$htm .= '     		<form id="frm01"><center>';
	$htm .= '				<p '.$estilo.'>Seleccione su archivo: '.$txtInputFile.'</p>';
	$htm .= frm_button("accionArchivo","Abrir archivo", $accion1.' type="button" class= "btn btn-primary"');
	$htm .= '			</center></form><br />';
	$htm .= '		</div>';
	$htm .= '	</div><hr/>'; 
	$htm .= '	<div class="row">';
	$htm .= '		<div id="datos" class="hijo col-sm-12"></div>';
	$htm .= '	</div>';
	$htm .= '</div>';
	return $htm;
}

function mostrarArchivo($archivoXLS){	
	extract($archivoXLS);
	$xr = new xajaxResponse();
	if($archivo == ""){
		$xr->alert("Debe seleccionar un archivo excel con los datos a validar.");	
	}
	else
	{
		$ltipo = array("");
		$archivo = "../xls/".$archivo;
		require_once("./cls_parrot.php");
		$idbanco = 1;
		$bd = new CLS_PARROT;
		//$REC = $bd->consultagenerica($sql);	
		require_once("./form_items.php");
		$valores = array("","Agregar");
		$items = array(0,1);
		/** PHPExcel */
		require_once '../PHPExcel/PHPExcel.php';
		/** PHPExcel_IOFactory */
		require_once '../PHPExcel/clases/IOFactory.php';
		/** PHPExcel READER */
		require_once '../PHPExcel/clases/Reader/Excel2007.php';
		// Create new PHPExcel object
		$objLector = new PHPExcel_Reader_Excel2007();
		$objPHPExcel = $objLector->load($archivo);//	"../xls/parrot022017.xlsx"
		$header = "<tr>";
		$header .=  "<th>N.</th><th width='100px'>FECHA</th>";
		$header .=  "<th width='100px'>REFERENCIA</th>";
		$header .=  "<th width='350px'>DESCRIPCION</th>";
		$header .=  "<th width='100px'>MONTO</th>";//$objPHPExcel->getActiveSheet()->getCell("D1")->getValue()."
		$header .=  "<th width='100px'>ACCION</th>";
		$header .=  "<th width='100px'>TIPO</th>";
		$header .= "</tr>";
		$cont = TRUE;
		$i = 1;
		$datos = "";
		$prop = "readonly";
		$alineacionDerecha = " style='text-align:right' ";
		$REC_XLS = array();
		while($cont){
			$i++;
			if(trim($objPHPExcel->getActiveSheet()->getCell("A$i")->getValue()) <> "" ){
				$fecha = date("d/m/Y",PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("A$i")->getValue()));
				$referencia = substr($objPHPExcel->getActiveSheet()->getCell("B$i")->getValue(),-6);
				$descripcion = $objPHPExcel->getActiveSheet()->getCell("C$i")->getValue();
				$banco = $objPHPExcel->getActiveSheet()->getCell("D$i")->getValue();
				$j = $i-1;
				$fila  = "<tr>";
				$fila .= "<td align='right'>$j</td>";
				$fila .=  "<td align='center'>".frm_text("fecha[]",$fecha,10,10, $prop)."</td>";
				$fila .=  "<td align='right'>".frm_text("referencia[]",$referencia,10,10, $prop.$alineacionDerecha )."</td>";
				$fila .=  "<td>".frm_text("descripcion[]",$descripcion,50,50, $prop)."</td>";
				$monto_xls = $banco;
				$montoOculto = frm_hidden("monto[]",$monto_xls);
				$fila .=  "<td  align='right'>$montoOculto ".number_format($banco,2, ",",".")."</td>";
				$condicion = 0;
				$cmb = frm_select("condicion[]",$valores,$items,$condicion);	
				$lTipo = fncTipo($descripcion);
				$cmbTipoMov = frm_comboGenerico("tipo_movimiento[]", "tipo_movimiento", "id_tipo_movimiento", "tbl_tipo_movimientos", "CLS_PARROT","","",$lTipo);
				$REC_XLS[]=array($fecha, $referencia, $descripcion, $monto_xls); 
				$fila .=  "<td  align='right'>$cmb</td>";
				$fila .=  "<td  align='right'>$cmbTipoMov</td>";
				$fila .= "</tr>";
				$datos .= $fila;
			}
			else{
				$cont = FALSE;
			}
		}
			
		$htm = "<form  id='frm'>";
		$htm .= "<table align='center' class='table table-hover'>";
		$htm .= "<thead>$header</thead>";
		$htm .= "<tfoot>$header</tfoot>";
		$htm .= "<tbody>$datos</tbody>";
		$htm .= "</table>";
		$htm .= "<br /><center>";
		$accion = "onclick=\"xajax_procXLS(xajax.getFormValues('frm'))\"";
		$htm .=  frm_button("Procesar","Procesar datos", $accion." class='btn btn-primary'"); 
		$htm .= "</center><hr />";
		$htm .= "<br />";
		$htm .= "</form>";
			//	return $htm;
		$xr->assign("datos","innerHTML", $htm);
	}
	return $xr;
}

function fncTipo($cadena){
	$cad = strtoupper(substr($cadena,0,3));
	switch($cad){
		case "TDC":
			$r = 8;
			break;
		case "TDB":
			$r = 7;
			break;
		case "TRA":
			$r = 6;
			break;
		case "DEP":
			$r = 2;
			break;
		case "TRF":
			$r = 6;
			break;
		case "MAN":
			$r = 4;
			break;
		case "COM":
			$r = 4;
			break;
		default:
			$r = 6;
		break;			
	}
	return $r;
}


