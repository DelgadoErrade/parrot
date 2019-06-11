<?php


/**********************************************************************
*  PHP Perfect Form Items v1.0				by Jose Carlos Garc?Neila
* ----------------------------------------------------------------------
*  Construye formularios mediante codigo PHP para separar de forma clara
*  la logica de programacion con la logica de diseño
* 
*  http://www.distintiva.com/jose/_perf_form
************************************************************************/


//- Funciones auxiliares--------------------------------------------------------



/**
*CONVIERTE UNA EXPRESION STRING A MINUSCULA
*
*@param string 	$val	Una expresi?n cualquiera en string.
* @return string
*/
function walk_tolower(&$val){
	$val=strtolower($val);
}

/**
*CONVIERTE TODOS LOS ELEMENTOS DE UN ARREGLO A MINUSCULAS.
*
*@param array $a_arr		Arreglo con cadenas de caracteres.
* @return array 
*/
function array_lower($a_arr){
	array_walk($a_arr, 'walk_tolower');
	return $a_arr;
}



//- Funciones de abstraccion de formularios -------------------------------------

/**
*CREA UN SELECT con base a un array de textos y valores de una misma dimension
*
*@param 	string  $name		Nombre del objeto SELECT
*@param 	array 	$arrTxt		Elemento oculto que sera tomado al seleccionar un elemento del SELECT
*@param 	array 	$arrVals	Texto a mostrar del  elemento del SELECT
*@param 	string 	$default	Texto seleccionado por defecto del objeto SELECT
*@param 	string 	$extraTag 	String que contendr?ropiedades adicionales del objeto Select.
* @return SELECT
*/
function frm_select($name, $arrTxt, $arrVals, $default='', $extraTag=''){
	$tmp="<select name='$name' $extraTag>";
	$items=count($arrTxt);
	if($items!=count($arrVals)) return $tmp."<option>ERR! en el array de valores</select>";
	for($i=0;$i<$items;$i++){
		$sel=' selected';
		$val=$arrVals[$i];
		if(is_array($default)){
			if(!in_array( strtolower($val), array_lower($default) )) $sel='';
		}else{
			if($val != $default) $sel='';//if(!preg_match('/'.$val.'/',$default)) $sel='';
		}
		$tmp.="<option value='$val' $sel>".$arrTxt[$i]."</option>";
	}
	return $tmp.'</select>';
}


/**
*CREA UNA LISTA (SELECT)  CON BASE A UN ARRAY de textos y valores de una misma dimension
*
*@param 	string  	$name		Nombre que tendr?l objeto LIST
*@param 	int  		$size		Nombre que tendr?l objeto SELECT
*@param 	array 	$arrTxt		Elemento oculto que ser?omado al seleccionar un elemento del SELECT
*@param 	array 	$arrVals		Texto a mostrar del  elemento del SELECT
*@param 	string 	$default		Texto seleccionado por defecto del objeto SELECT
*@param 	string 	$extraTag 	String que contendr?ropiedades adicionales del objeto Select.
*@return SELECT
*/
function frm_list($name, $size,  $arrTxt, $arrVals, $default='', $extraTag=''){
	return frm_select($name, $arrTxt, $arrVals, $default, "size=$size $extraTag");
}



/**
*CREA UN OBJETO LISTA (selec. m?ltiple)  con base a un array de textos y valores de una misma dimension. (Parecido al SELECT)
*
*@param 	string  	$name		Nombre que tendr?l objeto SELECT
*@param 	int 		$size		Elementos a mostrar  del elemento del SELECT 
*@param 	array 	$arr_txt		Elemento oculto que ser?omado al seleccionar un elemento del SELECT
*@param 	array 	$arr_vals		Texto a mostrar del  elemento del SELECT
*@param 	string 	$default		Texto seleccionado por defecto del objeto SELECT
*@param 	string 	$extra_tag 	String que contendr?ropiedades adicionales del objeto Select.
*@return 	LIST 
*/
function frm_list_multi($name, $size, $arr_txt, $arr_vals, $default='', $extra_tag=''){
	return frm_list($name."[]", $size, $arr_txt, $arr_vals, $default, "multiple $extra_tag");
}


/**
*CREA UN OBJETO CHECKBOX. Si se le pasa una variable por $var_in y coincide con $ck_val, se selecciona
*
*@param 	string  	$name		Nombre que tendr?l objeto.
*@param 	string 	$ck_val		Elemento oculto que sera tomado al seleccionar un elemento del Checkbox.
*@param 	string 	$var_in		Si este valor coincide con $ck_val, se selecciona este objeto.
*@param string 	$extra_tag 	String que contendr?ropiedades adicionales del objeto.
*@return CHECKBOX
*/
function frm_check($name, $ck_val, $var_in='', $extra_tag=''){
	$ck='';
	if(strtolower($ck_val)==strtolower($var_in)) $ck=' checked';
	return "<input type='checkbox' name='$name' value='$ck_val'$extra_tag$ck>";
}

/**
*CREA UN OBJETO RADIO. Si se le pasa una variable por $var_in y coincide con $val, se selecciona
*
*@param 	string  	$name		Nombre que tendr?l objeto.
*@param 	string 	$val			Elemento oculto que sera tomado al seleccionar un elemento del objeto.
*@param 	string 	$var_in		Si este valor coincide con $ck_val, se selecciona este objeto.
*@param 	string 	$extra_tag 	String que contendr?ropiedades adicionales del objeto.
*@return 	RADIO
*/
function frm_radio($name, $val, $var_in='', $extra_tag=''){
	$ck='';
	if(strtolower($val)==strtolower($var_in)) $ck=' checked';
	return "<input type='radio' name='$name' value='$val' $extra_tag $ck>";
	
	
	
}

/**
*CREA UN OBJETO TEXTBOX.
*
*@param 	string  	$name		Nombre que tendr?l objeto.
*@param 	string 	$val			String que ser?ostrado en el objeto.
*@param 	string 	$size		Tama?o del objeto.
*@param 	string 	$max_length	Cantidad m?ma de caracteres que admitir?l objeto. 
*@param 	string 	$extra_tag 	String que contendr?ropiedades adicionales del objeto.
*@return TEXTBOX
*/
function frm_text($name, $val, $size, $max_length, $extra_tag=''){
	return "<input type='text' name='$name' size='$size' maxlength='$max_length' value='$val' $extra_tag>";
}
//echo frm_text("campo","","6", "3"," class='form-control text-center' id='campo'");
/**
*CREA UN OBJETO NUMBER.
*
*@param 	string  	$name		Nombre que tendr?l objeto.
*@param 	string 	$val			String que ser?ostrado en el objeto.
*@param 	string 	$size		Tama?o del objeto.
*@param     string  $min 		Numero menor del objeto
* @param    string  $max		Numero mayor del objeto
*@param 	string 	$extra_tag 	String que contendr?ropiedades adicionales del objeto.
*@return TEXTBOX
*/
function frm_number($name, $val, $size, $min, $max, $extra_tag=''){
	return "<input type='number' name='$name' size='$size' value='$val' min='$min' max='$max' $extra_tag>";
}

/**
*CREA UN OBJETO EMAIL.
*
*@param 	string  $name		Nombre que tendra objeto.
*@param 	string 	$val			String que ser?ostrado en el objeto.
*@param 	string 	$size		Tama?o del objeto.
*@param 	string 	$max_length	Cantidad m?ma de caracteres que admitir?l objeto. 
*@param 	string 	$extra_tag 	String que contendr?ropiedades adicionales del objeto.
*@return TEXTBOX
*/
function frm_email($name, $val, $size, $max_length, $extra_tag=''){
	return "<input type='email' name='$name' size='$size' maxlength='$max_length' value='$val' $extra_tag>";
}



/**
*CREA UN OBJETO PASSWORD.
*
*@param 	string  	$name		Nombre que tendr?l objeto.
*@param 	string 	$val			String que ser?ostrado en el objeto.
*@param 	string 	$size		Tama?o del objeto.
*@param 	string 	$max_length	Cantidad m?ma de caracteres que admitir?l objeto. 
*@param 	string 	$extra_tag 	String que contendr?ropiedades adicionales del objeto.
*@return 	PASSWORD
*/
function frm_password($name, $val, $size, $max_length, $extra_tag=''){
	return "<input type='password' name='$name' size='$size' maxlength='$max_length' value='$val' $extra_tag>";
}

/**
*CREA UNA CAJA DE TEXTO  OCULTO (HIDDEN).
*
*@param 	string  	$name		Nombre que tendr?l objeto.
*@param 	string 	$val			Valor que tendr?l objeto.
*@param 	string 	$extra_tag 	String que contendr?ropiedades adicionales del objeto.
*@return 	HIDDEN
*/
function frm_hidden($name, $val, $extra_tag=''){
	return "<input type='hidden' name='$name' value='$val' $extra_tag>";
}

/**
*CREA UN OBJETO BOTON DE COMANDOS.
*
*@param 	string $name		Nombre que tendr?l objeto.
*@param 	string $val		Valor que mostrar?l objeto.
*@param 	string $extra_tag 	String que contendr?ropiedades adicionales del objeto.
*@return 	BUTTON
*/
function frm_button($name, $val, $extra_tag=''){
    return "<input type='button' name='$name' value='$val' $extra_tag>";
}

/**
*CREA UN OBJETO RESET.
*
*@param 	string $name		Nombre que tendr?l objeto.
*@param 	string $val		Valor que mostrar?l objeto.
*@param 	string $extra_tag 	String que contendr?ropiedades adicionales del objeto.
*@return 	RESET
*/
function frm_reset($name, $val, $extra_tag=''){
	return "<input type='reset' name='$name' value='$val' $extra_tag>";
}


/**
*CREA UN OBJETO SUBMIT.
*
*@param 	string  	$name		Nombre que tendr?l objeto.
*@param 	string 	$val			Valor que mostrar?l objeto.
*@param 	string 	$extra_tag 	String que contendr?ropiedades adicionales del objeto.
*@return 	SUBMIT
*/
function frm_submit($name, $val, $extra_tag=''){
	return "<input type='submit' name='$name' value='$val' $extra_tag>";
}

/**
*CREA UN OBJETO TEXTAREA.
*
*@param 	string  $name		Nombre que tendr?l objeto.
*@param 	string $val		Valor que mostrar?l objeto.
*@param 	int 	  $cols 		Columnas de texto que contendr?l objeto.
* @param 	int 	  $rows 		Filas de texto que contendr?l objeto.
*@return 	TEXTAREA
*/
 function frm_textArea($name, $val, $cols, $rows){
 	return "<textarea name='$name' rows='$rows' cols='$cols'>$val</textarea>";
 }

//ECHO frm_textArea("textarea", "Con valor en una linea",20, 10);


/**
*CREA UN OBJETO BOT? CON IMAGEN. Ej. echo frm_imagen("lupa.jpg", "buscar",30,22);
*
*@param 	string  	$imagen			Nombre del archivo de imagen a mostrar
*@param 	string 	$textoAlternativo 	Texto alternativo a la imagen.
*@param 	int 		$ancho 			ancho de la imagen en pixeles,
*@param 	int 		$alto 			Ancho de la imagen en pixeles.
*@param 	string	$extra_tag		String que contendr?ropiedades adicionales del objeto.
*@return image
*/
function frm_imagen($imagen, $textoAlternativo, $ancho="", $alto="", $extra_tag=""){
	$x = "";
	if($alto != ""){
		$x = " height='$alto'";
	}
	if($ancho !=""){
		$x .= " width='$ancho'";
	}
	return "<img src='$imagen' align='middle' alt='$textoAlternativo' $x $extra_tag />";
}

//$x = frm_imagen("./img/trash.png","Borrar",20,20);
//<button type='button' class='btn' title='Borrar item'  style= 'margin-top:0px;' id='fila2' onclick="('cuerpoTabla1', 'fila2')" ><img src='./img/trash.png' align='middle' alt='Borrar'  height='20' width='20'/></button>
/*$x = frm_imagen("./images/trash.png","Editar");*/
//echo $x;
//=  CREA UN BOTON QUE CAMBIA IMAGENES DINAMICAMENTE.
//     Requiere de funciones construidas en javascript para el cambio de imagenes din?camente.

function frm_enlace_imagen($url="#", $textoAlternativo="", $img = "", $nombre, $alto="", $ancho="", $extra_tag="" ){	
	$x = "";
	if($alto != ""){
		$x = " height='$alto'";
	}
	if($ancho !=""){
		$x .= " width='$ancho'";
	}
	$salida = "<a href ='$url' $extra_tag ><img src='$img' $x align='middle' border='0' alt='$textoAlternativo' name='$nombre'/></a>";
	return $salida;
}
/*
$x = frm_enlace_imagen("#", "Editar","../images/edit.png","","","","onclick=\"alert('Pronto editará.');\"");
echo $x;*/
/**
* CREA UNA ETIQUETA
* 
* @param 	string 	$texto		Texto a mostrar en la etiqueta
* @param	string	$idCampo	Nombre del id asociado al elemento de entrada de datos.
* @return   Etiqueta (label) con el texto a mostrar
*/
function frm_label($texto, $idCampo){
	//  agregado para html5 el 21/01/2016
	return "<label for='$idCampo'>$texto</label>";
}

/**
*CREA un link. 
*
*@param 	int  	$numeral	Entero que se utiliza para activar una referencia
*@param 	string 	$texto 		Texto a mostrar en el link
*@param 	string 	$extraTag 
*@return 	link
*/	
function frm_link($numeral, $texto, $extraTag=""){
	//	onclick='xajax_xmenu(0)'
	return "<a href='#$numeral' $extraTag >$texto</a>";
}


/**
*CREA UN OBJETO SELECT con datos de una base de datos.. 
*
*@param 	string 	$nombreCombo	nombre del objeto select
*@param 	string 	$campoVisible 	Campo a mostrar de la base de datos.
*@param 	string 	$campoClave		Campo clave relacionado con el campo a mostrar.
*@param 	string 	$entidad		Nombre de la entidad donde se encuentran los datos.
*@param 	string 	$clase			Nombre de la clase de base de datos generada por mi programa
*@param 	string 	$consulta		Instruccion SQL que se empleaa en caso de no pasar el argumento $entidad.
*@param 	string 	$extraTag		Propiedades o eventos  opcionales que se agregar?al select.
*@param 	string 	$default		Texto seleccionado por defecto del objeto SELECT
*@param		LOGICO  $decimales      TRUE para mostrar en formato numérico español con 2 decimales. FALSE para cualquier otra opcion.
*@return 	SELECT	
*/	
function frm_comboGenerico($nombreCombo, $campoVisible, $campoClave, $entidad, $clase, $consulta="",$extraTag="",$default="", $decimales=FALSE){
	   //include_once("clscondominio.php");
	    $objeto = new $clase();    //Instanciacion de la clase que contiene los metodos sobre la base de datos.
	    if($consulta==""){
	        $strSQL = "select $campoClave, $campoVisible from $entidad order by $campoVisible";        
	    }else{
	        $strSQL = $consulta;
	    }
	    $aterritorios = $objeto->consultagenerica($strSQL);  
	    if(is_array($aterritorios) AND count($aterritorios) == 0){
			$combo = "NO SE TIENEN DATOS PARA MOSTRAR DE LA ENTIDAD ". strtoupper($entidad);//frm_text($nombreCombo,"NO SE TIENEN DATOS PARA MOSTRAR", 40, 40, "DISABLED");
			$_SESSION["alerta"] = $combo;
			return $combo;
		} 
	    $i=0;
	    foreach($aterritorios as $fila){
	    	if($decimales){
	    		//if((is_numeric($fila["$campoVisible"]))){
					$col1[$i] = numeroEspanol($fila["$campoVisible"],2);				
				//}else{
				//	$col1[$i] = $fila["$campoVisible"];					
				//}
			}else{
	        	$col1[$i] = $fila["$campoVisible"];
			}
	    
	        $col2[$i] = $fila["$campoClave"];
		 if($i==0){
			$_SESSION[$nombreCombo] = $fila["$campoClave"];	
		 }
	       $i++;
	    }
	    $combo = frm_select($nombreCombo,$col1,$col2,$default,$extraTag);
	    return $combo;
	}   


/**
*CREA UN OBJETO LIST con datos de una base de datos.. 
*
*@param string 	$nombreCombo	nombre que tendr?l objeto select
*@param int 		$size	Elementos que mostrar?l objeto lista.
*@param string 	$campoVisible 	Campo a mostrar de la base de datos.
*@param string 	$campoClave		Campo clave relacionado con el campo a mostrar.
*@param string 	$entidad			Nombre de la entidad donde se encuentran los datos.
*@param string 	$clase			Nombre de la clase de base de datos generada por mi programa
*@param string 	$consulta			Instrucci?n SQL que se emplear?n caso de no pasar el argumento $entidad.
*@param string 	$extraTag			Propiedades o eventos  opcionales que se agregar?al select.
*@return LIST 	Retorna una lista con los datos de una base de datos.	
*/
function frm_ListGenerico($nombreLista, $size, $campoVisible, $campoClave, $entidad, $clase, $consulta="",$extraTag=""){
	    $objeto = new $clase();    //Instanciaci?n de la clase que contiene los m?dos sobre la base de datos.
	    if($consulta==""){
	        $strSQL = "SELECT $campoClave, $campoVisible FROM $entidad ORDER BY $campoVisible";        
	    }else{
	        $strSQL = $consulta;
	    }
	    $aterritorios = $objeto->consultagenerica($strSQL);   
	    $i=0;
	    foreach($aterritorios as $fila){
	        $col1[$i] = $fila["$campoVisible"];
	        $col2[$i] = $fila["$campoClave"];
/*		 if($i==0){
			$_SESSION[$nombreCombo] = $fila["$campoClave"];	
		 }*/
	       $i++;
	    }
	    $combo = frm_list($nombreLista, $size, $col1, $col2,"",$extraTag);
	    return $combo;
	}   
	

//------------------------------------------------------------------------------------------------------------- 

/**
*CONVIERTE UN N?ERO de formato espa?ol a ingl?. 
*
*@param 	string 	$numeroEspanol	N?mero en formato espa?ol.
*@param 	integer 	$decimales       D?tos decimales ?que tendr?l n?mero. 
*@return 	float	
*/
function numeroIngles($numeroEspanol, $decimales=2, $separadoresMil=TRUE){
	//Cambia el formato de un n?mero string 123.423,43 as 123423.43
	//1?. Se quitan los puntos.
	$s = str_replace(".","",$numeroEspanol);
	//2?. Se cambia la coma por el punto y se transforma en n?mero de coma flotante
	$t = (float) str_replace(",",".",$s);
	if($separadoresMil){
		$nIngles = number_format($t, $decimales,".","");	
	 }else{
		$nIngles = round($t, $decimales);	
	}
	return $nIngles;
}
//echo numeroIngles("-12,25");
/**
*CONVIERTE UN N?ERO de formato ingl?a espa?ol.. 
*
*@param 	string 	$numeroIngles	N?mero en formato ingl?
*@param 	integer 	$decimales       D?tos decimales ?que tendr?l n?mero. 
*@return 	float	
*/
function numeroEspanol($numeroIngles, $decimales=2, $separadoresMil=TRUE){
	if($numeroIngles == 0){
		$nIngles = "";
	}else{
		
		if($separadoresMil){
			$nIngles = number_format($numeroIngles, $decimales,",",".");	
		 }else{
			$nIngles = round($numeroIngles, $decimales);	
		}
	
	}
	return $nIngles;
}
//------------------------------------------------------------------------------

/**
*CREA UN TOOLTIPS sobre cualquier elemento. 
*
*@param 	string 	$texto	Texto a mostrar en el tooltip.
*@param 	string 	$color       Color de fondo que contendr?l tooltip. 
 *@param integer 	$ancho     ancho en pixeles que tendr?l tooltip.
*@return string	
*/
function frm_tooltips($texto, $color = "", $ancho=""){
/* Para su uso se requiere de:
1.  Crear una capa en el body de la pagina web con el id = 'dhtmltooltip'.
2.  Insertar en el body el codigo javascript: <script type='Text/JavaScript' src='tooltips.js'></script>
3.  Agregar en la hoja de estilo las caracter?icas iniciales de la capa

#dhtmltooltip{
	position: absolute;
	width: 150px;
	border: 2px solid black;
	padding: 2px;
	background-color: lightyellow;
	visibility: hidden;
	z-index: 100;
//  Quite la linea siguiente para eliminar la sombra. Debe estar siempre como la ?ltima l?a dentro del CSS
	filter: progid:DXImageTransform.Microsoft.Shadow(color=gray,direction=135);
}
*/	
	$tooltip = "onMouseover = \"ddrivetip('$texto'";
	if($color != ""){
		$tooltip .= ", '$color'";
	}else{
		$tooltip .= ",''";
	}
	if($ancho!=""){
		$ancho = ($ancho < 60) ? 60 : $ancho;
		$tooltip .= ", $ancho)\"";	
	}else{
		$tooltip .= ",60)\"";
	}
	$tooltip .=  "   onMouseout= 'hideddrivetip()' " ;
	return $tooltip;
}


/*	Crea un captcha en cualquier ?a sobre la p?na web.
		Condici?n: Tener el archivo captcha.php y el direcctorio resources con sus respectivos archivos.
	retorna un segmento de c?digo con la imagen y la caja de texto donde se valida el texto.
		El valor del captcha se obtiene en una variable de sesi?n.
*/
	function frm_captcha(){
		$accion = "onclick=\"document.getElementById('captcha').src='captcha.php?'+Math.random();";
    		$accion .= " document.getElementById('captcha-form').focus();\"";
		$htm = "<p><strong>Escriba la siguiente palabra:</strong></p>";
		$htm .= frm_imagen("captcha.php","captcha","","", "id='captcha'" )."<br/>";
		$htm .= frm_link(1, "No es legible? Cambie el texto.", $accion)."<br/><br/>*";
 		$htm .=  frm_text("captcha","",10,10," id='captcha-form'")."<br/>";
		return $htm;
	}
	
/*	
		Condici?n: Crear una capa cuyo id  =  'menuVertical'.  All?e debe personalizar la ubicaci?n del menu
	retorna un segmento de c?digo con la imagen y la caja de texto donde se valida el texto.
		El valor del captcha se obtiene en una variable de sesi?n.
*/
/**
*Crea un men? vertical con ubicaci?n y colores definidos en el archivo pestanas.css
*
*@param 	array  $texto  Textos a mostrar en el menu
*@param 	array $funciones Funciones a llamar al activarse el elemento del menu.
*@return string  con el menu y sus opciones.
*/
	function frm_menuv($texto, $funciones){	
		$htm = "<div class='pestanas'><ul id='menuv'>";
		$elementos = count($texto);
		$_SESSION['elementos'] = $elementos;
		$contador = 100;
		//$condicion = array("activa","inactiva");
		
		for($i = 0; $i < $elementos; $i++){
			$accion = "onclick=\"".$funciones[$i].";";
			$accion .= "xajax_cambia_pestana($i)\"";
			$condicion = ($i==0) ? "activa" : "inactiva";
			$htm .= "<li id='pestana$i' class= 'pestanainactiva'>".frm_link($condicion, $texto[$i],$accion)."</li>";
		}
		$htm .= "</ul>";
		return $htm;
	}
	
/**
*Crea una imagen miniatura de un archivo gr?co.
*
*@param 	string $pathImg  Direcci?n donde se encuentra el archivo de imagen
*@param 	string $imagen  Nombre del archivo de imagen con su extensi?n.
*@param 	integer  $ancho Ancho de la imagen.  El alto se ajusta por defecto para guardar las proporciones.
* @param 	string $pathThumb  Direcci?n donde se encuentra el archivo phpThumb.php
*@return string  con el la imagen en miniatura.
*/		
 function 	frm_thumb($pathImg, $imagen, $ancho, $pathThumb="../phpThumb/" ){
 	$path = $pathThumb."phpThumb.php";
	//$pathImg = "../res_paso_real/imagenes/";  //El path es relativo a la ubicaci?n de  phpThumb.php
	//$pathImg = "../res_paso_real/thumbs/";  //El path es relativo a la ubicaci?n de  phpThumb.php
 	$htm = "<img src ='$path?src=$pathImg$imagen&w=$ancho' />";
	return $htm;
 }


/***************************************************
* Software: Funciones de fecha                                          *
* Version:  3.0                                                                 *
* Date:     2007-10-22                                                      *
* Author:   Prof. José R. Delgado Errade                              *
* License:  Propietario                                                      *
*                                                                                   * 
* Puedes utilizar y modificar este software a tu conveniencia.*
***************************************************
          OBSERVACION    MUY    IMPORTANTE
  PARA TODOS LOS CASOS, LA FECHA DEBE TENER EL FORMATO MES/DIA/AÑO
/**

*Obtiene el año de una fecha dada escrita en formato inglés (mm/dd/aaaa).
*
*@param date $strFecha		Fecha suministrada en formato mm/dd/aaaa
* @return entero
*/
    function iAnno($strFecha)
    {
       $fecha=strtotime($strFecha);
       return date("Y",$fecha);
    }

 
 /**
*Obtiene el mes de una fecha dada escrita en formato inglés (mm/dd/aaaa).
*
*@param date $strFecha		Fecha suministrada en formato mm/dd/aaaa
* @return entero
*/
    function iMes($strFecha)
    {
       $fecha=strtotime($strFecha);
       return date("m",$fecha);
    }


 
/**
*Crea un calendario con dos combos correspondientes a meses y años.
*
*@param string 		$mes			nombre que se asigna al combo de los meses
*@param string 		$anio			nombre que se asigna al campo de los años.
*@param string 		$dReferencia		fecha (inglesa) de referencia que será mostrada por defecto.
*@param string 		$extraTag			características adicionales de calendario.
* @return calendario
*
*/
    function frm_medioCalendario($mes,$anio,$dReferencia='',$extraTag='')
    {
       $anios = array();
       $imeses= array(1,2,3,4,5,6,7,8,9,10,11,12);
       $meses = array('ene','feb','mar','abr','may','jun',
                          'jul','ago','sep','oct','nov','dic');
       $annioInicial = date("Y");
       $annioFinal = $annioInicial-1;
       if ($dReferencia=='') {
         $hoy = date("m/d/Y");
         $mesActual = date("m");
       } else {
          $fecha=date("$dReferencia");
          $hoy = $fecha;
          $mesActual = iMes($hoy);
       }
       for ($i=0; $i<=2; $i++)  {
         $anios[$i] = $annioInicial;
         $annioInicial = $annioInicial-1;
       }
       $lMes=frm_select($mes,$meses,$imeses,$mesActual, $extraTag);
       $lAnn=frm_select($anio,$anios,$anios,iAnno($hoy), $extraTag);
       return $lMes.$lAnn;
    }


/**
*CREA UN OBJETO NUMERO QUE ADMITE HASTA DOS DECIMALES.
*
*@param 	string  $name		Nombre del objeto.
*@param 	string 	$val		String que mostrara el objeto (número, en este caso).
*@param 	string 	$size		Tamaño del objeto.
*@param 	string 	$max_length	Cantidad maxima de caracteres que admite objeto. 
*@param 	string 	$extra_tag 	String con propiedades adicionales del objeto.
*@return TEXTBOX
*/
function frm_numero($name, $val, $size, $max_length, $extra_tag=''){
//	Para su uso se requiere de la funcion javascript NumCheck()
	return "<input type='text' name='$name' size='$size' maxlength='$max_length' value='$val'  onkeypress='return NumCheck(event, this);' style='text-align: right'  $extra_tag>";
}

function frm_cedula($name, $val, $size, $max_length, $extra_tag=''){
//	Para su uso se requiere de la funcion javascript NumCheck()
	return "<input type='text' name='$name' size='$size' maxlength='$max_length' value='$val'  onkeypress='return NumCheck(event, this, 10, 0);' style='text-align: right'  $extra_tag>";
}

 		
/***************************************************
*CREA UN OBJETO FILE .
*DATE: ENERO DE 2016
***************************************************
*@param	string $nombre	Nombre e id del objeto
*@param	string $extratag	Propiedades del objeto
*@return	Objeto de formulario input file
*/
	function frm_file($nombre, $extraTag=''){
		$file = "<input name=\"$nombre\" id=\"$nombre\"  type=\"file\" $extraTag>"; 
		return $file;	
	}
	
//--------------------------------------------------------------------------------------  
//echo "<img src ='../phpThumb/phpThumb.php?src=../res_paso_real/thumbs/avp_camaras.png&w=100' />";
//for($i=25;$i<=200;$i+=25){
/*	echo date("i:s");
	echo frm_thumb("../res_paso_real/fotos/", "entrada4.jpg", 75)."<br/><br/>";
	echo frm_thumb("../res_paso_real/fotos/", "entrada4.jpg", 400);	
	echo date("i:s");
*/	
/*
	include_once("clscotizador.php");
	$bd = new clscotizador();

;//select 0 AS idplanes, 0 as idcobertura, 'Seleccione monto' as monto, 0 as pago_anual UNION 
				
		for($i=0; $i<3; $i++){
			$sql = " (select distinct primas.idplanes, primas.idcobertura, monto,  pago_anual from primas
				inner join cobertura on primas.idcobertura = cobertura.idcobertura
				where fecha = (select max(fecha) as fecha from primas) and primas.idplanes = ";
			switch($i){
				case 0:
					$sql .= " 2 order by primas.idplanes, primas.idcobertura)"; 
					break;
				case 1:
					$sql .= " 3 order by primas.idplanes, primas.idcobertura)";
					break;
				case 2:
					$sql .= " 4 order by primas.idplanes, primas.idcobertura)";				
					break;	
			}		

	$imHtm = frm_comboGenerico("nombre", "monto", "idcobertura", "", "clscotizador",$sql,"","",TRUE);
	echo ($imHtm."<br/>");
	}
*/
/*	$activo = TRUE;
	echo("<b>Condici&oacute;n</b>:&nbsp;</td><td>Activado->".frm_radio('condicion',$activo, TRUE)."Desactivado->".frm_radio("condicion", $activo, FALSE));
*/
?>
