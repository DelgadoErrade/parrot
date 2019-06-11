<?php
	class CLS_TBL_TIPO_MOVIMIENTOS extends CLS_PARROT{
	var $sqlBase;
	var $titulo;
	var $ordenTabla ="data-order='[[ 0, \"asc\" ]]'";
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
			$this->sqlBase = "SELECT * FROM tbl_tipo_movimientos";
			$this->titulo = "TIPOS DE MOVIMIENTOS";
	}
//-----------------------------------------------------------------------------------------------------------
	function insertNewRecord($f){
		extract($f);
		//Convertir la fecha de formato ingles o español a formato MYSQL antes de pasarlo a la funcion.
		$r = $this->tbl_tipo_movimientosInsert($tipo_movimiento, $abreviatura, $operacion);
		return $r;
	}
//-----------------------------------------------------------------------------------------------------------
	function updateRecord($f){
		extract($f);
		$res = $this->tbl_tipo_movimientosUpdate($tipo_movimiento, $abreviatura, $operacion, $id_tipo_movimiento);
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function deleteRecord($id){
		$res = $this->tbl_tipo_movimientosDelete("id_tipo_movimiento = $id");
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function formAdd(){
		$html = $this->frmtbl_tipo_movimientos();
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function formEdit($id){
		$html = $this->frmtbl_tipo_movimientos($id);
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function frmtbl_tipo_movimientos(){
		$alineacionDerecha = " style='text-align:right' ";
		$numeroReal = " onkeypress='return NumCheck(event, this);'";
		if(func_num_args() > 0){
			$id_tipo_movimiento = func_get_arg(0);
			$records = $this->tbl_tipo_movimientosRecords("id_tipo_movimiento = $id_tipo_movimiento");
			foreach($records as $record){
				extract($record);
			}
			$textoBoton = "Actualizar";
			$accion = "onClick=\"xajax_update('CLS_TBL_TIPO_MOVIMIENTOS',xajax.getFormValues('frm'))\"";
		}else{
			//$pk = "";
			$textoBoton = "Grabar";
			$accion = "onClick=\"xajax_save('CLS_TBL_TIPO_MOVIMIENTOS',xajax.getFormValues('frm'))\"";
			$id_tipo_movimiento = 0;
			$tipo_movimiento = '';
			$abreviatura = '';
			$operacion = '';
		}
		$htm = "<form id='frm' >".frm_hidden("id_tipo_movimiento", $id_tipo_movimiento)."
				<table align='center' border='0'>
					<tr><td align='right'><b>Tipo_movimiento</b>:&nbsp;</td><td>".frm_text('tipo_movimiento', $tipo_movimiento, '15', '15 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Abreviatura</b>:&nbsp;</td><td>".frm_text('abreviatura', $abreviatura, '3', '3 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Operacion</b>:&nbsp;</td><td>".frm_text('operacion', $operacion, '1', '1 ', 'required')."</td></tr>	
				</table>	
			</form>";
	return $htm;	
	}
//-----------------------------------------------------------------------------------------------------------
	function checkAllData($f,$new = 0){		// Considerar colocar los campos obligatorios en el formulario.
		//if(empty($f['id_tipo_movimiento'])) return "El campo 'id_tipo_movimiento' no puede ser nulo.";
		if(empty($f['tipo_movimiento'])) return "El campo 'Tipo Movimiento' no puede ser nulo.";
		if(empty($f['abreviatura'])) return "El campo 'Abreviatura' no puede ser nulo.";
		if(empty($f['operacion'])) return "El campo 'Operacion' no puede ser nulo.";
	 	return 0;
	}
//-----------------------------------------------------------------------------------------------------------
	function blanquearCampos(){		//SI ES PARA EL FORMULARIO, SE PUEDE SUSTITUIR POR reset.
		$xr = new xajaxResponse();
		$xr->assign("id_tipo_movimiento","value","");
		$xr->assign("tipo_movimiento","value","");
		$xr->assign("abreviatura","value","");
		$xr->assign("operacion","value","");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
// Nombres de los campos de la consulta.
	function camposBD(){
		$fields = array();
		$fields[] = 'id_tipo_movimiento';	
		$fields[] = 'tipo_movimiento';	
		$fields[] = 'abreviatura';	
		$fields[] = 'operacion';	
		return $fields;
	}
//-----------------------------------------------------------------------------------------------------------
	function encabezados(){
		$headers = array();
		//$headers[] = "id_tipo_movimiento";
		$headers[] = "Tipo Movimiento";
		$headers[] = "Abreviatura";
		$headers[] = "Operacion";
		return $headers;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosEncabezados(){
		// HTML table: hearders attributes
		$attribsHeader = array();
		//$attribsHeader[] = '25';
		$attribsHeader[] = '25';
		$attribsHeader[] = '25';
		$attribsHeader[] = '25';
		return $attribsHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosColumnas(){
		// HTML Table: columns attributes
		$attribsCols = array();
		//$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		return $attribsCols;
	}
//-----------------------------------------------------------------------------------------------------------
	function ascDesEncabezado($CLASE, $limit, $filter, $content, $divName){
		// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
		$eventHeader = array();
		//$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","id_tipo_movimiento","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","tipo_movimiento","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","abreviatura","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","operacion","'.$divName.'","ORDERING");return false;\'';
		return $eventHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscar(){
		// Select Box: fields table.
		$fieldsFromSearch = array();
		//$fieldsFromSearch[] = "id_tipo_movimiento";
		$fieldsFromSearch[] = "tipo_movimiento";
		$fieldsFromSearch[] = "abreviatura";
		//$fieldsFromSearch[] = "operacion";
		return $fieldsFromSearch;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscarMostrar(){
		$fieldsFromSearchShowAs = array();
		//$fieldsFromSearchShowAs[] = "ID_TIPO_MOVIMIENTO";
		$fieldsFromSearchShowAs[] = "TIPO MOVIMIENTO";
		$fieldsFromSearchShowAs[] = "ABREVIATURA";
		//$fieldsFromSearchShowAs[] = "OPERACION";
		return	$fieldsFromSearchShowAs;
	}
//-----------------------------------------------------------------------------------------------------------
}
?>
