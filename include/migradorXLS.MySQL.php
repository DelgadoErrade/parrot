<?php
	session_start();
	//require_once("./clscotizador.php");
	require_once("./form_items.php");
	require_once("./funciones_fecha.php");
	//$objeto = new clscotizador;
	$html='<!DOCTYPE html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    <link href="../css/style.css" rel="stylesheet">
	    <link href="../css/bootstrap.min.css" rel="stylesheet">
	    <link href="../css/jquery.smartmenus.bootstrap.css" rel="stylesheet">
		<link href="../css/planisalud.css" rel="stylesheet" type="text/css">
		<link href="../css/resptabla.css" rel="stylesheet">
	<title>Migracion Excel</title>
	</head>

<body>
 <div class="container" style="margin-top: 50px;background-color: white">                   
		<header class="row" id="header">
			<div class="col-md-12"><h3 class="text-center">Migraci&oacute;n de Datos desde Excel</h3></div>
		</header>
		<div class="row">
	        <div class="col-md-12">
	        	<div  id="pagina">
	        		

<div align="center">
<form action="" method="post" enctype="multipart/form-data" name="form1">
<table width="90%" border="0">
    <td>&nbsp;</td>
  <tr>
    <td  >
      <div class="row">
       	<div class="col-md-6"><strong>Fecha de vigencia de los datos:</strong> </div>
		<div class="col-md-6"><input type="text" name="fecha" id="fecha" class="form-control"  require></div>
      </div>    
       <div class="row">   
        <div class="col-md-4"> <p> <strong>Seleccionar Archivo:</strong></p></div>
       <div class="col-md-4">
      	<input type="file" name="archivo"  class="form-control" accept=".xlsx" id="archivo">
      	</div>
      </div>
      </td>
      <td class="vertcelda">
      <div class="row ">
      	<div class="col-md-6 vertcelda" align="right"><strong>Actualizar la BD</strong>
      <label><input type="radio" name="radio" value="s" checked />SI</label>
      <label><input type="radio" name="radio" value="n" />NO</label></div>
      <div class="col-md-6"><input type="submit" name="button" class="btn bg-verde btn-lg" id="button" 
      value="Actualizar Base de Datos"></div>
      </div>
    </td>
    </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</form>
<table border="1" width="100%">
<thead>
<tr  class="bg-gris">
<th><center><strong>A</strong></center></th>
<th><center><strong>B</strong></center></th>
<th><center><strong>C</strong></center></th>
<th><center><strong>D</strong></center></th>
<th><center><strong>E</strong></center></th>
<th><center><strong>F</strong></center></th>
<th><center><strong>G</strong></center></th>
<th><center><strong>H</strong></center></th>
<th><center><strong>I</strong></center></th>
<th><center><strong>J</strong></center></th>
<th><center><strong>K</strong></center></th>
</tr>
</thead>
<tbody>';
	if(isset($_POST['radio'])){
		//subir la imagen del articulo
		$nameEXCEL = $_FILES['archivo']['name'];
		$tmpEXCEL = $_FILES['archivo']['tmp_name'];
		$tamanio = $_FILES['archivo']['size'];
		$extEXCEL = pathinfo($nameEXCEL);
		$urlnueva = "xls/$nameEXCEL";
		if(trim($nameEXCEL)!=""){
	
			
		if(is_uploaded_file($nameEXCEL)){
			//copy($tmpEXCEL,$urlnueva);	
			echo '<div align="center"><strong>Datos Actualizados con Exito</strong></div>';
		}
		
	
//cargamos el archivo
$lineas = file($tmpEXCEL);
 
//inicializamos variable a 0, esto nos ayudará a indicarle que no lea la primera línea
$i=0;
 $xpla=1;
 $sFecha=d_US_MySQL($_POST['fecha']);
 $xlin=8;
 $rxcober=array();
//Recorremos el bucle para leer línea por línea
foreach ($lineas as $linea_num => $linea)
{ 
   if($i != 0) 
   { 
       $datos = explode(";",$linea);
		$xMasculino=trim($datos[7]);			
		$xFemenino=trim($datos[8]);
		$html.= '<tr>';
		for($i=0;$i<=$xlin;$i++)
			$html.= '<td>'.trim($datos[$i]).'</td>';
			if ( $xpla==1 ){
				$html.= '<td></td><td></td>';
			}
			$html.= '</tr>';
			switch (trim($datos[0])) {

			    case "0-9":
			        $fmin=0;$fmax=9; 
			    	$xgraba=TRUE;
			        break;
			    case "10-19":
			        $fmin=10;$fmax=19;
			    	$xgraba=TRUE;
			        break;
				case "20-29":
			        $fmin=20;$fmax=29;
			    	$xgraba=TRUE;
					break;
			    case "30-39":
			        $fmin=30;$fmax=39;
			    	$xgraba=TRUE;
			        break;
			    case "40-49":
			        $fmin=40;$fmax=49;
			    	$xgraba=TRUE;
			        break;
			    case "50-59":
			        $fmin=50;$fmax=59;
			    	$xgraba=TRUE;
			        break;
			    case "60-64":
			        $fmin=60;$fmax=64;
			    	$xgraba=TRUE;
			        break;
			    case "65-69":
			        $fmin=65;$fmax=69;
			    	$xgraba=TRUE;
			        break;
			    case "70-74":
			        $fmin=70;$fmax=74;
			    	$xgraba=TRUE;
			        break;
			    case "75-79":
			        $fmin=75;$fmax=79;
			    	$xgraba=TRUE;
			        break;
			    case "80-84":
			        $fmin=80;$fmax=84;
			    	$xgraba=TRUE;
			        break;
			    case "85+":
			        $fmin=85;$fmax=159;
			    	$xgraba=TRUE;
			        break;
			    case "COBERTURA Bs. 100.000":
			    	$xpla=1;
			        $xcober=0;
			    	$xgraba=FALSE;
			    	$rxgraba=FALSE;
	    	
			        break;
			    case "COBERTURA Bs. 150.000":
			        $xcober=1;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 200.000":
			        $xcober=2;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 250.000":
			        $xcober=3;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 300.000":
			        $xcober=4;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 350.000":
			        $xcober=5;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 400.000":
			        $xcober=6;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 450.000":
			        $xcober=7;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 500.000":
			        $xcober=8;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 600.000":
			        $xcober=9;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 700.000":
			        $xcober=10;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 800.000":
			        $xcober=11;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 900.000":
			        $xcober=12;
			    	$xgraba=FALSE;
			        break;
			    case "COBERTURA Bs. 1.000.000":
			        $xcober=13;
			    	$xgraba=FALSE;
			        break;
			    case "ATENCION MEDICA PREVENTIVA":
			    	$xpla=2;
			    	 $xlin=10;
			    	$rxgraba=FALSE;
			    	break;			    
			    case "SERVICIO DE MEDICINAS":
			    	$xpla=3;
			    	$rxgraba=FALSE;
			    	
			    	break;
			    case "MATERNIDAD Y CUIDADOS AL RECIEN NACIDO":
			    	$xpla=4;
			    	$rxgraba=FALSE;
			    	
			    	break;
			    case "CUOTA":
			    	$rxgraba=FALSE;
					for($r=0;$r<=$xlin;$r++){
						$srX=trim($datos[$r]);
		    			$trxcober=$objeto->consultagenerica( "SELECT `idcobertura` FROM `cobertura` WHERE
		    			 `monto` =$srX 	AND  `idplanes` =$xpla");
		    			$rxcober[$r]=$trxcober[0]['idcobertura'];
		    		}
			    	break;
			    case "ANUAL":
			    	$rxgraba=TRUE;
			    	break;
			    default:
			    	$xgraba=FALSE;
			    	$rxgraba=FALSE;
			}
	 			if($xgraba and $xpla==1 and $_POST['radio']=='s'){
						$objeto->primasInsert(1,$xcober,$xpla,$fmin,$fmax,'F',numeroIngles($xFemenino),$sFecha);
						$objeto->primasInsert(1,$xcober,$xpla,$fmin,$fmax,'M',numeroIngles($xMasculino),$sFecha );
				}elseif($rxgraba and $xpla>1 and $xpla<=4 and $_POST['radio']=='s'){
					for($r=1;$r<=$xlin;$r++){
						$objeto->primasInsert(1,$rxcober[$r],$xpla,0,0,'', numeroIngles(trim($datos[$r])),
						$sFecha);
						$v=0;
					}
				}//$xpla==1
		}$i++;}
		}
		echo '<div align="center"><strong>Debe Seleccionar Archivo o escribir fecha</strong></div>';
		}
$html.='</tbody>
</table><br/><br/><br/>
</div>

			</div>
	      </div>

	    </div>
    </div>
</body>
</html>';	
echo $html;
?>