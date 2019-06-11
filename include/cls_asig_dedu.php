<?php
	class CLS_ASIG_DEDU extends CLS_PARROT{
	var $sqlBase;
	var $titulo;
	var $ordenTabla ="data-order='[[ 0, \"asc\" ],[ 1, \"asc\" ]]'";
//-----------------------------------------------------------------------------------------------------
//	METODOS SIN CAMBIOS DE NINGUN TIPO
//-----------------------------------------------------------------------------------------------------
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
			$this->sqlBase = "SELECT * FROM asig_dedu";
			$this->titulo = "ASIGNACIONES Y/O DEDUCCIONES SALARIALES";
	}
//-----------------------------------------------------------------------------------------------------------
	function insertNewRecord($f){
		extract($f);
		if(!isset($monto_diario)){
			$monto_diario=0;
		}
		if(!isset($formula)){
			$formula="";
		}
		//Convertir la fecha de formato ingles o español a formato MYSQL antes de pasarlo a la funcion.
		$r = $this->asig_deduInsert($tipo, $descripcion, $formula);
		return $r;
	}
//-----------------------------------------------------------------------------------------------------------
	function updateRecord($f){
		extract($f);
		if(!isset($monto_diario)){
			$monto_diario=0;
		}
		if(!isset($formula)){
			$formula="";
		}
		$res = $this->asig_deduUpdate($tipo, $descripcion, $formula, $id_asig_dedu);
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function deleteRecord($id){
		$res = $this->asig_deduDelete("id_asig_dedu = $id");
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function formAdd(){
		$html = $this->frmasig_dedu();
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function formEdit($id){
		$html = $this->frmasig_dedu($id);
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function frmasig_dedu(){
		$a_tipo = array("ASIGNACION","DEDUCCION"); 
		$alineacionDerecha = " style='text-align:right' ";
		$numeroReal = " onkeypress='return NumCheck(event, this);'";
		$accionSelect =" onchange =\"if(this.value==='FIJO'){document.getElementById('idformula').disabled=true;document.getElementById('idDiario').disabled=false;}else{document.getElementById('idformula').disabled=false;document.getElementById('idDiario').disabled=true;}\"";
		if(func_num_args() > 0){
			$id_asig_dedu = func_get_arg(0);
			$records = $this->asig_deduRecords("id_asig_dedu = $id_asig_dedu");
			foreach($records as $record){
				extract($record);
			}
			$textoBoton = "Actualizar";
			$accion = "onClick=\"xajax_update('CLS_ASIG_DEDU',xajax.getFormValues('frm'))\"";
		}else{
			//$pk = "";
			$textoBoton = "Grabar";
			$accion = "onClick=\"xajax_save('CLS_ASIG_DEDU',xajax.getFormValues('frm'))\"";
			$id_asig_dedu = 0;
			$tipo = "class='form-control'";
			$descripcion = '';
			$formula = '';
		}
		$htm = "<div class='row'><div class='col-md-12'><H3>ASIGNACIONES Y/O DEDUCCIONES DE N&Oacute;MINA  </H3>
				<form id='frm' >
				".frm_hidden("id_asig_dedu", $id_asig_dedu)."
				<table align='center' border='0'>
					<tr><td align='right'><b>Descripcion</b>:&nbsp;</td><td>".frm_text('descripcion', $descripcion, '45', '45 ', "required class='form-control'")."</td></tr>	
					<tr><td align='right'><b>Tipo</b>:&nbsp;</td><td>".frm_select("tipo",$a_tipo,$a_tipo,$tipo)."</td></tr>	
					<tr><td align='right'><b>F&oacute;rmula(Diario)</b>:&nbsp;</td><td>".
					frm_text('formula', $formula, '45', '45 ', "id='idformula' ").
					frm_enlace_imagen("#10", "Ayuda", "./imagenes/formula.jpg","help",  "30", "30","title='Ayuda' onclick=\"xajax_mostrarMensaje();\"" )."</td></tr>
				</table>	
			</form></div></div>";
	return $htm;	
	}
	
	function mostrarMensaje(){
		$str_mensaje = "<div id='miMensaje'>
		<center><h3>Para construir la f&oacute;rmula</h3></center>
		<p>Como referentes para la construcción de la <b>fórmula</b> se tienen como &uacute;nicos factores a:</p> 		<p><b>\$S</b>: Salario b&aacute;sico mensual.   </p>
		<p><b>\$UT</b>: Unidad tributaria.</p>
		<p><strong>Ejemplo 1:</strong> Salario b&aacute;sico.  Su f&oacute;rmula es <b>\$S/30</b></p>
		<p><strong>Ejemplo 2:</strong> Bono de alimentaci&oacute;n tendr&aacute; de f&oacute;rmula <b>0.50*\$UT</b></p>
		<p><strong>Ejemplo 3:</strong> F.A.O.V. cuya formula es <b>\$S*.01</b></p>
		<p>Para cualquier caso,se escribir&aacute; solamente la parte derecha de la f&oacute;rmula. Y punto como separador decimal. Los operadores aritm&eacute;ticos son: </p>
		<table align='center'>
			<tr><td>Operaci&oacute;n</td><td align='center'>Operador</td></tr>
			<tr><td>Suma</td><td align='center'>+</td></tr>
			<tr><td>Resta</td><td align='center'>-</td></tr>
			<tr><td>Multiplicaci&oacute;n</td><td>*</td></tr>
			<tr><td>Divisi&oacute;n</td><td align='center'>/</td></tr>
		</table>
	</div>";
			
		$xr = new xajaxResponse();
/*		$xr->script("document.getElementById('ventanaModal').style.visibility='visible';");
		$xr->assign("ventanaModal","innerHTML",$str_mensaje);*/
		$xr->script("aviso(".json_encode($str_mensaje).")");
		return $xr;			
	}	
//-----------------------------------------------------------------------------------------------------------
	function checkAllData($f,$new = 0){		// Considerar colocar los campos obligatorios en el formulario.
		//if(empty($f['id_asig_dedu'])) return "El campo 'id_asig_dedu' no puede ser nulo.";
		if(empty($f['tipo'])) return "El campo 'Tipo' no puede ser nulo.";
		if(empty($f['descripcion'])) return "El campo 'Descripcion' no puede ser nulo.";
		if(empty($f['formula'])) return "El campo 'Formula' no puede ser nulo.";
		//if(empty($f['monto_diario'])) return "El campo 'Monto Diario' no puede ser nulo.";
	 	return 0;
	}
//-----------------------------------------------------------------------------------------------------------
	function blanquearCampos(){		//SI ES PARA EL FORMULARIO, SE PUEDE SUSTITUIR POR reset.
		$xr = new xajaxResponse();
		//$xr->assign("id_asig_dedu","value","");
		$xr->assign("tipo","value","");
		$xr->assign("descripcion","value","");
		//$xr->assign("fijo_formula","value","");
		$xr->assign("monto_diario","value","");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
// Nombres de los campos de la consulta.
	function camposBD(){
		$fields = array();
		$fields[] = 'id_asig_dedu';	
		$fields[] = 'tipo';	
		$fields[] = 'descripcion';	
		//$fields[] = 'fijo_formula';	
		$fields[] = 'formula';
		//$fields[] = 'monto_diario';	
		return $fields;
	}
//-----------------------------------------------------------------------------------------------------------
	function encabezados(){
		$headers = array();
		//$headers[] = "id_asig_dedu";
		$headers[] = "Tipo";
		$headers[] = "Descripcion";
		//$headers[] = "Fijo o Formula";
		$headers[] = "F&oacute;rmula";
		//$headers[] = "Monto Diario";
		return $headers;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosEncabezados(){
		// HTML table: hearders attributes
		$attribsHeader = array();
		$attribsHeader[] = '20';
		$attribsHeader[] = '20';
		$attribsHeader[] = '20';
		//$attribsHeader[] = '20';
		//$attribsHeader[] = '20';
		return $attribsHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosColumnas(){
		// HTML Table: columns attributes
		$attribsCols = array();
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
/*		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:right"';*/
		return $attribsCols;
	}
//-----------------------------------------------------------------------------------------------------------
	function ascDesEncabezado($CLASE, $limit, $filter, $content, $divName){
		// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
		$eventHeader = array();
		//$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","id_asig_dedu","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","tipo","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","descripcion","'.$divName.'","ORDERING");return false;\'';
		//$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","fijo_formula","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","formula","'.$divName.'","ORDERING");return false;\'';
		//$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","monto_diario","'.$divName.'","ORDERING");return false;\'';
		return $eventHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscar(){
		// Select Box: fields table.
		$fieldsFromSearch = array();
		//$fieldsFromSearch[] = "id_asig_dedu";
		$fieldsFromSearch[] = "tipo";
		$fieldsFromSearch[] = "descripcion";
		//$fieldsFromSearch[] = "fijo_formula";
		//$fieldsFromSearch[] = "monto_diario";
		return $fieldsFromSearch;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscarMostrar(){
		$fieldsFromSearchShowAs = array();
		//$fieldsFromSearchShowAs[] = "ID_ASIG_DEDU";
		$fieldsFromSearchShowAs[] = "TIPO";
		$fieldsFromSearchShowAs[] = "DESCRIPCION";
		//$fieldsFromSearchShowAs[] = "FIJO_FORMULA";
		//$fieldsFromSearchShowAs[] = "MONTO_DIARIO";
		return	$fieldsFromSearchShowAs;
	}
//-----------------------------------------------------------------------------------------------------------
}
?>
