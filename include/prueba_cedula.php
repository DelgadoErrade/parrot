<?PHP

	include_once("./cls_empleados.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>PRUEBA DE USO DE LA FUNCIO DE CEDULA</title>
<meta name="" content="">
</head>
<body>
<script src="../js/funciones.js"></script>

<?php
	$emp = new CLS_EMPLEADOS();
	echo $emp->frmempleados();
?>

</body>
</html>