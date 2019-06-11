<?php
	class CLS_USER extends CLS_PARROT{
	var $sqlBase;
	var $titulo;
	var $ordenTabla ="data-order='[[ 1, \"asc\" ]]'";
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
			$this->sqlBase = "SELECT * FROM user";
			$this->titulo = "LISTADO DE USUARIOS";
	}
//-----------------------------------------------------------------------------------------------------------
	function insertNewRecord($f){
		extract($f);
		//Convertir la fecha de formato ingles o español a formato MYSQL antes de pasarlo a la funcion.
		$r = $this->userInsert($username, $password, $email);
		return $r;
	}
//-----------------------------------------------------------------------------------------------------------
	function updateRecord($f){
		extract($f);
		$res = $this->userUpdate($username, $password, $email, $id);
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function deleteRecord($id){
		$res = $this->userDelete("id = $id");
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function formAdd(){
		$html = $this->frmuser();
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function formEdit($id){
		$html = $this->frmuser($id);
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function frmuser(){
	  	if(func_num_args() > 0){
			$id = func_get_arg(0);
			$records = $this->userRecords("id = $id");
			foreach($records as $record){
				extract($record);
			}
			$textoBoton = "Actualizar";
			$accion = "onClick=\"xajax_update('CLS_USER',xajax.getFormValues('frm'))\"";
			$titulo = "Editar Usuario";
		}else{
			$pk = "";
			$textoBoton = "Grabar";
			$accion = "onClick=\"xajax_save('CLS_USER',xajax.getFormValues('frm'))\"";
			$id = 0;
			$password = '';
			$email = '';
			$username = '';
			$titulo = "Nuevo Usuario";
		}
		$htm = "<div class='row'><div class='col-md-12'>";
		$htm .= "<h3><p class='text-center'>$titulo</p></h3><br/>";
		$htm .= "<form id='frm' >
				<table align='center' border='0'>
					<tr><td align='right'><b>Nombre de usuario:</b>:&nbsp;</td><td>".frm_text('username', $username, '20', '20 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Email:</b>:&nbsp;</td><td>".frm_text('email', $email, '50', '50 ', 'required')."</td></tr>	
					<tr><td align='right'><b>Clave:</b>:&nbsp;</td><td>".frm_text('password', $password, '50', '50 ', 'required')."</td></tr>	
				</table>	
			</form></div></div>";
	return $htm;	
	}
//-----------------------------------------------------------------------------------------------------------
	function checkAllData($f,$new = 0){		// Considerar colocar los campos obligatorios en el formulario.
		if(empty($f['password'])) return "El campo 'clave' no puede ser nulo.";
		if(empty($f['email'])) return "El campo 'email' no puede ser nulo.";
		if(empty($f['username'])) return "El campo 'usuario' no puede ser nulo.";
	 	return 0;
	}
//-----------------------------------------------------------------------------------------------------------
	function blanquearCampos(){		//SI ES PARA EL FORMULARIO, SE PUEDE SUSTITUIR POR reset.
		$xr = new xajaxResponse();
		$xr->assign("id","value","");
		$xr->assign("password","value","");
		$xr->assign("email","value","");
		$xr->assign("username","value","");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
// Nombres de los campos de la consulta.
	function camposBD(){
		$fields = array();
		$fields[] = 'id';	
		//$fields[] = 'password';	
		$fields[] = 'email';	
		$fields[] = 'username';	
		return $fields;
	}
//-----------------------------------------------------------------------------------------------------------
	function encabezados(){
		$headers = array();
		$headers[] = "email";
		$headers[] = "username";
		return $headers;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosEncabezados(){
		// HTML table: hearders attributes
		$attribsHeader = array();
		$attribsHeader[] = '25';
		$attribsHeader[] = '25';
		return $attribsHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosColumnas(){
		// HTML Table: columns attributes
		$attribsCols = array();
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		return $attribsCols;
	}
//-----------------------------------------------------------------------------------------------------------
	function ascDesEncabezado($CLASE, $limit, $filter, $content, $divName){
		// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
		$eventHeader = array();
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","email","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","username","'.$divName.'","ORDERING");return false;\'';
		return $eventHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscar(){
		// Select Box: fields table.
		$fieldsFromSearch = array();
		$fieldsFromSearch[] = "email";
		$fieldsFromSearch[] = "username";
		return $fieldsFromSearch;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscarMostrar(){
		$fieldsFromSearchShowAs = array();
		$fieldsFromSearchShowAs[] = "EMAIL";
		$fieldsFromSearchShowAs[] = "USERNAME";
		return	$fieldsFromSearchShowAs;
	}
//-----------------------------------------------------------------------------------------------------------
}
?>
