<?php
	include_once("cls_parrot.php");
	include_once("form_items.php");
	include_once("funciones_fecha.php");
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
		$html .= "<br/>";
		$html .= "<b>Seleccione el Banco:</b> $cmbBancos<br/><br/>";
		$html .= "<b>Seleccione el tipo de movimiento: </b> $cmbTipMov<br/>";
		$html .= "<h3>Indique el periodo a mostrar.</h3></center><br/>";
		$html .= "<table align='center' class='table'>";
		$html .= "<tr>
				<td>".frm_radio("opcion","1","1")."Dia</td>
				<td colspan='4'>$calendario</td>
			</tr>
			<tr>
				<td>".frm_radio("opcion","2","")."Mes/a&ntilde;o</td>
				<td colspan='4'>$medioCalendario</td>
			</tr>
			<tr>
				<td>".frm_radio("opcion","3","")."Por rango:</td>
				<td>Desde:</td>
				<td>$calendario1</td>
				<td>Hasta</td>
				<td>$calendario2</td>
			</tr>";
		$html .= "</table><br/><br/><center>".frm_button("reporte", "Mostrar", $ejecutar )."</center>
		<br/><br/><br/></div></div>";  
		$htm = "<form id='frm' >".$html."</form>";
		return $htm;
	}
	
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
				setcookie("dia",   $dia,   time()+6000);
				break;
			case 2:
				$dia_desde = "$anno/$mes/01";
				$finMes = dFinMes("$mes/01/$anno");
				$dia_hasta = d_US_MySQL($finMes);
				$dias = array("dia_desde"=>$dia_desde, "dia_hasta"=>$dia_hasta);
				setcookie("dia_desde",   $dia_desde,   time()+6000);
				setcookie("dia_hasta",    $dia_hasta,   time()+6000);
				break;
			case 3:
				$dia_desde = d_ES_MYSQL($fecha1);
				$dia_hasta = d_ES_MYSQL($fecha2);
				$dias = array("dia_desde"=>$dia_desde, "dia_hasta"=>$dia_hasta);
				setcookie("dia_desde", $dia_desde,   time()+6000);
				setcookie("dia_hasta", $dia_hasta,   time()+6000);
				break;
		}
//------------------------------------------------------
		$idbanco = $_COOKIE["idbanco"];
		$nDias   = $_COOKIE["nDias"];
		$reporte = new pdf_bancos($idbanco, $nDias);
		$reporte->AddPage();
		$reporte->body();
		$reporte->SetDisplayMode("default");
		$reporte->Output();
//------------------------------------------------------		
	/*		$xr = new xajaxResponse();
		$xr->script("var newWin = window.open('$reporte', '_blank', 'fullscreen=yes')");
		return $xr;
	*/
	}
	
	echo frmReportesBanXtipo();
?>