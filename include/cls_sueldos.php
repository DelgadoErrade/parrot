<?php
	class CLS_SUELDOS extends CLS_PARROT{
	var $sqlBase;
	var $nombres;
	var $cedulas;
	var $titulo;
	var $ordenTabla ="data-order='[[ 3, \"desc\" ],[ 0, \"asc\" ]]'";
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
		$this->sqlBase = "SELECT * FROM (SELECT id_sueldo, CONCAT(apellidos, ', ', 
			nombres) AS nombre, sueldos.cedula, cargo, sueldo_mensual, fecha 
			FROM sueldos 
			INNER JOIN empleados ON empleados.cedula = sueldos.cedula) AS Z";
			$this->titulo = "HISTORICO DE SUELDOS DE LOS EMPLEADOS";
		$sql = "SELECT CONCAT( apellidos, ', ', nombres ) AS nombre, cedula FROM empleados ORDER BY apellidos";
		$empleados = $this->consultagenerica($sql);
		foreach($empleados as $empleado){
			extract($empleado);
			$nombres[]=$nombre;
			$cedulas[]=$cedula;		
		}
		$this->nombres = $nombres;
		$this->cedulas = $cedulas;
	}
//-----------------------------------------------------------------------------------------------------------
	function insertNewRecord($f){
		extract($f);
		//Convertir la fecha de formato ingles o español a formato MYSQL antes de pasarlo a la funcion.
		$sueldo_mensual = numeroIngles($sueldo_mensual);
		$r = $this->sueldosInsert($cedula, $cargo, $sueldo_mensual, $fecha);
		return $r;
	}
//-----------------------------------------------------------------------------------------------------------
	function updateRecord($f){
		extract($f);
		$sueldo_mensual = numeroIngles($sueldo_mensual);
		$res = $this->sueldosUpdate( $cedula, $cargo, $sueldo_mensual, $fecha, $id_sueldo);
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function deleteRecord($id){
		$res = $this->sueldosDelete("id_sueldo = $id");
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function formAdd(){
		$html = $this->frmsueldos();
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function formEdit($id){
		$html = $this->frmsueldos($id);
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function frmsueldos(){
		$alineacionDerecha = " style='text-align:right' ";
		$numeroReal = " onkeypress='return NumCheck(event, this);'";
		if(func_num_args() > 0){
			$id_sueldo = func_get_arg(0);
			$records = $this->sueldosRecords("id_sueldo = $id_sueldo");
			foreach($records as $record){
				extract($record);
			}
			$fecha = dMySQL_ES($fecha);
			$textoBoton = "Actualizar";
			$accion = "onClick=\"xajax_update('CLS_SUELDOS',xajax.getFormValues('frm'))\"";
			$titulo = "Editar sueldo";
		}else{
			$pk = "";
			$textoBoton = "Grabar";
			$accion = "onClick=\"xajax_save('CLS_SUELDOS',xajax.getFormValues('frm'))\"";
			$id_sueldo = "";
			$cedula = "";
			$cargo = '';
			$sueldo_mensual = "";
			$fecha = date('d/m/Y');
			$id_sueldo ="";
			$titulo = "Nuevo sueldo";
		}
		$htm = "<div class='row'><div class='col-md-12'>";
		$htm = "<h3><p class='text-center'>$titulo</p></h3>";	
		$htm .= "<form id='frm' >
			".frm_hidden('id_sueldo', $id_sueldo)."
				<table align='center' border='0'><tr><td align='right'><b>Nombre</b></td><td>".
					frm_select("cedula", $this->nombres, $this->cedulas,$cedula)
				."</td></tr>	<tr><td align='right'><b>Cargo</b>:&nbsp;</td><td>".frm_text('cargo', $cargo, '30', '30 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Sueldo_mensual</b>:&nbsp;</td><td>".frm_numero('sueldo_mensual', $sueldo_mensual, '10', '10')."</td></tr>	
					<tr><td align='right'><b>Fecha</b>:&nbsp;</td><td>".frm_calendario('fecha','fecha' ,$fecha, "id='fecha' required")."</td></tr>	
				</table>	
			</form> </div></div>";
			  
	return $htm;	
	}
//-----------------------------------------------------------------------------------------------------------
	function checkAllData($f,$new = 0){		// Considerar colocar los campos obligatorios en el formulario.
		//if(empty($f['id_sueldo'])) return "El campo 'id_sueldo' no puede ser nulo.";
		if(empty($f['cedula'])) return "El campo 'cedula' no puede ser nulo.";
		if(empty($f['cargo'])) return "El campo 'cargo' no puede ser nulo.";
		if(empty($f['sueldo_mensual'])) return "El campo 'sueldo_mensual' no puede ser nulo.";
		if(empty($f['fecha'])) return "El campo 'fecha' no puede ser nulo.";
	 	return 0;
	}
//-----------------------------------------------------------------------------------------------------------
	function blanquearCampos(){		//SI ES PARA EL FORMULARIO, SE PUEDE SUSTITUIR POR reset.
		$xr = new xajaxResponse();
		$xr->assign("id_sueldo","value","");
		$xr->assign("nombre","value","");
		$xr->assign("cedula","value","");
		$xr->assign("cargo","value","");
		$xr->assign("sueldo_mensual","value","");
		$xr->assign("fecha","value","");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
// Nombres de los campos de la consulta.
	function camposBD(){
		$fields = array();
		$fields[] = 'id_sueldo';
		$fields[] = 'nombre';	
		$fields[] = 'cedula';	
		$fields[] = 'cargo';	
		$fields[] = 'sueldo_mensual';	
		$fields[] = 'fecha';	
		return $fields;
	}
//-----------------------------------------------------------------------------------------------------------
	function encabezados(){
		$headers = array();
		$headers[] = "nombre";
		$headers[] = "cedula";
		$headers[] = "cargo";
		$headers[] = "sueldo_mensual";
		$headers[] = "fecha";
		return $headers;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosEncabezados(){
		// HTML table: hearders attributes
		$attribsHeader = array();
		$attribsHeader[] = '20';
		$attribsHeader[] = '20';
		$attribsHeader[] = '20';
		$attribsHeader[] = '20';
		$attribsHeader[] = '20';
		return $attribsHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosColumnas(){
		// HTML Table: columns attributes
		$attribsCols = array();
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:right"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		return $attribsCols;
	}
//-----------------------------------------------------------------------------------------------------------
	function ascDesEncabezado($CLASE, $limit, $filter, $content, $divName){
		// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
		$eventHeader = array();
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","nombre","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","cedula","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","cargo","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","sueldo_mensual","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","fecha","'.$divName.'","ORDERING");return false;\'';
		return $eventHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscar(){
		// Select Box: fields table.
		$fieldsFromSearch = array();
		$fieldsFromSearch[] = "nombre";
		//$fieldsFromSearch[] = "cedula";
		$fieldsFromSearch[] = "cargo";
		//$fieldsFromSearch[] = "sueldo_mensual";
		//$fieldsFromSearch[] = "fecha";
		return $fieldsFromSearch;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscarMostrar(){
		$fieldsFromSearchShowAs = array();
		$fieldsFromSearchShowAs[] = "NOMBRE";
		//$fieldsFromSearchShowAs[] = "CEDULA";
		$fieldsFromSearchShowAs[] = "CARGO";
		//$fieldsFromSearchShowAs[] = "SUELDO_MENSUAL";
		//$fieldsFromSearchShowAs[] = "FECHA";
		return	$fieldsFromSearchShowAs;
	}
//-----------------------------------------------------------------------------------------------------------
}
?>
