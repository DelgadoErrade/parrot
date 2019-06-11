<?php
	//session_start();

	/*define("LOG_ENABLED", 0); // Enable debuggin
	define("FILE_LOG", "/tmp/xajaxDebug.log");  // File to debug.
	define("ROWSXPAGE", 5); // Number of rows show it per page.
	define("MAXROWSXPAGE", 10);  // Total number of rows show it when click on "Show All" button.
*/

//	INICIO DE LA CLASE XAJAX.	
	require ('./xajax/xajax_core/xajax.inc.php');
	$xajax = new xajax();
//	$xajax->setFlag("debug", true);
	$xajax->setCharEncoding("ISO-8859-1");

//   funciones comunes de la clase xajaxgrid.
	$xajax->register(XAJAX_FUNCTION,"showGrid");
	$xajax->register(XAJAX_FUNCTION,"add");
	$xajax->register(XAJAX_FUNCTION,"edit");
	$xajax->register(XAJAX_FUNCTION,"show");
	$xajax->register(XAJAX_FUNCTION,"delete");
	$xajax->register(XAJAX_FUNCTION,"save");
	$xajax->register(XAJAX_FUNCTION,"update");
/*	$xajax->register(XAJAX_FUNCTION,"editField");
	$xajax->register(XAJAX_FUNCTION,"updateField");*/
//-----------------------------------------------------------------------------------------------
//	Otras funciones XAJAX...
//-----------------------------------------------------------------------------------------------	
	$xajax->register(XAJAX_FUNCTION, 'asignarConXajax');
	$xajax->register(XAJAX_FUNCTION, array("validaUsuario", "CLS_USUARIO","validaUsuario"),"cls_usuario.php");
	$xajax->register(XAJAX_FUNCTION, array("repor_comprobante", "CLS_COMPROBANTES","repor_comprobante"),"cls_comprobantes.php");
//	$xajax->register(XAJAX_FUNCTION, array("sumar", "CLS_COMPROBANTES","sumar"),"cls_comprobantes.php");
	$xajax->register(XAJAX_FUNCTION, array("imprimir_comp", "CLS_COMPROBANTES","imprimir_comp"),"cls_comprobantes.php");
	$xajax->register(XAJAX_FUNCTION, array("reporteComprobantes", "CLS_COMPROBANTES","reporteComprobantes"),"cls_comprobantes.php");
	$xajax->register(XAJAX_FUNCTION, array("mostrarMensaje", "CLS_ASIG_DEDU","mostrarMensaje"),"cls_asig_dedu.php");
	$xajax->register(XAJAX_FUNCTION, array("cambiarDetalles", "CLS_QUINCENAS","cambiarDetalles"),"cls_quincenas.php");
	$xajax->register(XAJAX_FUNCTION, array("calcular", "CLS_QUINCENAS","calcular"),"cls_quincenas.php");
	$xajax->register(XAJAX_FUNCTION, array("frmReporteQuincena", "CLS_QUINCENAS","frmReporteQuincena"),"cls_quincenas.php");
	$xajax->register(XAJAX_FUNCTION, array("imprimirRecibo", "CLS_QUINCENAS","imprimirRecibo"),"cls_quincenas.php");
	$xajax->register(XAJAX_FUNCTION, array("imp_recibo", "CLS_QUINCENAS","imp_recibo"),"cls_quincenas.php");
	$xajax->register(XAJAX_FUNCTION, array("frmNomina", "CLS_QUINCENAS","frmNomina"),"cls_quincenas.php");
	$xajax->register(XAJAX_FUNCTION, array("imp_nomina", "CLS_QUINCENAS","imp_nomina"),"cls_quincenas.php");
	$xajax->register(XAJAX_FUNCTION, array("mostrarArchivo", "CLS_TBL_MOV_BANCARIOS","mostrarArchivo"),"cls_tbl_mov_bancarios.php");
	$xajax->register(XAJAX_FUNCTION, array("procXLS", "CLS_TBL_MOV_BANCARIOS","procXLS"),"cls_tbl_mov_bancarios.php");
	
	$xajax->register(XAJAX_FUNCTION, array("reporteBancario", "CLS_TBL_MOV_BANCARIOS","reporteBancario"),"cls_tbl_mov_bancarios.php");
	/*   FUNCION DE AUTOCOMPLETAR  */
	$xajax->register(XAJAX_FUNCTION,"autocompletar");
	$xajax->register(XAJAX_FUNCTION,"funcion_lenta");	
	$xajax->processRequest();
	
?>
