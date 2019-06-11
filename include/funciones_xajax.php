<?php
//		funciones_xajax.php
//		Prof. José R. Delgado E. 
//			Julio de 2015.

//-----------------------------------------------------------------------
/**
*ESTABLECE UN VALOR O PROPIEDAD DE UN OBJETO CON XAJAX. 
*
*@param 	string 	$idElemento Identificacion (id) del elemento que sera afectado por la funcion.
*@param 	string 	$atributo	nombre del atributo HTML que recibira la funcion.
*@param 	string 	$funcion 	Nombre de la funcion que se ejecutara
*@param 	string 	$parFun		Argumentos que recibira la funcion.
*@return 	Ejecuta la clase xajaxResponse sobre el elemento identificado.	
*/		
	function asignarConXajax($idElemento, $atributo, $clase = NULL, $funcion = "" , $parFun = NULL){
		// por mejorar cuando es en el mètodo de una clase se tienen muchos argumentos.
		if($parFun==NULL AND $clase == NULL){
			$resultado = $funcion();
		}else{
			if($clase == NULL){
				if(!is_array($parFun)){
					$resultado = $funcion($parFun);
				}else{
					$resultado = $funcion($parFun);
				}
			}else{
				$lClase = new $clase();
				if($parFun == NULL){
					$resultado = $lClase->$funcion();
				}
				if(!is_array($parFun)){
				//	$resultado = $lClase->$funcion($parFun);
				}else{
					//El argumento es un arreglo...
				}
			}
		}
		$xr = new xajaxResponse();
		$xr->assign($idElemento, $atributo, $resultado);
		return $xr; 	
	}	
		
function menu(){
	$ac01 = "onclick=\"xajax_showGrid('CLS_COMPROBANTES');\"";
	$ac02 = "onclick=\"xajax_showGrid('CLS_ASIG_DEDU');\"";
	$ac03 = "onclick=\"xajax_showGrid('CLS_EMPLEADOS');\"";
	$ac04 = "onclick=\"xajax_showGrid('CLS_QUINCENAS');\"";
	$ac05 = "onclick=\"xajax_showGrid('CLS_SUELDOS');\"";
	$ac06 = "onclick=\"xajax_showGrid('CLS_UNIDAD_TRIBUTARIA');\"";
	$ac07 = "onclick=\"xajax_showGrid('CLS_USER');\"";
	$ac08 = "onclick=\"xajax_reporteComprobantes();\"";
	$ac09 = "onclick=\"xajax_frmNomina();\"";
	$ac10 = "onclick=\"xajax_asignarConXajax('contenedor','innerHTML','CLS_QUINCENAS', 'frmReporteQuincena');\"";
	$ac11 = "onclick=\"xajax_showGrid('CLS_TBL_BANCOS');\"";
	$ac12 = "onclick=\"xajax_showGrid('CLS_TBL_MOV_BANCARIOS');\"";
	$ac13 = "onclick=\"xajax_showGrid('CLS_TBL_TIPO_MOVIMIENTOS');\"";
	$ac14 = " onclick=\"xajax_asignarConXajax('contenedor', 'innerHTML', 'CLS_TBL_MOV_BANCARIOS', 'frmReportesBancos');\"";
	$ac15 = "onclick=\"xajax_asignarConXajax('contenedor','innerHTML','CLS_TBL_MOV_BANCARIOS', 'frmReportesBanXtipo');\"";
	$ac16 = "onclick=\"xajax_asignarConXajax('contenedor','innerHTML','CLS_TBL_MOV_BANCARIOS', 'buscarArchivo');\"";	
	$htm ="<div id='cssmenu'>
		<ul>
		   <!--<li><a href='#'><span>Home</span></a></li>-->
		   <li><a href='#' $ac01 ><span>Comprobantes</span></a></li>
		   <li class='active has-sub'><a href='#' ><span>N&oacute;mina</span></a>
		      <ul>
		         <li class='last'><a href='#' $ac02><span>Asignaciones y/o<br/>Deducciones</span></a></li>
		         <li class='last'><a href='#' $ac03 ><span>Empleados</span></a></li>
		         <li class='last'><a href='#' $ac04><span>Quincenas</span></a></li>
		         <li class='last'><a href='#' $ac05><span>Sueldos</span></a></li>
		         <li class='last'><a href='#' $ac06><span>Unidad Tributaria</span></a></li>
		      </ul>
		   </li>
		   <li><a href='#' $ac07><span>Usuarios</span></a></li>
		   <li class='active has-sub'><a href='#'><span>Reportes</span></a>
		   <ul>
		         <li class='last'><a href='#' $ac08><span>Comprobantes</span></a></li>
		         <li class='last'><a href='#' $ac09><span>Nomina quincenal</span></a></li>
		         <li class='last'><a href='#' $ac10><span>Recibo Quincenal</span></a></li>
		   </ul>
		   </li>
		 		   <li class='active has-sub'><a href='#'><span>Bancos</span></a>
		   <ul>
		         <li class='last'><a href='#' $ac11><span>Bancos</span></a></li>
		         <li class='last'><a href='#' $ac16><span>Migraci&oacute;ns</span></a></li>
		         <li class='last'><a href='#' $ac12><span>Movimientos</span></a></li>
		         <li class='last'><a href='#' $ac13><span>Tipos movimientos</span></a></li>
		         <li class='last'><a href='#' $ac14><span>Reportes Bancarios</span></a></li>
		         <li class='last'><a href='#' $ac15><span>Reportes Bancarios por Tipo</span></a></li>
		   </ul>
		   </li>
		  <!-- <li class='last'><a href='#'><span>Contacto</span></a></li>-->
		</ul>
		</div>";
		return $htm;
}	
	
function funcion_lenta(){
	sleep(3);
	$objResponse = new xajaxResponse();
	$objResponse->Assign("capa_cargando","innerHTML","Finalizado");
//	$objResponse->script("document.getElementById('transparente').style='display:none'");
	return $objResponse;
}
	
	
?>