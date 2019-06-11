<?php
/*	include_once("cls_parrot.php");
	include_once("form_items.php");
	require_once('xajaxGrid.inc.php');
	require_once("funciones_fecha.php");*/
	class CLS_COMPROBANTES extends CLS_PARROT{
	var $sqlBase;
	var $titulo;
	var $ordenTabla ="data-order='[[ 0, \"desc\" ]]'";	//La primera columna en orden descendente.
//-----------------------------------------------------------------------------------------------------------
//	METODOS SIN CAMBIOS DE NINGUN TIPO
//-----------------------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------------------	

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
			$this->sqlBase = "SELECT * FROM comprobantes"; // ORDER BY n_comprobante DESC
			$this->titulo = "HISTORICO DE COMPROBANTES.";
	}
//-----------------------------------------------------------------------------------------------------------
	function insertNewRecord($f){
		extract($f);
		// VALIDA QUE EL REGISTRO NO EXISTA EN LA BASE DE DATOS. 
		$sql = "SELECT COUNT(id_comprobantes) as n FROM comprobantes WHERE 
		    factura = '$factura' AND fecha_factura = '".d_ES_MYSQL($fecha_factura)."'  AND beneficiario='$beneficiario'";
   	  	$valida = $this->consultagenerica($sql);
   	  	if($valida[0]["n"] == 0){ // Si n = 0, entonces no existe otro registro igual. Por ende, se guardan los datos.
			$r = $this->comprobantesInsert($numero, $factura, $fecha_factura, $beneficiario, $cancela, $fecha_cancela);
			$r = $this->nuevoMovBancario($f);				
		}else{
			$r = false;
		}
/*   	  	$resp = $this->consultagenerica("call prc_nuevoMovimiento($id_comprobantes,$numero,
  	  		'$factura', '$fecha_factura', '$beneficiario', '$cancela', '$fecha_cancela')");
		$r = FALSE;
		if($resp[0]["errno"]==0){
			$bd = new CLS_COMPROBANTES();
			//$r = TRUE;
			//echo "Registro grabado exitosamente.";
		}elseif($resp[0]["errno"]==1){
			//echo utf8_decode("La factura $factura ya fue registrada");
		}
		// AGREGA NUEVO REGISTRO EN LA TABLA COMPROBANTES. 
		//$r = $this->comprobantesInsert($numero, $factura, $fecha_factura, $beneficiario, $cancela, $fecha_cancela);
		// AGREGA NUEVO REGISTRO EN LA TABLA DE MOVIMIENTOS BANCARIOS.*/
 		return $r;
	}
//-----------------------------------------------------------------------------------------------------------
	function nuevoMovBancario($formulario){
		extract($formulario);
		$parrot = new CLS_PARROT;
		$id_comp = $parrot->max_id("comprobantes","id_comprobantes");
		for($i=0;$i<4;$i++){
			$monto[$i] =  numeroIngles($monto[$i]); //Primero se convierte el numero a formato ingles
 			if($monto[$i] > 0){
				$r = $this->pagosInsert($id_comp,$forma_pago[$i], $banco[$i], $referencia[$i], $monto[$i]);
				if(strtoupper($banco[$i]) != "CAJA"){
					$regBancos = $parrot->tbl_bancosRecords("banco like '%".$banco[$i]."%'");
					$tipoMovBanco = $parrot->tbl_tipo_movimientosRecords("tipo_movimiento like '".$forma_pago[$i]."'");
					$cantidad = -1*$monto[$i];
			// AGREGA NUEVO REGISTRO EN LA TABLA tbl_mov_bancarios		
					$r = $this->tbl_mov_bancariosInsert($regBancos[0]["idbanco"], 
						$tipoMovBanco[0]["id_tipo_movimiento"], 
						$numero,
						d_ES_MYSQL($fecha_cancela), 
						$referencia[$i],
						$beneficiario,
						$cantidad);
				}
			}
		}	
		return $r;	
	}	
//-----------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------
	function updateRecord($f){
		extract($f);
		$parrot = new CLS_PARROT;
		// ACTUALIZA LA TABLA COMPROBANTES.
		$res = $this->comprobantesUpdate($numero, $factura, $fecha_factura, $beneficiario, $cancela, $fecha_cancela,$id_comprobantes);
		$r = $this->pagosDelete("id_comprobantes = $id_comprobantes");
		$r = $this->tbl_mov_bancariosDelete("n_comprobante = $numero");
		$res = $this->nuevoMovBancario($f);
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function repor_comprobante($id){
		setcookie("id", $id, time()+6000);		//Crea cookie con duración de seis (6) minutos.
		$reporte = "./include/pdf_comprobantes.php";
		$xr = new xajaxResponse();
		//$n = 0;
		$xr->script("var newWin = window.open('$reporte', '_blank', 'fullscreen=yes')");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
	function deleteRecord($id){
		$res = $this->pagosDelete("id_comprobantes = $id");
		$res = $this->comprobantesDelete("id_comprobantes = $id");
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function formAdd(){
		$html = $this->frmcomprobantes();
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function formEdit($id){
		$html = $this->frmcomprobantes($id);
		return $html;
	}
//--------------------------------------------------------------------------------------------------------
/*	function sumar($formulario){		// Hacer esta funcion en javascript.
		extract($formulario);
		$total = 0;
		for($i=0;$i<4;$i++){
			$sumando = numeroIngles($monto[$i]);
			if(is_numeric($sumando)){
				//$sumando = numeroIngles()
				$total += $sumando;
			}
		}
		$xr = new xajaxResponse();
		$xr->assign("total","innerHTML", numeroEspanol($total));
		return $xr;
	}*/
//-----------------------------------------------------------------------------------------------------------
	function frmcomprobantes(){
		$alineacionDerecha = "style='text-align:right' ";
		$numeroReal = " onkeypress='return NumCheck(event, this, 10, 2);'";
		$formaPago = array("Cheque","Debito","Efectivo","Transferencia");
		$fuente = array("Banesco","Caja","Mercantil");
		$accion2 = "xajax_repor_comprobante(document.getElementById('numero').value);";
		$blanquear = "onfocus=\"if(this.value == '0') { this.value = ''; };\"";
		$sumar = "oninput='sumar(this.value);'";//"oninput=\"xajax_sumar(xajax.getFormValues('frm'));\"";
		$total = 0;
		if(func_num_args() > 0){
			$id_comprobantes = func_get_arg(0);
			$records = $this->comprobantesRecords("id_comprobantes = $id_comprobantes");
			foreach($records as $record){
				extract($record);
			}
			$fecha_cancela = dMySQL_ES($fecha_cancela);
   	  		$fecha_factura = dMySQL_ES($fecha_factura);
			$textoBoton = "Actualizar";
			$accion = "onClick=\"xajax_update('CLS_COMPROBANTES',xajax.getFormValues('frm'));$accion2\"; ";
			$titulo = "Editar Comprobante";
			$pagos = $this->pagosRecords("id_comprobantes = $id_comprobantes");
			$i = 0;
			$numero = 	$n_comprobante;
			if(is_array($pagos)){
				foreach($pagos as $fila){
					extract($fila);
					$f[$i]	=	$forma_pago;
					$b[$i]	=	$banco;
					$r[$i]	=	$referencia;
					$m[$i]	= 	numeroEspanol($monto_pago);
					$numero = 	$n_comprobante;	
					$total += 	$monto_pago;
					$i++;				
				}
				$total = numeroEspanol($total);
				//$i--;
			}
			if($i<=3){
				for($j=$i;$j<4;$j++){
					$f[$j]	=	"";
					$b[$j]	=	"";
					$r[$j]	=	"";
					$m[$j]	= 	0;
				}
			}
		}else{
			for($i=0;$i<4;$i++){
				$f[$i]	=	"Transferencia";
				$b[$i]	=	"Banesco";
				$r[$i]	=	"";
				$m[$i]	= 	0;
			}
			$titulo = "<center><h3>Nuevo Comprobante</h3></center>";
			$id_comprobantes = "";
			$numero = $this->nuevo_id("comprobantes","n_comprobante");
			$factura = "";
			$fecha_factura = date("d/m/Y");
			$beneficiario = "";
			$cancela = "";
			$fecha_cancela = date("d/m/Y");
			$textoBoton = "Grabar";
			$accion = "onClick=\"xajax_save('CLS_COMPROBANTES',xajax.getFormValues('frm')); $accion2\";";
		}
		$forma = "align='center' style='padding-top:14px; padding-bottom: 14px; font-weight:bold; font-size:14px'";
		$tabla1 = " $titulo
			<div class='col-md-6'>
				<table align='center'  class='table'>
				<tr><td class='forma text-right'><b>N&uacute;mero:</b>&nbsp;</td><td>"
					.frm_text('numero', $numero, '4', '4 ', "autofocus required id='numero' autocomplete='off' class='form-control'")."</td></tr>	
				<tr><td class='forma text-right' ><b>Factura:</b>&nbsp;</td><td>"
					.frm_text('factura', $factura, '8', '8 ', "required autocomplete='off'  id='idFactura' class='form-control'")."</td></tr>	
				<tr><td  class='forma text-right'><b>Fecha_factura:</b>&nbsp;</td><td>"
					.frm_calendario2('fecha_factura','fecha_factura', $fecha_factura, "id='fecha_factura' class='form-control'")."</td></tr>	
				<tr><td  class='forma text-right'><b>Beneficiario:</b>&nbsp;</td><td>"
					.frm_text('beneficiario', $beneficiario, '30', '45 ', "required id='beneficiarios' autocomplete='on' class='form-control'")."</td></tr>	
				<tr><td  class='forma text-right'><b>Cancela:</b>&nbsp;</td><td>"
					.frm_text('cancela', $cancela, '20', '20 ', "required id='pagadores' autocomplete='on' class='form-control'")."</td></tr>	
				<tr><td  class='forma text-right'><b>Fecha_cancela:</b>&nbsp;</td><td>"
					.frm_calendario2('fecha_cancela','fecha_cancela', $fecha_cancela,"id='fecha_cancela' class='form-control'")."</td></tr>
				</table>
			</div>	";
		$tabla2 = "	<div class='col-md-6'>
				<table class='table'>
					<tr><th class='forma text-center'>Forma pago</th>
					<th class='forma text-center'>Fuente</th>
					<th class='forma text-center'>Ref. No.</th>
					<th class='forma text-center'>Monto</th></tr>
					<tr><td>".frm_select("forma_pago[0]",$formaPago,$formaPago,$f[0],"class='form-control' id='fp0' onchange='cambiaFp(0);'")."</td>
					<td>".frm_select("banco[0]",$fuente, $fuente, $b[0],"class='form-control' id='bnc0'")."</td>
					<td>".frm_text("referencia[0]",$r[0], 6, 6,"class='form-control' id='ref0'")."</td>
					<td>".frm_text("monto[0]",$m[0], 12, 14, $numeroReal." id='m0' style='text-align:right' $blanquear $sumar class='form-control'")."</td>
					</tr>
					<tr><td>".frm_select("forma_pago[1]",$formaPago,$formaPago,$f[1],"class='form-control' id='fp1' onchange='cambiaFp(1);'")."</td>
					<td>".frm_select("banco[1]",$fuente, $fuente, $b[1], "class='form-control'  id='bnc1'")."</td>
					<td>".frm_text("referencia[1]",$r[1], 6, 6, "class='form-control' id='ref1'")."</td>
					<td>".frm_text("monto[1]",$m[1], 12, 14,  $numeroReal." id='m1' style='text-align:right'  $blanquear $sumar class='form-control'")."</td>
					</tr>
					<tr><td>".frm_select("forma_pago[2]",$formaPago,$formaPago,$f[2],"class='form-control' id='fp2' onchange='cambiaFp(2);'")."</td>
					<td>".frm_select("banco[2]",$fuente, $fuente, $b[2],"class='form-control' id='bnc2'")."</td>
					<td>".frm_text("referencia[2]",$r[2],6, 6,"class='form-control'  id='ref2'")."</td>
					<td>".frm_text("monto[2]",$m[2], 12, 14, $numeroReal."id='m2' style='text-align:right' $blanquear $sumar class='form-control'")."</td>
					</tr>
					<tr><td>".frm_select("forma_pago[3]",$formaPago,$formaPago,$f[3],"class='form-control' id='fp3' onchange='cambiaFp(3);'")."</td>
					<td>".frm_select("banco[3]",$fuente, $fuente, $b[3],"class='form-control' id='bnc3'")."</td>
					<td>".frm_text("referencia[3]",$r[3], 6, 6,"class='form-control' id='ref3'")."</td>
					<td>".frm_text("monto[3]",$m[3], 12, 14, $numeroReal." id='m3' style='text-align:right' $blanquear $sumar class='form-control'")."</td>
					</tr>															
					<tr>
						<td colspan='3' align='right'><p class='form-control'>TOTAL</p></td>
						<td align='right'>".
							frm_text("total",$total, 12,14, "id='total' style='text-align:right' readonly class='form-control' " )   
						."</td>
					</tr>
				</table>
			</div>";
		$div = "<div class='row'><form id='frm' >";
		$div .= frm_hidden("id_comprobantes",$id_comprobantes);
		$div .= $tabla1;
		$div .= $tabla2;
		$div .= "</form></div>";
	   	$html = "<center>".$div."</center>";  
		return $html;	
	}
//-----------------------------------------------------------------------------------------------------------
	function checkAllData($f,$new = 0){		// Considerar colocar los campos obligatorios en el formulario.
		if(empty($f['numero'])) return "El campo 'Número' no puede ser nulo.";
		if(empty($f['factura'])) return "El campo 'Factura' no puede ser nulo.";
		if(empty($f['fecha_factura'])) return "El campo 'fecha_factura' no puede ser nulo.";
		if(empty($f['beneficiario'])) return "El campo 'beneficiario' no puede ser nulo.";
		if(empty($f['cancela'])) return "El campo 'cancela' no puede ser nulo.";
		if(empty($f['fecha_cancela'])) return "El campo 'fecha_cancela' no puede ser nulo.";
	 	return 0;
	}
//-----------------------------------------------------------------------------------------------------------
	function blanquearCampos(){		//SI ES PARA EL FORMULARIO, SE PUEDE SUSTITUIR POR reset.
		$xr = new xajaxResponse();
		$xr->assign("id_comprobantes","value","");
		$xr->assign("numero","value","");
		$xr->assign("factura","value","");
		$xr->assign("fecha_factura","value","");
		$xr->assign("beneficiario","value","");
		$xr->assign("cancela","value","");
		$xr->assign("fecha_cancela","value","");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
// Nombres de los campos de la consulta.
	function camposBD(){
		$fields = array();
		$fields[] = 'id_comprobantes';	
		$fields[] = 'n_comprobante';	
		$fields[] = 'factura';	
		$fields[] = 'fecha_factura';	
		$fields[] = 'beneficiario';	
		$fields[] = 'fecha_cancela';	
		return $fields;
	}
//-----------------------------------------------------------------------------------------------------------
	function encabezados(){
		$headers = array();
		$headers[] = "No. Comprobante";
		$headers[] = "Factura";
		$headers[] = "Fecha Factura";
		$headers[] = "Beneficiario";
		$headers[] = "Fecha Pago";
		return $headers;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosEncabezados(){
		// HTML table: hearders attributes
		$attribsHeader = array();
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
		$attribsCols[] = 'nowrap style="text-align:center"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		return $attribsCols;
	}
//-----------------------------------------------------------------------------------------------------------
	function reporteComprobantes(){
		$accion = " onclick = \"xajax_imprimir_comp(xajax.getFormValues('frm'))\";";
		$sql = "SELECT n_comprobante, factura, fecha_cancela, beneficiario FROM comprobantes order by n_comprobante desc";
		$bd = new CLS_PARROT();
		$registros = $bd->consultaGenerica($sql);
		$encabezado = "<tr>
				<th class='alerta'>Comprobante</th>
				<th class='alerta'>Factura</th>
				<th class='alerta'>Cancelado</th>
				<th class='alerta'>Beneficiario</th>
				<th class='alerta'>Imprimir</th>
			</tr>";
		$tabla = "<br/><center><h3>SELECCIONE LOS COMPROBANTES QUE DESEA IMPRIMIR</h3></center>
			<form name = 'frm' id='frm' >
			<table align='center' data-order='[[ 0, \"desc\" ]]' class='table' id='dataGrid'>
			<thead>$encabezado</thead><tfoot>$encabezado</tfoot>";
		foreach($registros as $fila){
			extract($fila);
			$tabla .= "<tr>
				<td align='right'>$n_comprobante</td>
				<td align='right'>$factura</td>
				<td align='center'>$fecha_cancela</td>
				<td>$beneficiario</td>
				<td align='center'>".frm_imagen("./imagenes/pdf.jpg","imprimir",20,20, "style='cursor: pointer' onclick=xajax_repor_comprobante($n_comprobante)")."</td>
			</tr>";
		}

		$html = "<div class='row'><div class='col-md-offset-2 col-md-8 cajita'>";
		$html .= $tabla;
		$html .= "</div></div>";
		$xr = new xajaxResponse();
		$xr->assign("contenedor","innerHTML",$html);
		$xr->script("dataGrid2('dataGrid');");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------	
	function imprimir_comp($formulario){
		extract($formulario);
		$xr = new xajaxResponse;
		foreach($comp as $comprobante){
			//Imprimir comprobante, segun el valor optenido.
			$xr->script("aviso('Se imprime comprobante $comprobante')");
			$xr->script("xajax_repor_comprobante($comprobante);");
		}
		return $xr;	
	}
}

?>
