<?php
	//session_start();
	require_once("./lecturaXLS_X.php");
	require_once('../xajax/xajax_core/xajax.inc.php');
	$xajax = new xajax();
	$xajax->setFlag("debug", true);
	$xajax->register(XAJAX_FUNCTION, 'mostrarArchivo');
	$xajax->register(XAJAX_FUNCTION, 'ventanaEmergente');
	$xajax->register(XAJAX_FUNCTION, "procXLS");
	$xajax->processRequest();
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Carga de Excel a MySQL</title>
	<?php
			$xajax->printJavascript("../xajax/");
	?>
	    <!-- archivos  CSS -->
  	<link href="../css/bootstrap.min.css" rel="stylesheet">
  	<link href="../css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
  	<link rel="stylesheet" href="../../modal/css/modal.css" />
  	<!--		archivos javascript 	-->
  	<script src="../js/jquery.min.js"></script>
	<script src="../js/fileinput.min.js" type="text/javascript"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../../modal/js/modal.js"></script>
</head>
<body>
	<?php
		echo buscarArchivo();
	?>
</body>
</html>