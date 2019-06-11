<?php
	include_once("./include/servidor.php");
?>
<!doctype html>
<html>
<head>
	<meta charset="iso-8859-1">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximun-scale=1, minimun-scale=1">
	<meta name="Parrot2004" content="Parrot V2.">
	<title>Parrot System 2004, c.a.</title>
<!--  AGREGAR ICONO EN LA PESTAÑA DEL NAVEGADOR         -->	
	<link href='./images/favicon.ico' rel='shortcut icon' type='image/x-icon'>
<!--    ARCHIVOS DE ESTILOS                     -->
	<link rel="stylesheet" href="./css/estilos.css" />
	<?php
		$xajax->printJavascript("xajax/");
	?>
	<style>
		header > img { 
    width: auto;
    height: 70px;
    border-radius: 7px 0 0 7px;
    float: left;
    /*z-index: 3;*/
}
@media (max-width: 640px) {
	
	header img{height: 60px;}
	header h1{
		font-size: 20px;
	}
}
@media (max-width: 440px) {
	
	header img{height: 60px;}
	header h1{
		font-size: 20px;
	}
}
	</style>
	
</head>
<body>
<!--	ARCHIVOS JAVASCRIPT   -->	
	<script src="./js/cargando.js"></script>
	<script src="./js/jquery-1.12.3.min.js"></script>	<!--  ARCHIVO BASE DE JQUERY  -->  
	<script src='./js/jquery.dataTables.min.js'></script>
	<script src="./js/bootstrap.min.js"></script>
	<script src="./js/dataTables.buttons.min.js"></script> 	
	<script src='./js/dataTables.responsive.min.js'></script>
	<script src="./js/dataGrid.js"></script>
	<script src="./js/modal.js"></script>      										
	<script src='./js/scw.js'></script>		<!--	Funciones para mostrar calendario.	-->
	<script src="./js/script.js"></script>
	<script src="./js/funciones.js"></script>
	<script src="./js/awesomplete.min.js" async></script>										
	<script src="./js/lockr.min.js"></script>	<!-- ARCHIVO PARA TRANSFERIR DATOS DE PHP A JAVASCRIPT	-->
	<script src="./js/fileinput.min.js" ></script>
	<script src="./js/fncautoZ9.js"></script>	<!-- ARCHIVO CON INSTRUCCIONES JQUERY PARA AUTOCOMPLETAR	-->	
	<script> 
/*xajax_funcion_lenta();*/
		xajax_asignarConXajax("contenedor", "innerHTML", "CLS_USUARIO", "frmLogin");//	FUNCIONA EXCELENTE  ..
		
		//document.getElementById("usuario").focus;
		//xajax_asignarConXajax('contenedor','innerHTML','CLS_TBL_MOV_BANCARIOS', 'buscarArchivo');
	</script>
	<div id='main' class="container">
		<header>
			<img src="../alquileres/imagenes/parrotLogo.png" alt="Parrot">
			<h1>Sistema Administrativo</h1>
		</header>
		
	    <nav id="menu"><?php echo menu();?>	</nav>
	    <section id="contenedor">

	    </section>
		<footer>&copy; Prof. J. R. Delgado Errade.  Octubre de 2016</footer>
		
	</div>
</body>
</html>