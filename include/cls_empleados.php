<?php
	class CLS_EMPLEADOS extends CLS_PARROT{
	var $sqlBase;
	var $titulo;
	var $ordenTabla ="data-order='[[ 0, \"asc\" ],[ 1, \"asc\" ]]'";
//-----------------------------------------------------------------------------------------------------------
//	METODOS SIN CAMBIOS DE NINGUN TIPO
//-----------------------------------------------------------------------------------------------------------
	function getAllRecords($start, $limit, $order = null){
		if($order == null){
			$sql = $this->sqlBase . " LIMIT $start, $limit ".$_SESSION['ordering'];
		}else{
			$sql = $this->sqlBase . " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit ";
		}
		$res = $this->consultagenerica($sql);
		return $res;
	}
//---------------------------------------------------------------------------------------------------------	
	function getRecordsFiltered($start, $limit, $filter = null, $content = null, $order = null, $ordering = ""){
		if(($filter != null) and ($content != null)){
			$sql = $this->sqlBase
					." WHERE ".$filter." like '%".$content."%' "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
		}
		$res = $this->consultagenerica($sql);
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function getNumRows($filter = null, $content = null){
		if(($filter != null) and ($content != null)){
			$criterio = " $filter like '%$content%'";
			$sql = $this->sqlBase . " WHERE " .$criterio;
		}else{
			$sql = $this->sqlBase;
		}
		$registros = $this->consultagenerica($sql);
		$res = $this->filas; 
		return $res;		
	}
//-----------------------------------------------------------------------------------------------------------
	function getRecordByID($id){
		$sql = $this->sqlBase. " WHERE id = $id";
		foreach($res as $row){}
		return $row;
	}
//-----------------------------------------------------------------------------------------------------------
	function events($event = null){
		global $login;
		if(LOG_ENABLED){
			$now = date("Y-M-d H:i:s");
			$fd = fopen (FILE_LOG,'a');
			$log = $now." ".$_SERVER["REMOTE_ADDR"] ." - $event \n";
   		fwrite($fd,$log);
   		fclose($fd);
		}
	}
//-----------------------------------------------------------------------------------------------------------
//	METODOS CON CAMBIOS. DICHOS CAMBIOS; 
//-----------------------------------------------------------------------------------------------------------
	function __construct(){
		parent::__construct();
			$this->sqlBase = "SELECT * FROM empleados";
			$this->titulo = "EMPLEADOS DE PARROT SYSTEM";
	}
//-----------------------------------------------------------------------------------------------------------
	function insertNewRecord($f){
		extract($f);
		$r = $this->empleadosInsert($cedula, $apellidos, $nombres, $fecha_nacimiento, $telefono, $direccion, $ciudad, $zona_postal, $condicion);
		return $r;
	}
//-----------------------------------------------------------------------------------------------------------
	function updateRecord($f){
		extract($f);
		$res = $this->empleadosUpdate($apellidos, $nombres, $fecha_nacimiento, $telefono, $direccion, $ciudad, $zona_postal, $condicion, $cedula);
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function deleteRecord($id){
		$res = $this->empleadosDelete("cedula = $id");
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function formAdd(){
		$html = $this->frmempleados();
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function formEdit($id){
		$html = $this->frmempleados($id);
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function frmempleados(){
		$alineacionDerecha = " style='text-align:right' ";
		$numeroReal = " onkeypress='return NumCheck(event, this);'";
		if(func_num_args() > 0){
			$cedula = func_get_arg(0);
			$records = $this->empleadosRecords("cedula = $cedula");
			foreach($records as $record){
				extract($record);
			}
			$fecha_nacimiento = dMySQL_ES($fecha_nacimiento);
			$textoBoton = "Actualizar";
			$accion = "onClick=\"xajax_update('CLS_EMPLEADOS',xajax.getFormValues('frm'))\"";
		}else{
			$pk = "";
			$textoBoton = "Grabar";
			$accion = "onClick=\"xajax_save('CLS_EMPLEADOS',xajax.getFormValues('frm'))\"";
			$cedula = '';
			$apellidos = '';
			$nombres = '';
			$fecha_nacimiento = date('d/m/Y');
			$telefono = '';
			$direccion = '';
			$ciudad = '';
			$zona_postal = '';
			$condicion = 1;
		}
		$htm = "<form id='frm' >
				<table align='center' border='0'>
					<tr><td align='right'><b>Cedula</b>:&nbsp;</td><td>".frm_cedula('cedula', $cedula, '10', '10')."</td></tr>	
					<tr><td align='right'><b>Apellidos</b>:&nbsp;</td><td>".frm_text('apellidos', $apellidos, '20', '20 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Nombres</b>:&nbsp;</td><td>".frm_text('nombres', $nombres, '20', '20 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Fecha_nacimiento</b>:&nbsp;</td><td>".frm_calendario('fecha_nacimiento', 'fecha_nacimiento',$fecha_nacimiento,10)."</td></tr>	
					<tr><td align='right'><b>Telefono</b>:&nbsp;</td><td>".frm_text('telefono', $telefono, '11', '11 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Direccion</b>:&nbsp;</td><td>".frm_text('direccion', $direccion, '50', '145 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Ciudad</b>:&nbsp;</td><td>".frm_text('ciudad', $ciudad, '20', '20 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Zona_postal</b>:&nbsp;</td><td>".frm_text('zona_postal', $zona_postal, '4', '4 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Condici&oacute;n</b>:&nbsp;</td><td>".frm_radio('condicion', 1,$condicion)."&nbsp;Activado &nbsp; &diams;  &nbsp;".frm_radio("condicion", 0, $condicion)."Desactivado"."</td></tr>
					</table>	
			</form>";
	return $htm;	
	}
//-----------------------------------------------------------------------------------------------------------
	function checkAllData($f,$new = 0){		// Considerar colocar los campos obligatorios en el formulario.
		if(empty($f['cedula'])) return "El campo 'cedula' no puede ser nulo.";
		if(empty($f['apellidos'])) return "El campo 'apellidos' no puede ser nulo.";
		if(empty($f['nombres'])) return "El campo 'nombres' no puede ser nulo.";
		if(empty($f['fecha_nacimiento'])) return "El campo 'fecha_nacimiento' no puede ser nulo.";
		if(empty($f['telefono'])) return "El campo 'telefono' no puede ser nulo.";
		if(empty($f['direccion'])) return "El campo 'direccion' no puede ser nulo.";
		if(empty($f['ciudad'])) return "El campo 'ciudad' no puede ser nulo.";
		if(empty($f['zona_postal'])) return "El campo 'zona_postal' no puede ser nulo.";
	 	return 0;
	}
//-----------------------------------------------------------------------------------------------------------
	function blanquearCampos(){		//SI ES PARA EL FORMULARIO, SE PUEDE SUSTITUIR POR reset.
		$xr = new xajaxResponse();
		$xr->assign("cedula","value","");
		$xr->assign("apellidos","value","");
		$xr->assign("nombres","value","");
		$xr->assign("fecha_nacimiento","value","");
		$xr->assign("telefono","value","");
		$xr->assign("direccion","value","");
		$xr->assign("ciudad","value","");
		$xr->assign("zona_postal","value","");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
// Nombres de los campos de la consulta.
	function camposBD(){
		$fields = array();
		$fields[] = 'cedula';	
		$fields[] = 'apellidos';	
		$fields[] = 'nombres';	
		//$fields[] = 'fecha_nacimiento';	
		$fields[] = 'telefono';	
		//$fields[] = 'direccion';	
		$fields[] = 'ciudad';	
		$fields[] = 'zona_postal';	
		return $fields;
	}
//-----------------------------------------------------------------------------------------------------------
	function encabezados(){
		$headers = array();
		//$headers[] = "cedula";
		$headers[] = "apellidos";
		$headers[] = "nombres";
		//$headers[] = "fecha_nacimiento";
		$headers[] = "telefono";
		//$headers[] = "direccion";
		$headers[] = "ciudad";
		$headers[] = "zona_postal";
		return $headers;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosEncabezados(){
		// HTML table: hearders attributes
		$attribsHeader = array();
		//$attribsHeader[] = '13';
		$attribsHeader[] = '13';
		$attribsHeader[] = '13';
		//$attribsHeader[] = '13';
		//$attribsHeader[] = '13';
		$attribsHeader[] = '13';
		$attribsHeader[] = '13';
		$attribsHeader[] = '13';
		return $attribsHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosColumnas(){
		// HTML Table: columns attributes
		$attribsCols = array();
		//$attribsCols[] = 'nowrap style="text-align:left"';
		//$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		return $attribsCols;
	}
//-----------------------------------------------------------------------------------------------------------
	function ascDesEncabezado($CLASE, $limit, $filter, $content, $divName){
		// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
		$eventHeader = array();
		//$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","cedula","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","apellidos","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","nombres","'.$divName.'","ORDERING");return false;\'';
		//$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","fecha_nacimiento","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","telefono","'.$divName.'","ORDERING");return false;\'';
		//$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","direccion","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","ciudad","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","zona_postal","'.$divName.'","ORDERING");return false;\'';
		return $eventHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscar(){
		// Select Box: fields table.
		$fieldsFromSearch = array();
		//$fieldsFromSearch[] = "cedula";
		$fieldsFromSearch[] = "apellidos";
		$fieldsFromSearch[] = "nombres";
		//$fieldsFromSearch[] = "fecha_nacimiento";
		//$fieldsFromSearch[] = "telefono";
		//$fieldsFromSearch[] = "direccion";
		$fieldsFromSearch[] = "ciudad";
		$fieldsFromSearch[] = "zona_postal";
		return $fieldsFromSearch;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscarMostrar(){
		$fieldsFromSearchShowAs = array();
		//$fieldsFromSearchShowAs[] = "CEDULA";
		$fieldsFromSearchShowAs[] = "APELLIDOS";
		$fieldsFromSearchShowAs[] = "NOMBRES";
		//$fieldsFromSearchShowAs[] = "FECHA_NACIMIENTO";
		//$fieldsFromSearchShowAs[] = "TELEFONO";
		//$fieldsFromSearchShowAs[] = "DIRECCION";
		$fieldsFromSearchShowAs[] = "CIUDAD";
		$fieldsFromSearchShowAs[] = "ZONA_POSTAL";
		return	$fieldsFromSearchShowAs;
	}
//-----------------------------------------------------------------------------------------------------------
}
?>
