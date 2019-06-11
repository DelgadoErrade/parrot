<?php

	function UrlLocal(){
		$url="http://".$_SERVER['HTTP_HOST'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		//return $url;
		$xr = new xajaxResponse();
		$xr -> alert("La Direccion actual es <br/>$url");
		//$xr ->assign("pagina","innerHTML","HOLA, MUNDO.....-");
		return $xr;
	}
	
	function ejecutarFuncion($nombreFuncion ){
		$porEjecutar = $nombreFuncion;
		return $porEjecutar();
	}


/**
*ESTABLECE UN VALOR O PROPIEDAD DE UN OBJETO CON XAJAX. 
*
*@param 	string 	$idElemento Identificaci�n del elemento que serᠡfectado por la funci�n.
*@param 	string 	$atributo	nombre del atributo HTML que recibirᠬa funci�n.
*@param 	string 	$funcion Nombre de la funci�n que se ejecutarᮍ
*@param 	string 	$parFun	Par᭥tro que recibirᠬa funci�n.
*@return 	Ejecutarᠬa clase xajaxResponse sobre el elemento identificado.	
*/		
	function asignarEnXajax($idElemento, $atributo, $clase = NULL, $funcion = "" , $parFun = NULL){
		if($parFun==NULL AND $clase == NULL){
			$resultado = $funcion();
		}else{
			if($clase == NULL){
				if(!is_array($parFun)){
					$resultado = $funcion($parFun);
				}else{
					//El argumento es un arreglo...	
					/*$parametros = "";
					$n = count($parFun);
					$i=1;
					foreach($parFun as $par){
						if(is_string($par)){
							$parametros .= "\"".$par."\"";
						}else{
							$parametros .= $par;
						}
						if($i <= $n-1){
							$parametros .= ", ";
							$i++;
						}
					}*/
					$resultado = $funcion($parFun);
				}
			}else{
				$lClase = new $clase();
				if($parFun == NULL){
					$resultado = $lClase->$funcion();
				}else{
					if(!is_array($parFun)){
					$resultado = $lClase->$funcion($parFun);
				}else{
					//El argumento es un arreglo...
				}
				}
				
			}
			
		}
		$xr = new xajaxResponse();
		$xr->assign($idElemento, $atributo, $resultado);
		return $xr; 
	}

	function primerNombre($nombre){
		$nombre = TRIM($nombre); 
		if(strstr($nombre, " ")){
			$iEspacio = strpos($nombre, " ");
			$pNombre = substr($nombre,0, $iEspacio);
		}else{
			$pNombre = $nombre;
		}
		return $pNombre;	
	}

	function CiertoFalso($variable){
		return ($variable) ? "true" : "false";
	}

//-------------------------------------------------------------------------------------------------------------------

/**
*CONVIERTE todos los n�meros de una matriz a formato espa�ol: separadores de mil (.) y decimal (,)) con 2 decimales. 
*	Requiere de la funcion numeroEspanol.
*@param 	matriz 	$matriz	N�mero en formato ingl鳮
*@return 	matriz 	
*/

function convNumFormat($matriz){
	//Podemos recorrer este array utilizando un bucle for y un peque�o truco para
	//acceder al indice mayor del array tanto de las filas como de las columnas
	$filas = count($matriz, 0);
	//redondeo al alza con ceil()
	$columnas = ceil(count($matriz,1)/count($matriz,0))-1;
	//echo 'Este arreglo tiene '.$filas.' filas y '.$columnas.' columnas<br/>';
	/*
	//Ciclo para arreglar la matriz por los indices numericos
	*/
	for ($fil = 0; $fil < $filas; $fil++) {
	  for ($col = 1; $col < $columnas; $col++) {
	  	if(is_numeric($matriz[$fil][$col])){
			$matriz[$fil][$col] = numeroEspanol($matriz[$fil][$col]);
		}
	  //  echo $matriz[$fil][$col].'<br/>';
	  }
	}
	
	/*
			Ciclo para arreglar matriz con indices de caracteres	 
	*/
	
	foreach($matriz as $key1 => $fila){
		foreach($fila as $key2 =>$item){
			if($key2 != "cedula" AND is_numeric($item)){
				$matriz[$key1][$key2]= numeroEspanol($matriz[$key1][$key2]);
			}
		}
	}
	return $matriz;
}
//-------------------------------------------------------------------------------------------------------------------

/**
* CREA REPORTES EN PDF Y LOS PRESENTA POR PANTALLA.
* @param array 	$formulario	Contiene los nombres de los campos y sus valores tomados del formulario.
* @param string	$reporte	Nombre del reporte a imprimir.
* @return	Reporte por pantalla.
*/
	function reportes($formulario){
		$Meses = array(1=>'enero',2=>'febrero',3=>'marzo',4=>'abril',
                      5=>'mayo',6=>'junio',7=>'julio',8=>'agosto',
                      9=>'septiembre',10=>'octubre',11=>'noviembre',12=>'diciembre');
		$clss = new CLSCOOPERATIVA();
		extract($formulario);

		//Cambio archivo plano por variables de $_session a  $_COOKIE.
		$arreglo = array("mes"=>$mes, "anio"=>$anio, "grupo"=>$grupo[0], "idpersona"=>$cmbPersonas);
		setcookie("mes", $mes, time()+6000);		//Crea cookie con duración de diez (10) minutos.
		setcookie("anio", $anio, time()+6000);		//Crea cookie con duración de diez (10) minutos.
		setcookie("grupo", $grupo[0], time()+6000);		//Crea cookie con duración de diez (10) minutos.
		setcookie("idpersona", $cmbPersonas, time()+6000);		//Crea cookie con duración de diez (10) minutos.
		setcookie("tipo", $tipo[0], time()+6000);		//Crea cookie con duración de diez (10) minutos.
		/*$_SESSION["mes"] = $mes;
		$_SESSION["anio"] = $anio;
		$_SESSION["grupo"] = $grupo[0];
		$_SESSION["idpersona"] = $cmbPersonas;
		$_SESSION["tipo"] = $tipo[0]*/;
		$xr = new xajaxResponse();
		if($tipo[0]=='mensual'){
			//REPORTE MENSUAL
			$reporte = "./include/rptMensuales.php";
			$registros = $clss->movimientosRecords("MONTH(fecha) = $mes AND YEAR(fecha) = $anio");
			if($registros==null){
				$salida = "No hay registros en <br/> $Meses[$mes] / $anio.";
					//$xr->alert();
				    //$respuesta->alert($salida);
			     $xr->script("document.getElementById('ventanaModal').style.visibility='visible';");
			     $xr->call("xajax_asignarEnXajax", "ventanaModal", "innerHTML", NULL, "alerta",$salida);
				return $xr;	
			}else{
/*				$reporte = new rptMensuales();
				$reporte->AddPage();
				$reporte->body();
				//$reporte->SetDisplayMode();
				$reporte->Output();*/
			}

			if(trim($grupo[0])=="resumen"){
				$reporte = "./include/rptResumen.php";
			}
			$xr->script("var newWin = window.open('$reporte', '_blank', 'fullscreen=yes')");
		}elseif($tipo[0]=='anual'){
			//REPORTE ANUAL
			$reporte = "./include/rptAnual.php";
			$registros = $clss->movimientosRecords();
			if($registros==null){
				$xr->alert("No hay registros grabados de movimientos en el sistema.");
				return $xr;	
			}
			if(trim($grupo[0])=="resumen"){
				$reporte = "./include/rptResumen.php";
			}	
			$xr->script("var newWin = window.open('$reporte', '_blank', 'fullscreen=yes')");
/*	
			
		$grupo		= "grupo";
		$idPersona	=  "";
			
		$reporte = new rptAnuales( $grupo[0], $cmbPersonas);
		$reporte->AddPage();
		$reporte->body();
		$reporte->SetDisplayMode("fullpage");
		$reporte->Output();
		$xr->alert("Se presentó salida exitosa.");*/
		}else{
			$reporte = "./include/rptResumen.php";
			$registros = $clss->movimientosRecords();
			if($registros==null){
				$xr->alert("No hay registros grabados de movimientos en el sistema.");
				return $xr;	
			}	
			$xr->script("var newWin = window.open('$reporte', '_blank', 'fullscreen=yes')");
		}
		return $xr;
	}
	
	function alerta($mensaje){
		$htm = "<div id='alerta' class = 'ventanaModal' style='z-index:20'><br/>
				<div id='mensaje' style='padding:10px;'>$mensaje</div><br/><br/>
				<div>
					<form>
					<input type='button' id='name' value='Continuar' onKeyPress='enterpressalert(event, this);' onclick=\"document.getElementById('ventanaModal').style.visibility='hidden';document.getElementById('ventanaModal').innerHTML='';return false;\" />
					</form>
					</div>
			</div>";
		return $htm;
	}

	function confirmacion($mensaje, $accion){
		$accion = "xajax_grabaChequeo(xajax.getFormValues('frm'), $accion);";
		$htm ="<div id='confirmacion' class = 'ventanaModal' style='z-index=12'>
		    	<br/>
				<div id='mensaje' style='padding:10px;'>$mensaje</div>
				<br/>
				<div><input type='button' value='SI' style='width:50px;' onclick = \"$accion  document.getElementById('ventanaModal').style.visibility='hidden';\" /> &nbsp; &nbsp; &nbsp; &nbsp;
				<input type='button' value='NO'  style='width:50px;' onclick=\"document.getElementById('ventanaModal').style.visibility='hidden';\"  /> </div>
			</div>";
		//return $htm;
		$xr = new xajaxResponse();
		$xr->script("document.getElementById('ventanaModal').style.visibility='visible';");
		$xr->assign("ventanaModal","innerHTML",$htm);
		return $xr;
	}
	
	
	//$X = reporte();
	//echo confirmacion("Ud. es un verdadero pingo!!.", "onclick=\"alert('No jodas')\"");
	
	//	echo asignarEnXajax("ventanaModal", "innerHTML", null,"confirmacion", array("mensaje"=>"Ud. es un verdadero pingo..", "accion"=>"onclick=\"alert('No jodas')\""));
	//echo alerta("este es un mensaje");
?>