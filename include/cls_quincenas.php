<?php
include_once("cls_parrot.php");
include_once("fpdf.php");
include_once("funciones_fecha.php");
include_once("form_items.php");

	class CLS_QUINCENAS extends CLS_PARROT{
	var $sqlBase;
	var $nombres;
	var $cedulas;
	var $titulo;
	var $ordenTabla ="data-order='[[ 1, \"desc\" ],[ 1, \"asc\" ]]'";
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
			$this->sqlBase = "SELECT * FROM 
(SELECT id_quincenas, CONCAT(apellidos,', ',nombres) AS empleado, fecha, desde, hasta 
FROM `quincenas` INNER JOIN empleados ON empleados.cedula = quincenas.cedula) as z";
			$this->titulo = "HISTORICO DE QUINCENAS";
			$sql = "SELECT CONCAT( apellidos, ', ', nombres ) AS nombre, cedula FROM empleados WHERE condicion = 1 ORDER BY apellidos";
			$empleados = $this->consultagenerica($sql);
			$nombres[]="Seleccione empleado";
			$cedulas[]=0;
			foreach($empleados as $empleado){
				extract($empleado);
				$nombres[]=$nombre;
				$cedulas[]=$cedula;		
			}
			$this->nombres = $nombres;
			$this->cedulas = $cedulas;
	}
//-----------------------------------------------------------------------------------------------------------
	function insertNewRecord($formulario){
		extract($formulario);
		$empleados = $this->empleadosRecords("cedula = $cedula");
		foreach($empleados as $registro){
			extract($registro);
		}
		$n_comprobante = $this->nuevo_id("comprobantes","n_comprobante");
		$_SESSION["comprobante"] = $n_comprobante;
		$n_recibo = $this->nuevo_id("quincenas","n_recibo");
	//  Graba en quincenas
		$id_quincena = $this->nuevo_id("quincenas","id_quincenas");
		$_SESSION["id_quincena"] = $id_quincena;
		$res = $this->quincenasInsert($cedula, $fecha, $desde, $hasta, $n_recibo, $n_comprobante);
		$n = count($dias);
	//  Graba en nomina
		for($i = 0; $i < $n; $i++){
			if($dias[$i] > 0){
				$res = $this->nominaInsert($id_quincena, $id_asig_dedu[$i], $monto[$i], $dias[$i]);
			}
		}
	//Graba en Comprobantes
		$indice = array_search($cedula, $this->cedulas);
		$res = $this->comprobantesInsert($n_comprobante, $n_recibo, $fecha, $this->nombres[$indice], "JOSE DELGADO", $fecha );		
	//Graba en pagos
		$id_comp = $this->max_id("comprobantes","id_comprobantes");
		for($i=0;$i<2;$i++){
			if($m[$i]>0){
				$res = $this->pagosInsert($id_comp,$f[$i], $b[$i], $r[$i], $m[$i]);
	//	Graba en bancos.
				if(strtoupper($b[$i]) != "CAJA"){
					$idBanco = (strtoupper($b[$i]) == "BANESCO") ? 1 : 2;
					if(strtoupper($f[$i]) == "CHEQUE"){
						$formaPago = 5;
					}elseif(strtoupper($f[$i]) == "DEBITO"){
						$formaPago = 4;
					}else{
						$formaPago = 6;
					}
					$lmonto = -1 * $m[$i];
					$lfecha = d_ES_MYSQL($fecha);
					$res = $this->tbl_mov_bancariosInsert($idBanco, $formaPago, $n_comprobante, $lfecha, $r[$i],$this->nombres[$indice], $lmonto );	
				}			
			}
		}
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function updateRecord($formulario){
		extract($formulario);
	//Obtener el valor del comprobante.
	//Obtener el valor del recibo.
		$n_comprobante = $_SESSION["n_comprobante"];
		$n_recibo = $_SESSION["n_recibo"];
		$id_quincenas = $_SESSION["id_quincena"];
		$indice = array_search($cedula, $this->cedulas);
	// Actualiza en la tabla quincenas
		$res = $this->quincenasUpdate($cedula, $fecha, $desde, $hasta, $n_recibo, $n_comprobante,$id_quincenas);
	// Borrar todos los registros de nomina del id a actualizar.
		$res = $this->nominaDelete("id_quincena = $id_quincenas");
	//Espera un segundo antes de grabar el(los) nuevo(s) registro(s).
	//  Graba en nomina
		$n = count($monto);
		for($i=0; $i<$n; $i++){
			if($dias[$i]<>0){
				//$dias[$i] = settype( $dias[$i],"integer") ;
				if($dias[$i]>0){
					$res = $this->nominaInsert($id_quincenas, $id_asig_dedu[$i], $monto[$i], $dias[$i]);
				}
			}
		}
		//Obtener el id del comprobante.
		$a_comprobantes = $this->comprobantesRecords("n_comprobante = $n_comprobante"); 
		foreach($a_comprobantes as $fila){
			extract($fila);
		}
		// Edita registro de comprobantes // Por proteccion del registro solo se podran alterar fechas.
		$fecha_cancela = ($fecha);
   	  	$fecha_factura = ($fecha);		
		$res = $this->comprobantesUpdate(
			$n_comprobante, 
			$factura, 
			$fecha_factura, 
			$beneficiario, 
			$cancela, 
			$fecha_cancela, 
			$id_comprobantes);
		// Se Borran los registros de pagos con id_comprobantes = $id_comprobantes
		$res = $this->pagosDelete("id_comprobantes = $id_comprobantes");
		// Se borran los registros de movimientos bancarios con n_comprobante = $n_comprobante 
		$res = $this->tbl_tipo_movimientosDelete("n_comprobante = $n_comprobante ");
		// Se registran los datos en la tabla pagos.
		for($i=0;$i<2;$i++){
			if($m[$i]>0){
				$res = $this->pagosInsert($id_comprobantes,$f[$i], $b[$i], $r[$i], $m[$i]);
		//	Graba en bancos.
				if(strtoupper($b[$i]) != "CAJA"){
					$idBanco = (strtoupper($b[$i]) == "BANESCO") ? 1 : 2;
					if(strtoupper($f[$i]) == "CHEQUE"){
						$formaPago = 5;
					}elseif(strtoupper($f[$i]) == "DEBITO"){
						$formaPago = 4;
					}else{
						$formaPago = 6;
					}	
					$lmonto = -1*$m[$i];
					$lfecha = d_ES_MYSQL($fecha_cancela);
					$res = $this->tbl_mov_bancariosInsert($idBanco, $formaPago, $n_comprobante, $lfecha, $r[$i],$this->nombres[$indice], $lmonto );	
				}
			}
		}
		if($imprimir=="imprimir"){
			//Imprimir recibo de pago.
			$_COOKIE["id_quincenas"] = $id_quincenas;
			//Imprimir comprobante de pago.
			$_COOKIE["id"] = $n_comprobante;
			//Imprimir nómina quincenal.
			$_COOKIE["fecha"] = $fecha_cancela;
		}
		return $res;
	}
//----------------------------------------------------------------------------------------------
	function deleteRecord($id){
		$SQL1 = "SELECT n_comprobante FROM quincenas WHERE id_quincenas = $id";
		$record = $this->consultagenerica($SQL1);
		$n_comprobante = $record[0]["n_comprobante"];
		$SQL2 = "SELECT id_comprobantes FROM comprobantes WHERE n_comprobante = $n_comprobante";
		$record = $this->consultagenerica($SQL2);
		$id_comprobantes = $record[0]["id_comprobantes"];
//	Se borra el/los registro(s) de la tabla nomina		
		$res = $this->nominaDelete("id_quincenas = $id");
//	Se borra el registro de la tabla quincena		
		$res = $this->quincenasDelete("id_quincenas = $id");
//	Se borran el/los registro(s) de la tabla pagos.
		$res = $this->pagosDelete("id_comprobantes = $id_comprobantes");
//	Se borra el registro de la tabla comprobantes.
		$res = $this->comprobantesDelete("n_comprobante = $n_comprobante");
//	Se borra el registro de la tabla tbl_mov_bancarios
		$res = $this->tbl_mov_bancariosDelete("n_comprobante = $n_comprobante");				
		return $res;
	}
//-----------------------------------------------------------------------------------------------------------
	function formAdd(){
		$html = $this->frmquincenas();
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function formEdit($id){
		$html = $this->frmquincenas($id);
		return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function periodo($id=0){
		//Dos opciones: (1)Inicial. Sin datos. (2) De edicion.
		$alineacionDerecha = " style='text-align:right' ";
		$numeroReal = " onkeypress='return NumCheck(event, this);'";
		$formaPago = array("Cheque","Debito","Efectivo","Transferencia");
		$fuente = array("Banesco","Caja","Mercantil");
		$blanquear = "onfocus=\"if(this.value == '0') { this.value = ''; };\"";
		if($id==0){
			$fecha = date('d/m/Y');
			$desde = date('d/m/Y')<date('16/m/Y') ? date('01/m/Y'):date('16/m/Y');
			$hasta =  date('d/m/Y')<date('16/m/Y') ? date('15/m/Y'):d_US_ES(dFinMes(date('m/16/Y')));
			$_SESSION["desde"] = $desde;
			for($i=0;$i<4;$i++){
				$f[$i]	=	$formaPago[3];
				$b[$i]	=	"";
				$r[$i]	=	"";
				$m[$i]	= 	"";
			}
		}else{
			$aQuincena = $this->quincenasRecords("id_quincenas = $id");
			foreach($aQuincena as $fila){
				extract($fila);
				$fecha = dMySQL_ES($fecha);
				$desde = dMySQL_ES($desde);
				$hasta = dMySQL_ES($hasta);
				$_SESSION["n_comprobante"] = $n_comprobante;
				$_SESSION["n_recibo"] = $n_recibo;
				$_SESSION["id_quincena"] = $id;
			}
			$sql = "SELECT forma_pago, banco, referencia, monto_pago FROM pagos INNER JOIN comprobantes 
				ON comprobantes.id_comprobantes = pagos.id_comprobantes WHERE n_comprobante = $n_comprobante";
			$aPagos = $this->consultaGenerica($sql);
			$nPagos = count($aPagos);
			$i = 0;
			for($j = 0;$j<2; $j++){
				$f[$j]	=	$formaPago[3];
				$b[$j]	=	"";
				$r[$j]	=	"";
				$m[$j]	= 	"";
			}
			foreach($aPagos as $fPagos){
				extract($fPagos);
				$f[$i] = $forma_pago; 
				$b[$i] = $banco;
				$r[$i] = $referencia;
				$m[$i] = $monto_pago;
				$i++;
			}
		}
		$htm = "<table class='table'>
				<tr>
					<td><b>Per&iacute;odo:</b></td>
					<td align='center'><b>Desde</b></td>
					<td>".frm_calendario2('desde','desde' ,$desde, "id='desde' required")."</td>
					<td align='center'><b>Hasta</b></td>
					<td>".frm_calendario2('hasta','hasta' ,$hasta, "id='hasta' required")."</td>
				</tr>
				<tr>
					<td colspan='2'><b>Fecha de pago</b></td>
					<td colspan='2'>".frm_calendario2('fecha','fecha' ,$fecha, "id='fecha' required")."</td>
				</tr>
				<tr>
					<td colspan='5'>
						<table align='center' border='1' class='table'>
							<tr><td><b>Forma de pago</b></td><td><b>Fuente</b></td><td><b>Referencia</b></td><td><b>Monto</b></b></td></tr>
							<tr><td align='center'>".frm_select("f[0]",$formaPago,$formaPago,$f[0], "style='width:110px'")."</td>
							<td>".frm_select("b[0]",$fuente, $fuente, $b[0])."</td>
							<td>".frm_text("r[0]",$r[0], 6, 6)."</td>
								<td>".frm_text("m[0]",$m[0], 6, 10, "id='idMonto0' style='text-align:right' $blanquear $alineacionDerecha")."</td>
							</tr>
							<tr><td align='center'>".frm_select("f[1]", $formaPago, $formaPago,$f[1], "style='width:110px'")."</td>
							<td>".frm_select("b[1]",$fuente, $fuente, $b[1])."</td>
							<td>".frm_text("r[1]",$r[1], 6, 6)."</td>
								<td>".frm_text("m[1]",$m[1], 6, 10, "id='idMonto1' style='text-align:right'  $blanquear $alineacionDerecha ")."</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>";
			return $htm;
	}
//-----------------------------------------------------------------------------------------------------------
	function detalles($id = 0, $cedula = 0){
		$sw = TRUE;
		//Tres opciones. 1. Sin datos de empleado; 2. Con datos de empleado y nuevo registro y 3. Edicion de registro.
		$numeroReal = " onkeypress='return NumCheck(event, this);'";
		$registros = $this->asig_deduRecords("","","",0,20);
		$nRegAsigDedu = $this->numRegistros("asig_dedu");
		for($i=0;$i<$nRegAsigDedu;$i++){
			$subtotal_A[$i]="";
			$subtotal_D[$i]="";
		}
		$total_A = "";
		$total_D = "";
		$total = "";
		$calcular = " onBlur=\"xajax_calcular(xajax.getFormValues('frm'))\"";
		if($id == 0 AND $cedula == 0){
			for($i=0;$i<$nRegAsigDedu;$i++){
				$Aid_asig_dedu[$i] = "";	
				$item[$i] = $registros[$i]["descripcion"];
				$diario[$i] = "";
				$dias[$i] = "";
				$tipo[] = "";
			}
		}
		if($id > 0 AND $cedula > 0){
			$sql = "SELECT id_asig_dedu, tipo, descripcion FROM asig_dedu";
			$resultados = $this->consultaGenerica($sql);
			foreach($resultados as $fila){
				$Aid_asig_dedu[] = $fila["id_asig_dedu"];
				$tipo[] = $fila["tipo"];
				$item[] = $fila["descripcion"];
				$diario[] = "";
				$dias[] = "";
			}
			$sql = "SELECT asig_dedu.id_asig_dedu, dias, monto FROM asig_dedu INNER JOIN nomina 
						ON asig_dedu.id_asig_dedu = nomina.id_asig_dedu WHERE id_quincena = $id";
			$resultados = $this->consultagenerica($sql);
			$n = count($tipo);
			$total_A = 0;
			$total_D = 0;
			foreach($resultados as $fila){
				$indice = array_search($fila["id_asig_dedu"], $Aid_asig_dedu);
				$diario[$indice] = $fila["monto"];
				$dias[$indice] = $fila["dias"];
				if(substr($tipo[$indice],0,1)=="A"){
					$subtotal_A[$indice] = $diario[$indice]*$dias[$indice];
					$total_A +=  $subtotal_A[$indice];
				}else{
					$subtotal_D[$indice] = $diario[$indice]*$dias[$indice];
					$total_D += $subtotal_D[$indice];
				}
			}
			$total = $total_A - $total_D;
		}
		if($cedula > 0 AND $id==0){
			$sql = "SELECT unidad_tributaria FROM unidad_tributaria order by fecha desc LIMIT 0,1";
			$uts = $this->consultagenerica($sql);
			$UT = $uts[0]["unidad_tributaria"];
			$sql = "SELECT sueldo_mensual FROM sueldos WHERE cedula = $cedula ORDER BY fecha DESC LIMIT 0,1";
			$sueldos = $this->consultaGenerica($sql);
			$S = $sueldos[0]['sueldo_mensual'];	
			$registros = $this->asig_deduRecords("","","",0,20);
			$sql = "SELECT id_asig_dedu, tipo, descripcion FROM asig_dedu";
			$resultados = $this->consultaGenerica($sql);
			$i = 0;
			foreach($registros as $registro){
				$Aid_asig_dedu[$i] = $registro["id_asig_dedu"];
				$item[$i] = $registro["descripcion"];
				$f = $registro['formula'];
				$x = "\$diario[$i] = $f";
				$dias[$i] = "";
				eval($x);
				if($registro["descripcion"] === "Salario B&aacute;sico"){
					$dias[$i] = "15";
				}
				if(idia($_SESSION["desde"])>15){
					switch($registro["descripcion"]){
						case "F.A.O.V.":
							$dias[$i] = "1";
							break;
						case "R.P.E.":
						 	$dias[$i] = lunesXmes(d_ES_US($_SESSION["desde"]));
							break;
						case "I.V.S.S.":
							$dias[$i] = lunesXmes(d_ES_US($_SESSION["desde"]));
							break;
					}
				}
				$tipo[$i] = substr($registro['tipo'],0,1);
				$i++;
			}
		}
		$htm = "<table class='table'>
			<tr>
				<td align='center'><b>Descripci&oacute;n</b></td>
				<td align='center'><b>Monto</b><br/><b>Diario</b></td>
				<td align='center'><b>Dias</b></td>
				<td align='center'><b>Asignaci&oacute;n</b></td>
				<td align='center'><b>Deducci&oacute;n</b></td>
			</tr>";
		for($i=0;$i<$nRegAsigDedu;$i++){
			$diario[$i] = round($diario[$i], 4);// SE REDONDEA A DOS (4) DECIMALES.)
			$htm .= "<tr><td><b>".frm_hidden("tipo[$i]",$tipo[$i]).frm_hidden("id_asig_dedu[$i]",$Aid_asig_dedu[$i]).$item[$i]."</b></td>
				<td>".frm_text("monto[$i]",$diario[$i],6,6,"style='text-align:right'")."</td>
				<td align='center'>".frm_text("dias[$i]",$dias[$i],4,2,"style='text-align:right' $calcular")."</td>
				<td align='right' id='asignacion$i'>".($subtotal_A[$i])."</td>
				<td align='right' id='deduccion$i'>".($subtotal_D[$i])."</td> 
				</tr>";
		}	
			$htm .= "<tr>
				<td colspan='3'><b>Total Asignaciones y Deducciones</b></td>
				<td align='right' id='totalAsignaciones'>".($total_A)."</td>	<td align='right' id='totalDeducciones'>".($total_D)."</td>
			</tr>
				<tr>
				<td colspan='4' align='right'><b>Total a Cancelar</b></td>
				<td align='right' id='total'>".($total)."</td>
			</tr>
			<tr>
			</table>";
			return $htm;
	}
//-----------------------------------------------------------------------------------------------
/*	function producto($a, $b){
		return $a*$b;
	} */
//-----------------------------------------------------------------------------------------------
	function calcular($f){
		extract($f);
		$n = count($dias);
		$totalAsignaciones = 0;
		$totalDeducciones = 0;
		$xr = new xajaxResponse();
		for($i=0; $i<$n; $i++){
			if(substr($tipo[$i],0,1)=="A"){
				$asignacion = $monto[$i] * $dias[$i];
				$totalAsignaciones += $asignacion; 		
				$xr->assign("asignacion$i", "innerHTML", round($asignacion,2));	
			}else{
				$deduccion = $monto[$i] * $dias[$i];
				$totalDeducciones += $deduccion;
				$xr->assign("deduccion$i", "innerHTML", round($deduccion,2));
			}
			$xr->assign("totalAsignaciones","innerHTML",round($totalAsignaciones,2));
			$xr->assign("totalDeducciones","innerHTML", round($totalDeducciones,2));
			$xr->assign("total","innerHTML", round($totalAsignaciones - $totalDeducciones,2));
			$xr->assign("idMonto0","value", round($totalAsignaciones - $totalDeducciones,2));
			$xr->assign("idMonto1","value", "0.00");
		}
		return $xr;
	}
//-----------------------------------------------------------------------------------------------
	function frmquincenas(){
		$alineacionDerecha = " style='text-align:right' ";
		$numeroReal = " onkeypress='return NumCheck(event, this);'";
		$los_detalles = "onchange = \"xajax_cambiarDetalles(this.value)\"";
		if(func_num_args() > 0){
			$id_quincenas = func_get_arg(0);
			$cedulas = $this->consultagenerica("select cedula FROM quincenas WHERE id_quincenas = $id_quincenas");
			$cedula = $cedulas[0]["cedula"];
			$htmX = $this->periodo($id_quincenas);
			$htmY = $this->detalles($id_quincenas, $cedula);
			$textoBoton = "Actualizar";
			$accion = "onClick=\"xajax_update('CLS_QUINCENAS',xajax.getFormValues('frm'))\"";
		}else{
			// Nuevo registro.
			$textoBoton = "Grabar";
			$accion = "onClick=\"xajax_save('CLS_QUINCENAS',xajax.getFormValues('frm'))\"";
			$id_quincenas = 0;
			$cedula = 0;
			$htmX = $this->periodo();
			$htmY = $this->detalles();
		}
		$htm = "<div class='row'><div class='col-md-12' style='font-size:12px'>";
		$htm .= "<H3><p class='text-center'>Nueva quincena</p></H3>";
		$htm .= "</div></div>";
		
		$htm .= "<div class='row'><div class='col-md-12' style='font-size:12px'>";
		$htm .= "<form id='frm' >".frm_hidden("id_quincenas",$id_quincenas);
		$htm .= "<table class='table'>
			<tr>
				<td align='right'><b>Nombre del Empleado</b></td>
				<td>".frm_select("cedula", $this->nombres, $this->cedulas,$cedula, $los_detalles)."</td>
			</tr>
			<tr>
				<td id='zonaX'>
					$htmX
				</td>
				<td id='zonaY'>
					$htmY
				</td>
			</tr>
		</table>";				
		$htm .= "</form></div></div>";
		return $htm;	
	}
//-----------------------------------------------------------------------------------------------------------
	function cambiarDetalles($cedula){
		$clase = new CLS_QUINCENAS;
		$salida = $clase->detalles(0, $cedula);
		$xr = new xajaxResponse();
		$xr->assign("zonaY","innerHTML",$salida);
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
	
//-----------------------------------------------------------------------------------------------------------
	function checkAllData($f,$new = 0){		// Considerar colocar los campos obligatorios en el formulario.
		//if(empty($f['id_quincenas'])) return "El campo 'id_quincenas' no puede ser nulo.";
		if(empty($f['cedula'])) return "El campo 'cedula' no puede ser nulo.";
		if(empty($f['fecha'])) return "El campo 'fecha' no puede ser nulo.";
		if(empty($f['desde'])) return "El campo 'desde' no puede ser nulo.";
		if(empty($f['hasta'])) return "El campo 'hasta' no puede ser nulo.";
		extract($f);
		if(empty($r[0])) return "El campo 'Referencia' no puede ser nulo.";
	 	if(empty($m[0]) OR $m[0] < 0) return "El campo 'monto' no puede ser nulo ni negativo.";
	 	if(empty($dias[0])) return "El campo 'dia' no puede ser nulo.";
	 	return 0;
	}
//-----------------------------------------------------------------------------------------------------------
	function blanquearCampos(){		//SI ES PARA EL FORMULARIO, SE PUEDE SUSTITUIR POR reset.
		$xr = new xajaxResponse();
		//$xr->assign("id_quincenas","value","");
		$xr->assign("cedula","value","");
		$xr->assign("fecha","value","");
		$xr->assign("desde","value","");
		$xr->assign("hasta","value","");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
// Nombres de los campos de la consulta.
	function camposBD(){
		$fields = array();
		$fields[] = 'id_quincenas';	
		$fields[] = 'empleado';	
		$fields[] = 'fecha';	
		$fields[] = 'desde';	
		$fields[] = 'hasta';	
		return $fields;
	}
//-----------------------------------------------------------------------------------------------------------
	function encabezados(){
		$headers = array();
		//$headers[] = "id_quincenas";
		$headers[] = "Empleado";
		$headers[] = "Fecha de pago";
		$headers[] = "Desde";
		$headers[] = "Hasta";
		return $headers;
	}
//-----------------------------------------------------------------------------------------------------------
	function atributosEncabezados(){
		// HTML table: hearders attributes
		$attribsHeader = array();
		//$attribsHeader[] = '20';
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
		//$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:left"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		$attribsCols[] = 'nowrap style="text-align:center"';
		return $attribsCols;
	}
//-----------------------------------------------------------------------------------------------------------
	function ascDesEncabezado($CLASE, $limit, $filter, $content, $divName){
		// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
		$eventHeader = array();
		//$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","id_quincenas","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","empleado","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","fecha","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","desde","'.$divName.'","ORDERING");return false;\'';
		$eventHeader[]= 'onClick=\'xajax_showGrid("'.$CLASE.'", 0,'.$limit.',"'.$filter.'","'.$content.'","hasta","'.$divName.'","ORDERING");return false;\'';
		return $eventHeader;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscar(){
		// Select Box: fields table.
		$fieldsFromSearch = array();
		//$fieldsFromSearch[] = "id_quincenas";
		$fieldsFromSearch[] = "empleado";
		$fieldsFromSearch[] = "fecha";
		//$fieldsFromSearch[] = "desde";
		//$fieldsFromSearch[] = "hasta";
		return $fieldsFromSearch;
	}
//-----------------------------------------------------------------------------------------------------------
	function camposBuscarMostrar(){
		$fieldsFromSearchShowAs = array();
		//$fieldsFromSearchShowAs[] = "ID_QUINCENAS";
		$fieldsFromSearchShowAs[] = "EMPLEADO";
		$fieldsFromSearchShowAs[] = "FECHA DE PAGO";
		//$fieldsFromSearchShowAs[] = "DESDE";
		//$fieldsFromSearchShowAs[] = "HASTA";
		return	$fieldsFromSearchShowAs;
	}
//-----------------------------------------------------------------------------------------------------------
	function frmReporteQuincena(){
		$annio = date('Y');
		$mesActual = date("m");
		$per_actual = (date("j")<16) ? "1ra.":"2da.";
		$annios = array($annio, $annio-1);
		$periodo = array("1ra.", "2da.");
		$imeses = array(1,2,3,4,5,6,7,8,9,10,11,12);
		$accion = "onchange = 'xajax_imprimirRecibo(xajax.getFormValues(\"frm\"))'";
		$meses = array('enero','febrero','marzo','abril',
                      'mayo','junio','julio','agosto',
                      'septiembre','octubre','noviembre','diciembre');
        $html  = "<br/><div class='row'><div class='col-md-8 col-md-offset-2 cajita' style='margin=20px;'>";             
        $html .= "<center><h2>Recibos de Quincena</h2></center>";              
        $html .= "<form id='frm'><center><h2>Seleccione Per&iacute;odo, Mes y A&ntilde;o.</h2><br/><strong>Quincena:</strong> ".
        	frm_select("periodo",$periodo,array(1,2), $per_actual,"style='width:80px'"."$accion")."   <strong> Mes:</strong> ".
        	frm_select("mes", $meses, $imeses,$mesActual, $accion). "   <strong>A&ntilde;o:</strong> ".
        	frm_select("annio",$annios, $annios,$annio,$accion).
        	"</form><br/><br/><div id='empleados' style='width:100%'></div>";
       	$html .= "<br/><div id='frmquincenas' style='width:100%'>"; 
       	$frm = array("periodo"=>$per_actual,"mes"=>$mesActual, "annio"=>$annio);
       	$html .= $this->imprimirRecibo($frm,TRUE);
       	$html .= "</div></center>";
        return $html;

/*        $xr = new xajaxResponse();
        $xr->assign("contenedor","innerHTML", $html);
        $xr->waitFor("xajax_imprimirRecibo(xajax.getFormValues('frm'))", 10);
        return $xr;*/
	}
//-----------------------------------------------------------------------------------------------------------
	function imprimirRecibo($formulario, $ir=FALSE){
		extract($formulario);
		//$periodo = 2; $mes = 6; $annio = 2015;
		$sql = "select id_quincenas, concat(apellidos, ', ', nombres) as nombre, empleados.cedula, total from
			empleados inner join
			(select cedula,id_quincenas, desde,if(day(desde)=1,1,2) as quincena, sum(dias * monto) as total
			from quincenas inner join nomina on quincenas.id_quincenas = nomina.id_quincena
			group by cedula, fecha) as x
			on x.cedula = empleados.cedula
			where x.quincena = $periodo and month(x.desde)= $mes and year(x.desde)= $annio 
			order by nombre";
		$bd = new CLS_PARROT;	
		$registros = $bd->consultagenerica($sql);
		$xr = new xajaxResponse();
		if($bd->filas == 0){
			$mensaje = "<h2>No se tienen registros para este per&iacute;odo.</h2>";
			if($ir){
				return $mensaje;	
			}else{
				$xr->assign("frmquincenas", "innerHTML", $mensaje);	
				return $xr;	
			}
			
		}else{
			$htm = "<table class='table .table -striped'>
				<tr><th class='text-center alerta'>NOMBRE</th><th class='text-center alerta'>CEDULA</th><th class='text-center alerta'>TOTAL</th><th class='text-center alerta'>IMPRIMIR</th></tr>";
			foreach($registros as $registro){
				extract($registro);
				$htm .= "<tr><td width='250px'>$nombre</td><td align='right' width='100px'>".numeroEspanol($cedula, 0)."</td><td align='right' width = '100px'>".numeroEspanol($total)."</td><td align='center'>".frm_imagen("./imagenes/pdf.jpg","imprimir",20,20, "style='cursor: pointer' onclick=xajax_imp_recibo($id_quincenas)")."</td></tr>";
			}
		}	
		$htm .= "</table>"; 
		//return $htm;
		if($ir){
			return $htm;
		}else{
			$xr->assign("frmquincenas", "innerHTML", $htm);
			return $xr;			
		}

	}
//-----------------------------------------------------------------------------------------------------------
	function imp_recibo($id_quincenas){
		setcookie("id_quincenas", $id_quincenas, time()+6000);		//Crea cookie con duración de diez (10) minutos.
		$reporte = "./include/pdf_recibos.php";
		$xr = new xajaxResponse();
		//$n = 0;
		$xr->script("var newWin = window.open('$reporte', '_blank', 'fullscreen=yes')");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
	function frmNomina(){
		$sql = "SELECT DISTINCT fecha FROM quincenas ORDER BY fecha DESC";
		$bd = new CLS_PARROT;
		$registros = $bd->consultagenerica($sql);
		$html = "<div class='row'><div class='col-md-offset-2 col-md-8 cajita' >";
		$html .= "<h2><center>Selecciona la QUINCENA que desea imprimir</h2></center>";
		$html .= "<br/><br/>";
		$html .= "<table align='center' id='dataGrid' data-order='[[ 0, \"desc\" ]]'>";
		$html .= "<thead><tr><th width='100px' class='text-center alerta'>FECHA</th><th width='100px' class='text-center alerta'>IMPRIMIR</th></tr></thead>";
		$html .= "<tfoot><tr><th width='100px' class='text-center alerta'>FECHA</th><th width='100px' class='text-center alerta'>IMPRIMIR</th></tr></tfoot>";
		foreach($registros as $registro){
			extract($registro);
			$html .= "<tr><td align='center'>$fecha</td><td align='center'>".frm_imagen("./imagenes/pdf.jpg","imprimir",20,20, "style='cursor: pointer' onclick='xajax_imp_nomina(\"$fecha\")'")."</td></tr>";		
		}
		$html .= "</table>";  
		$html .= "</div></div>"; 
		$xr = new xajaxResponse;
		$xr->assign("contenedor", "innerHTML", ($html));
		$xr->script("dataGrid2('dataGrid')");
		return $xr;
		//return $html;
	}
//-----------------------------------------------------------------------------------------------------------
	function imp_nomina($fecha){
		setcookie("fecha", $fecha, time()+6000);		//Crea cookie con duración de diez (10) minutos.
		$reporte = "./include/pdf_nomina.php";
		$xr = new xajaxResponse();
		//$n = 0;
		$xr->script("var newWin = window.open('$reporte', '_blank', 'fullscreen=yes')");
		return $xr;
	}
//-----------------------------------------------------------------------------------------------------------
}
/*	$ej = new CLS_QUINCENAS;
	echo $ej->frmquincenas();*/

?>
