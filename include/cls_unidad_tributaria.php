<?php
	class CLS_UNIDAD_TRIBUTARIA extends CLS_PARROT{
	var $sqlBase;
	var $titulo;
	var $ordenTabla ="data-order='[[ 0, \"desc\" ]]'";
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
			$this->sqlBase = "SELECT * FROM unidad_tributaria";
			$this->titulo = "VALORES DE LA UNIDAD TRIBUTARIA";
	}
//-----------------------------------------------------------------------------------------------------------
	function insertNewRecord($f){
		extract($f);
		//Convertir la fecha de formato ingles o español a formato MYSQL antes de pasarlo a la funcion.
		$unidad_tributaria = numeroIngles($unidad_tributaria);
		$r = $this->unidad_tributariaInsert($fecha, $unidad_tributaria);
		return $r;
	}
//-----------------------------------------------------------------------------------------------------------
	function updateRecord($f){
		extract($f);
		$res = $this->unidad_tributariaUpdate($fecha, $unidad_tributaria, $id_unidad_tributaria);
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function deleteRecord($id){
		$res = $this->unidad_tributariaDelete("id_unidad_tributaria = $id");
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function formAdd(){
		$html = $this->frmunidad_tributaria();
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function formEdit($id){
		$html = $this->frmunidad_tributaria($id);
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function frmunidad_tributaria(){
		$alineacionDerecha = " style='text-align:right' ";
		$numeroReal = " onkeypress='return NumCheck(event, this);'";
		if(func_num_args() > 0){
			$id_unidad_tributaria = func_get_arg(0);
			$records = $this->unidad_tributariaRecords("id_unidad_tributaria = $id_unidad_tributaria");
			foreach($records as $record){
				extract($record);
			}
			$fecha = dMySQL_ES($fecha);
			$textoBoton = "Actualizar";
			$accion = "onClick=\"xajax_update('CLS_UNIDAD_TRIBUTARIA',xajax.getFormValues('frm'))\"";
			$pk = $id_unidad_tributaria;
			$titulo = "Editar Unidad Tributaria";
		}else{
			$pk = "";
			$textoBoton = "Grabar";
			$accion = "onClick=\"xajax_save('CLS_UNIDAD_TRIBUTARIA',xajax.getFormValues('frm'))\"";
			$id_unidad_tributaria = 0;
			$unidad_tributaria = "";
			$fecha = date('d/m/Y');
			$titulo = "Nueva Unidad Tributaria";
		}
		$htm = "<div class='row'><div class='col-md-12'>";
		$htm .= "<h3><p class='text-center'>$titulo</p></h3><br/>";
		$htm .= "<form id='frm' >
				".frm_hidden("id_unidad_tributaria",$pk)."
				<table align='center' border='0'>
				<tr><td align='right'><b>Fecha</b>:&nbsp;</td><td>".frm_calendario('fecha','fecha', $fecha, "id='fecha'")."</td></tr>	
					<tr><td align='right'><b>Unidad Tributaria</b>:&nbsp;</td><td>".frm_numero('unidad_tributaria', $unidad_tributaria, '10', '10')."</td></tr>	
				</table>	
			</form></div></div>";
	return $htm;	
	}
//-----------------------------------------------------------------------------------------------------------
	function checkAllData($f,$new = 0){		// Considerar colocar los campos obligatorios en el formulario.
		if(empty($f['fecha'])) return "El campo 'fecha' no puede ser nulo.";
		if(empty($f['unidad_tributaria'])) return "El campo 'unidad tributaria' no puede ser nulo.";
	 	return 0;
	}
//-----------------------------------------------------------------------------------------------------------
	function blanquearCampos(){		//SI ES PARA EL FORMULARIO, SE PUEDE SUSTITUIR POR reset.
		$xr = new xajaxResponse();
		$xr->assign("id_unidad_tributaria","value","");
		$xr->assign("fecha","value","");
		$xr->assign("unidad_tributaria","value","");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
// Nombres de los campos de la consulta.
	function camposBD(){
		$fields = array();
		$fields[] = 'id_unidad_tributaria';	
		$fields[] = 'fecha';	
		$fields[] = 'unidad_tributaria';	
		return $fields;
	}
//-----------------------------------------------------------------------------------------------------------
	function encabezados(){
		$headers = array();
		//$headers[] = "id_unidad_tributaria";
		$headers[] = "fecha";
		$headers[] = "unidad_tributaria";
		return $headers;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosEncabezados(){
		// HTML table: hearders attributes
		$attribsHeader = array();
		//$attribsHeader[] = '33';
		$attribsHeader[] = '33';
		$attribsHeader[] = '33';
		return $attribsHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosColumnas(){
		// HTML Table: columns attributes
		$attribsCols = array();
		//$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		$attribsCols[] = 'nowrap style="text-align:right"';
		return $attribsCols;
	}
//-----------------------------------------------------------------------------------------------------------
	function ascDesEncabezado($CLASE, $limit, $filter, $content, $divName){
		// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
		$eventHeader = array();
		//$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","id_unidad_tributaria","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","fecha","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","unidad_tributaria","'.$divName.'","ORDERING");return false;\'';
		return $eventHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscar(){
		// Select Box: fields table.
		$fieldsFromSearch = array();
		//$fieldsFromSearch[] = "id_unidad_tributaria";
		$fieldsFromSearch[] = "fecha";
		//$fieldsFromSearch[] = "unidad_tributaria";
		return $fieldsFromSearch;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscarMostrar(){
		$fieldsFromSearchShowAs = array();
		//$fieldsFromSearchShowAs[] = "ID_UNIDAD_TRIBUTARIA";
		$fieldsFromSearchShowAs[] = "FECHA";
		//$fieldsFromSearchShowAs[] = "UNIDAD_TRIBUTARIA";
		return	$fieldsFromSearchShowAs;
	}
//-----------------------------------------------------------------------------------------------------------
}
?>
