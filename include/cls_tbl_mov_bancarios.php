<?php
	include_once("cls_parrot.php");
	include_once("fpdf.php");
	include_once("funciones_fecha.php");
	include_once("form_items.php");

	class CLS_TBL_MOV_BANCARIOS extends CLS_PARROT{
	var $sqlBase;
	var $titulo;
	var $ordenTabla ="data-order='[[ 0, \"desc\" ], [ 1, \"asc\" ]]'";
//-----------------------------------------------------------------------------------------------------------
//	METODOS SIN CAMBIOS DE NINGUN TIPO
//-----------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------
//	METODOS CON CAMBIOS. DICHOS CAMBIOS; 
//-----------------------------------------------------------------------------------------------------------
	function __construct(){
		parent::__construct();
			//$this->sqlBase = "SELECT * FROM tbl_mov_bancarios";
			$this->sqlBase = "SELECT id_mov_bancario, tbl_bancos.idbanco, banco, tbl_tipo_movimientos.id_tipo_movimiento, 
				tipo_movimiento, fecha, referencia, descripcion, FORMAT(monto,2) as monto  FROM tbl_mov_bancarios INNER JOIN 
				tbl_bancos ON tbl_bancos.idbanco = tbl_mov_bancarios.idbanco INNER JOIN 
				tbl_tipo_movimientos ON tbl_tipo_movimientos.id_tipo_movimiento = tbl_mov_bancarios.id_tipo_movimiento";
			$this->titulo = "MOVIMIENTOS BANCARIOS";
	}
//-----------------------------------------------------------------------------------------------------------
	function insertNewRecord($f){
		extract($f);
		$monto = numeroIngles($monto);
		$fecha = d_ES_MYSQL($fecha);
		$referencia = utf8_encode(strtoupper($referencia));
		$descripcion = utf8_encode(strtoupper($descripcion));
		$descripcion = strtoupper($descripcion);
//		VALIDA QUE EL REGISTRO NO EXISTA EN LA BASE DE DATOS.
/*		$sql = "SELECT COUNT(id_mov_bancario) as n FROM tbl_mov_bancarios WHERE 
			fecha = '$fecha' AND referencia = '$referencia' AND monto = $monto";
		$valida = $this->consultagenerica($sql);
   	  	if($valida[0]["n"] == 0){ // Si n = 0, Registro no esta en la base de datos.*/
		$r = $this->tbl_mov_bancariosInsert($idbanco, $id_tipo_movimiento, 0, $fecha, $referencia, $descripcion, $monto);
		return $r;
/*		}else{
			$r = FALSE;
		}*/
	}
//-----------------------------------------------------------------------------------------------------------
	function updateRecord($f){
		extract($f);
		$monto = numeroIngles($monto);
		$fecha = d_ES_MYSQL($fecha);
		$referencia = strtoupper($referencia);
		$descripcion = strtoupper($descripcion);
		$res = $this->tbl_mov_bancariosUpdate($idbanco, $id_tipo_movimiento, $fecha, $referencia, $descripcion, $monto, $id_mov_bancario);
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function deleteRecord($id){
		$res = $this->tbl_mov_bancariosDelete("id_mov_bancario = $id");
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function formAdd(){
		$html = $this->frmtbl_mov_bancarios();
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function formEdit($id){
		$html = $this->frmtbl_mov_bancarios($id);
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function frmtbl_mov_bancarios(){
		$alineacionDerecha = " style='text-align:right' ";
		if(func_num_args() > 0){
			$id_mov_bancario = func_get_arg(0);
			$records = $this->tbl_mov_bancariosRecords("id_mov_bancario = $id_mov_bancario");
			foreach($records as $record){
				extract($record);
			}
			$fecha = dMySQL_ES($fecha);
			$monto = numeroEspanol($monto);
			$cmbBancos = frm_comboGenerico("idbanco", "banco", "idbanco", "tbl_bancos", "CLS_PARROT","","",$idbanco);
			$cmbTipoMov = frm_comboGenerico("id_tipo_movimiento", "tipo_movimiento", "id_tipo_movimiento", "tbl_tipo_movimientos", "CLS_PARROT","","",$id_tipo_movimiento);
			$textoBoton = "Actualizar";
			$accion = "onClick=\"xajax_update('CLS_TBL_MOV_BANCARIOS',xajax.getFormValues('frm'))\"";
			$titulo ="Editar movimiento bancario";
		}else{
			$cmbBancos = frm_comboGenerico("idbanco", "banco", "idbanco", "tbl_bancos", "CLS_PARROT");
			$cmbTipoMov = frm_comboGenerico("id_tipo_movimiento", "tipo_movimiento", "id_tipo_movimiento", "tbl_tipo_movimientos", "CLS_PARROT");
			$textoBoton = "Grabar";
			$accion = "onClick=\"xajax_save('CLS_TBL_MOV_BANCARIOS',xajax.getFormValues('frm'))\"";
			$id_mov_bancario = 0;
			$idbanco = 0;
			$id_tipo_movimiento = 0;
			$fecha = date('d/m/Y');
			$referencia = '';
			$descripcion = '';
			$monto = "";
			$titulo = "Nuevo movimiento bancario";
		}
		$htm = "<div class='row'><div class='col-md-12'><h3>$titulo</h3><form id='frm' >".frm_hidden("id_mov_bancario", $id_mov_bancario)."
				<table align='center' border='0'>
					<tr><td align='right'><b>Banco</b>:&nbsp;</td><td>".$cmbBancos."</td></tr>	
					<tr><td align='right'><b>Tipo de Movimiento</b>:&nbsp;</td><td>".$cmbTipoMov."</td></tr>	
					<tr><td align='right'><b>Fecha</b>:&nbsp;</td><td>".frm_calendario('fecha','fecha' ,$fecha, "id='fecha' required")."</td></tr>	
					<tr><td align='right'><b>Referencia</b>:&nbsp;</td><td>".frm_text('referencia', $referencia, '45', '45 ', "required class='mayusculas'")."</td></tr>	
					<tr><td align='right'><b>Descripcion</b>:&nbsp;</td><td>".frm_text('descripcion', $descripcion, '45', '100 ', "required class='mayusculas'")."</td></tr>	
					<tr><td align='right'><b>Monto</b>:&nbsp;</td><td>".frm_numero('monto', $monto, '10', '10')."</td></tr>	
				</table>	
			</form></div></div>";
	return $htm;	
	}
//-----------------------------------------------------------------------------------------------------------
	function checkAllData($f,$new = 0){		// Considerar colocar los campos obligatorios en el formulario.
		//if(empty($f['id_mov_bancario'])) return "El campo 'id_mov_bancario' no puede ser nulo.";
		if(empty($f['idbanco'])) return "El campo 'idbanco' no puede ser nulo.";
		if(empty($f['id_tipo_movimiento'])) return "El campo 'id_tipo_movimiento' no puede ser nulo.";
		if(empty($f['fecha'])) return "El campo 'fecha' no puede ser nulo.";
		if(empty($f['referencia'])) return "El campo 'referencia' no puede ser nulo.";
		if(empty($f['descripcion'])) return "El campo 'descripcion' no puede ser nulo.";
		if(empty($f['monto'])) return "El campo 'monto' no puede ser nulo.";
	 	return 0;
	}
//-----------------------------------------------------------------------------------------------------------
	function blanquearCampos(){		//SI ES PARA EL FORMULARIO, SE PUEDE SUSTITUIR POR reset.
		$xr = new xajaxResponse();
		$xr->assign("id_mov_bancario","value","");
		$xr->assign("idbanco","value","");
		$xr->assign("id_tipo_movimiento","value","");
		$xr->assign("fecha","value","");
		$xr->assign("referencia","value","");
		$xr->assign("descripcion","value","");
		$xr->assign("monto","value","");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
// Nombres de los campos de la consulta.
	function camposBD(){
		$fields = array();
		$fields[] = 'id_mov_bancario';	
		$fields[] = 'fecha';	
		$fields[] = 'banco';	
		$fields[] = 'tipo_movimiento';	
		$fields[] = 'referencia';	
		$fields[] = 'descripcion';	
		$fields[] = 'monto';	
		return $fields;
	}
//-----------------------------------------------------------------------------------------------------------
	function encabezados(){
		$headers = array();
		//$headers[] = "id_mov_bancario";
		$headers[] = "Fecha";
		$headers[] = "Banco";
		$headers[] = "Tipo Movimiento";
		$headers[] = "Referencia";
		$headers[] = "Descripcion";
		$headers[] = "Monto";
		return $headers;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosEncabezados(){
		// HTML table: hearders attributes
		$attribsHeader = array();
		//$attribsHeader[] = '14';
		$attribsHeader[] = '14';
		$attribsHeader[] = '14';
		$attribsHeader[] = '14';
		$attribsHeader[] = '14';
		$attribsHeader[] = '14';
		$attribsHeader[] = '14';
		return $attribsHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosColumnas(){
		// HTML Table: columns attributes
		$attribsCols = array();
		//$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:right"';
		return $attribsCols;
	}
//-----------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------
function frmReportesBancos(){
	$cmbBancos = frm_comboGenerico("banco","banco", "idbanco", "tbl_bancos", "CLS_PARROT");
	$bd = new CLS_PARROT;
	$calendario = frm_calendario("fecha","fecha");
	$calendario1 = frm_calendario("fecha1","fecha1");
	$calendario2 = frm_calendario("fecha2","fecha2");
	$medioCalendario = xfrm_medioCalendario("mes","anno");
	
	$ejecutar = "onclick=\"xajax_reporteBancario(xajax.getFormValues('frm'))\"";
	$html =	"<div class='row'><div class='col-md-8 col-md-offset-2 cajita'>
		<center><h2>REPORTES BANCARIOS</h2>";
	$html .= "";
	$html .= "<b>Seleccione el Banco:</b> $cmbBancos<br/>
			<h3>Indique el periodo a mostrar.</h3></center>";
	$html .= "<div class='row'> 
			<hr/>
			<div class='col-md-4 text-right' id='rX0'  style='font-weight:900'> ".frm_radio("opcion","1","1", "onclick='activaFechas(0)' ")." Por d&iacute;a </div>";
	$html .= "<div class='col-md-3 text-right' id='rX1'>".frm_radio("opcion","2","", "onclick='activaFechas(1)' ")." Por Mes</div>";
	$html .= "<div class='col-md-3 text-right' id='rX2'>".frm_radio("opcion","3","", "onclick='activaFechas(2)'  ")." Por per&iacute;odo</div>";
	$html .= "</div><hr/></br>";
	
	$html .= "<div class='row'>
			 	<div class='col-md-12' id='xDia'>
			 		<center><b>Seleccione el Dia:</b> $calendario</center>
			 	</div>
			 </div>";
	$html .= "<div class='row' id='xMes'  style='display:none'>
			 	<div class='col-md-12'>
			 		<center><b>Seleccione Mes y a&ntilde;o:</b> $medioCalendario</center>
			 	</div>
			 </div>";
	$html .= "<div class='row' id='xFechas' style='display:none'>
			 	<div class='col-md-12'>
			 		<center><b>Seleccione fechas: <i>Desde</i></b> $calendario1; <b><i>hasta </i></b>$calendario2</center>
			 	</div>
			 </div>";		 
	$html .= "<hr/></br><div class='row'>
			 	<div class='col-md-12'>
			 		<center>".frm_button("reporte", "Mostrar", $ejecutar )."</center>
			 	</div>
			 </div></br></br>";
	$html .= "</div></div>";  
	$htm = "<form id='frm' >".$html."</form>";
	return $htm;
}
//-----------------------------------------------------------------------------------------------------------
function frmReportesBanXtipo(){
	$cmbBancos = frm_comboGenerico("banco","banco", "idbanco", "tbl_bancos", "CLS_PARROT");
	$cmbTipMov = frm_comboGenerico("tipoMovimiento", "tipo_movimiento","id_tipo_movimiento", "tbl_tipo_movimientos", "CLS_PARROT");
	$bd = new CLS_PARROT;
	$calendario = frm_calendario("fecha","fecha");
	$calendario1 = frm_calendario("fecha1","fecha1");
	$calendario2 = frm_calendario("fecha2","fecha2");
	$medioCalendario = xfrm_medioCalendario("mes","anno");
	$ejecutar = "onclick=\"xajax_reporteBancario(xajax.getFormValues('frm'))\"";
	$html = "<div class='row'><div class='col-md-8 col-md-offset-2 cajita'><center><h2>REPORTE BANCARIO POR TIPO DE MOVIMIENTO</h2>";
	$html .= "";
	$html .= "<b>Seleccione el Banco:</b> $cmbBancos<br/><br/>";
	$html .= "<b>Seleccione el tipo de movimiento: </b> $cmbTipMov<br/>";
	$html .= "<h3>Indique el periodo a mostrar.</h3></center><hr/>";
	$html .= "<div class='row'> 
			<div class='col-md-4 text-right' id='rX0' style='font-weight:900'> ".frm_radio("opcion","1","1", "onclick='activaFechas(0)' ")." Por d&iacute;a </div>";
	$html .= "<div class='col-md-3 text-right' id='rX1'>".frm_radio("opcion","2","", "onclick='activaFechas(1)' ")." Por Mes</div>";
	$html .= "<div class='col-md-3 text-right' id='rX2'> ".frm_radio("opcion","3","", "onclick='activaFechas(2)' ")." Por per&iacute;odo</div>";
	$html .= "</div><hr/>";
	$html .= "<div class='row'>
			 	<div class='col-md-12' id='xDia'>
			 		<center><b>Seleccione el Dia:</b> $calendario</center>
			 	</div>
			 </div>";
	$html .= "<div class='row' id='xMes'  style='display:none'>
			 	<div class='col-md-12'>
			 		<center><b>Seleccione Mes y a&ntilde;o:</b> $medioCalendario</center>
			 	</div>
			 </div>";
	$html .= "<div class='row' id='xFechas' style='display:none'>
			 	<div class='col-md-12'>
			 		<center><b>Seleccione fechas: <i>Desde</i></b> $calendario1; <b><i>hasta </i></b>$calendario2</center>
			 	</div>
			 </div>";		 
	$html .= "<hr/><div class='row'>
			 	<div class='col-md-12'>
			 		<center>".frm_button("reporte", "Mostrar", $ejecutar )."</center>
			 	</div>
			 </div></br></br>";
	$html .= "</div></div>";  
	$htm = "<form id='frm' >".$html."</form>";
	return $htm;
}
//-----------------------------------------------------------------------------------------------		
	function reporteBancario($formulario){
		extract($formulario);
		if(isset($tipoMovimiento)){
			setcookie("tipoMovimiento", $tipoMovimiento, time()+6000);
			$reporte = "./include/pdf_bancosXtipo.php";
		}else{
			$reporte = "./include/pdf_bancos.php";		
		}
		setcookie("idbanco", $banco,  time()+6000);
		setcookie("nDias",   $opcion, time()+6000);
		switch($opcion){
			case 1:
				$dia = d_ES_MYSQL($fecha);
				$dias = array("dia"=>$dia);
				setcookie("dia", $dia, time()+6000);
				break;
			case 2:
				$dia_desde = "$anno/$mes/01";
				$finMes = dFinMes("$mes/01/$anno");
				$dia_hasta = d_US_MySQL($finMes);
				$dias = array("dia_desde"=>$dia_desde, "dia_hasta"=>$dia_hasta);
				setcookie("dia_desde", $dia_desde, time()+6000);
				setcookie("dia_hasta", $dia_hasta, time()+6000);
				break;
			case 3:
				$dia_desde = d_ES_MYSQL($fecha1);
				$dia_hasta = d_ES_MYSQL($fecha2);
				$dias = array("dia_desde"=>$dia_desde, "dia_hasta"=>$dia_hasta);
				setcookie("dia_desde", $dia_desde, time()+6000);
				setcookie("dia_hasta", $dia_hasta, time()+6000);
				break;
		}
		$xr = new xajaxResponse();
		$xr->script("var newWin = window.open('$reporte', '_blank', 'fullscreen=yes')");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
	function buscarArchivo(){
		/*require_once("./form_items.php");
		require_once("./funciones_xajax.php");*/
		$estilo = "style ='font-size:1.2em;'";
		$accion1 = 'onclick="document.getElementById(\'datos\').innerHTML=\'CARGANDO...\';xajax_mostrarArchivo(xajax.getFormValues(\'frm01\'))"';
		$clase = "class=\"file file-loading\"";
		$otro = "data-show-preview='false' data-show-upload='false' data-show-remove='false' placeholder='Seleccione archivo' accept='.xlsx'";
		$txtInputFile = frm_file("archivo", "$clase $otro");	
		$htm =  '<div id="cont01" class="container" style="background-color:white; border-radius: 7pt;">';
		$htm .= '   <div class="row">';
		$htm .= '		<div class="col-sm-9">';
		$htm .= '     		<form id="frm01"><center>';
		$htm .= '				<p '.$estilo.'>Seleccione su archivo: '.$txtInputFile.'';
		$htm .= '       </div><div class="col-sm-3"><br/>';
		$htm .= frm_button("accionArchivo","Abrir archivo", $accion1.' type="button" class= "btn btn-primary"');
		$htm .= '			</p></center></form><br />';
		$htm .= '		</div>';
		$htm .= '	</div>'; 
		$htm .= '	<div class="row">';
		$htm .= '		<div id="datos" class="hijo col-sm-12"></div>';
		$htm .= '	</div>';
		$htm .= '</div>';
		return $htm;
	}
//-----------------------------------------------------------------------------------------------------------
	function mostrarArchivo($archivoXLS){	
		extract($archivoXLS);
		$xr = new xajaxResponse();
		if($archivo == ""){
			$xr->alert("Debe seleccionar un archivo excel con los datos a validar.");	
		}
		else
		{
			$ltipo = array("");
			$partes_ruta = pathinfo($archivo);
			$lfile = $partes_ruta['filename'];
			$archivo = "./xls/$lfile.xlsx";
			$idbanco = 1;
			$bd = new CLS_PARROT;
			$valores = array("","Agregar");
			$items = array(0,1);
			/** PHPExcel */
			require_once './PHPExcel/PHPExcel.php';
			/** PHPExcel_IOFactory */
			require_once './PHPExcel/clases/IOFactory.php';
			/** PHPExcel READER */
			require_once './PHPExcel/clases/Reader/Excel2007.php';
			// Create new PHPExcel object
			$objLector = new PHPExcel_Reader_Excel2007();
			$objPHPExcel = $objLector->load($archivo);//	"../xls/parrot022017.xlsx"
			$nc = "style='font-weight:bold;text-align:center;' class='alerta'";
			$header = "<tr >";
			$header .=  "<th $nc>N.</th><th $nc>FECHA</th>";
			$header .=  "<th $nc>REFERENCIA</th>";
			$header .=  "<th $nc>DESCRIPCION</th>";
			$header .=  "<th $nc>MONTO</th>";//$objPHPExcel->getActiveSheet()->getCell("D1")->getValue()."
			$header .=  "<th $nc>ACCION</th>";
			$header .=  "<th $nc>TIPO</th>";
			$header .= "</tr>";
			$cont = TRUE;
			$i = 1;
			$datos = "";
			$prop = "readonly";
			$alineacionDerecha = " style='text-align:right' ";
			$REC_XLS = array();
			$mObj = new CLS_TBL_MOV_BANCARIOS;
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
					$lTipo = $mObj->fncTipo($descripcion);
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
			$htm .= "<table id='dataGrid_2'  class='adminlist table table-striped table-bordered dt-responsive' cellspacing='0' width='100%'>";
			$htm .= "<thead>$header</thead>";
			$htm .= "<tfoot>$header</tfoot>";
			$htm .= "<tbody >$datos</tbody>";
			$htm .= "</table>";
			$htm .= "<br /><center>";
			$accion = "onclick=\"xajax_procXLS(xajax.getFormValues('frm'))\"";
			$htm .=  frm_button("Procesar","Procesar datos", $accion." class='btn btn-primary'"); 
			$htm .= "</center><hr />";
			$htm .= "<br />";
			$htm .= "</form>";
				//	return $htm;
			$xr->assign("datos","innerHTML", $htm);
			$xr->script("dataGrid2('dataGrid_2')");
		}
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------

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
//------------------------------------------------------------------------------------
function procXLS($frm){
	extract($frm);
	$bd = new CLS_PARROT;
	$registros = count($condicion);
	$j = 0;
	for($i = 0; $i < $registros; $i++){
		if($condicion[$i] == 1){
			// agregar.  Validar registros para evitar la duplicidad.
			$fecha[$i] = d_ES_MYSQL($fecha[$i]); 
			$r = $bd->tbl_mov_bancariosInsert(1, $tipo_movimiento[$i], 0, $fecha[$i], $referencia[$i], $descripcion[$i], $monto[$i] );
			if($r){
				$j++;
			}
		}
	}
	$xr = new xajaxResponse();
	$mensaje = "Se grabaron $j registros";
	$xr->script("aviso(" . json_encode($mensaje).  ")");
	return $xr;	
}

//------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------

}
/*	
$mb = new CLS_TBL_MOV_BANCARIOS;
	echo $mb->frmReportesBancos();*/
?>
