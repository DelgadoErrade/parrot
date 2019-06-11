<?php
	function autocompletar(){
		$sql = "SELECT DISTINCTROW `beneficiario` FROM `comprobantes` order by beneficiario";
		$x = new CLS_PARROT;
		$y=array();
		$RECORDS = $x->consultagenerica($sql);
		foreach($RECORDS as $record){
			extract($record);
			$y[]="$beneficiario";
		}
		$beneficiarios = json_encode($y);
		$sql = "SELECT DISTINCTROW `cancela` FROM `comprobantes` order by cancela ";
		$f=array();
		$RECORDS = $x->consultagenerica($sql);
		foreach($RECORDS as $record){
			extract($record);
			$f[] = "$cancela";
		}
		$pagadores = json_encode($f);
		$xr = new xajaxResponse();
		$xr->script("Lockr.set('beneficiarios', $beneficiarios);Lockr.set('pagadores', $pagadores);cargar_autocompletar();");
		//$xr->script("");
		return $xr;	
	}
?>