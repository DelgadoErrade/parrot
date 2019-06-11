<?php

class CLS_USUARIO extends CLS_PARROT{
    private $usuario;
    private $clave;
    private $link;
    private $bd;
//----------------------------------------------------------------------------------------------------
    
	function __construct(){
		parent :: __construct();
	/*	$this->bd = parent::conexion;*/
    }

//----------------------------------------------------------------------------------------------------
    function getUsuario(){
    	return $this ->usuario;
    }
//----------------------------------------------------------------------------------------------------
    function incluir($usuario,$clave){
	    $ret=false;
	    if(!$this->existe($usuario)){
		    $this ->usuario =$usuario;
	    	$this ->clave = md5($clave);
	    	$ret = $this->usuariosInsert($usuario, $this ->clave);	
		}
		if($ret){
			return TRUE;
		}else{
			return FALSE;
		}
    }

//----------------------------------------------------------------------------------------------------
    function existe($login){
    	
    	$fila = $this->numRegistros("usuarios","usuario='$login'");
    	if ($fila == 0 ){
    		return FALSE;
    	}else{
    		return TRUE;
    	}
    }
//----------------------------------------------------------------------------------------------------    
    function borrar($login){
    	$ret = $this->usuariosDelete("usuario='$login'");
    	if($ret){
    		return TRUE;
    	}else{
    		return FALSE;
    	}			
    }
//----------------------------------------------------------------------------------------------------    
    function cambiaClave($formulario){
    	$xr = new xajaxResponse();
    	$cooperativa = new CLSCOOPERATIVA();
    	$mensaje = clsUSUARIO::valida($formulario);
    	extract($formulario);
    	if(!$mensaje){
			$idusuario = $_SESSION["idusuario"];
			$clave = md5($nuevaclave1);
			$strSQL = "UPDATE usuarios SET  clave = '$clave' WHERE  idusuario = $idusuario";
			$r = $cooperativa->consultagenerica($strSQL);
			if($r){
				//$xr->alert("Se ha actualizado la clave correctamente.");
				$salida = "Se ha actualizado la clave correctamente.";
				$xr->script("document.getElementById('ventanaModal').style.visibility='visible';");
     			$xr->call("xajax_asignarEnXajax", "ventanaModal", "innerHTML", NULL, "alerta",$salida);
				if($_SESSION["USUARIO"]=="ADMINISTRADOR"){
					$xr->call("xajax_asignarEnXajax","contenedor", "innerHTML",  NULL, "menuAdministrador");									
				}else{
					$xr->call("xajax_asignarEnXajax","contenedor", "innerHTML",  NULL, "menuPrincipalUsuario", 1);
				}
			}else{
				$xr->alert("No se ha actualizado la clave!.  Intente de nuevo.");
			}
		}else{
			$xr->alert($mensaje);		
		}
    	return $xr;
    }
//----------------------------------------------------------------------------------------------------
	function valida($f){
		if(empty($f['clave'])) return "El campo 'Clave Actual' no puede ser nulo.";
		if(empty($f['nuevaclave1'])) return "El campo'Nueva Clave' no puede ser nulo";
		if(empty($f['nuevaclave2'])) return "El campo 'Repita Nueva Clave' no puede ser nulo.";
		if($f['nuevaclave1'] != $f['nuevaclave2']) return "Las nuevas claves deben ser iguales.";
		$idusuario = $_SESSION["idusuario"];
		$cooperativa = new CLSCOOPERATIVA();
		$registros = $cooperativa->usuariosRecords("idusuario=$idusuario");
		foreach($registros as $registro){
			extract($registro);
		}
		if(MD5($f['clave']) != $clave) return "La 'Clave Actual' no coincide con la registrada.";
	 	return 0;
	}

//----------------------------------------------------------------------------------------------------
    function frmLogin(){
        $evento = "onkeypress=\"if(enterCheck(event)==13)xajax_validaUsuario(xajax.getFormValues('idFormulario'))\"";
        $frm="<br/><br/><br/><br/><center>
        <div id='frmLogin' >
            <div id='frmEnvoltura'>
    		<div id='frmDiv'>
    			<form id='idFormulario'>
    				<div><p class='login'>Usuari@:</p></div><div><input type='text' name='usuario' autofocus id='user' /></div>
    				<div><p class='login'>Clave:</p></div><div><input type='password' autofocus name='password' $evento/></div>
    				<div class='submitDiv'><br/><input type='button' value='Iniciar sesi&oacute;n' 
                    onclick=\"xajax_validaUsuario(xajax.getFormValues('idFormulario'))\"/></div><br/>
    			</form>
    		</div>
        		</div>
        </div>
        </center>";
        return $frm;
    }
//----------------------------------------------------------------------------------------------------
	function frmCambioClave(){
		$accion = "onclick=\"xajax_cambiaClave(xajax.getFormValues('frm'))\";";
		$tabla = new Table;
		$htm = $tabla->Top("Cambio de clave de acceso al sistema.");
		$htm .= "<center>
		<div style='border: thick solid #000;height:290px;width:280px;background-color::#d1e3ed;border-color:#3882C7;box-shadow: 4px 4px 5px #999;-webkit-box-shadow: 4px 4px 5px #999; -moz-box-shadow: 4px 4px 5px #999;';>
		<form id='frm'>
		<table border='0' style='background-color:fff'>
			<tr>
				<td colspan='2' align='center' style='background-color:#bd10ef; color:fff;padding:5px' ><h3>Cambiar Clave de Acceso</h3></td>
			</tr>
			<tr>
				<td rowspan='3' align='center'><img src='./imagenes/claves5.jpg' alt='' width='80px' height='80px' /></td>
				<td align='center' align='center' style='background-color: #7bb9f7; color:#000;'>Clave Actual<br/>".
				frm_password("clave", "", 12, 12, "autofocus  id='clave'")
			."<br/>&nbsp;</td>
			<tr>
				<td align='center' style='background-color:#f9b5fb; color:#000'>Nueva Clave<br/>".
				frm_password("nuevaclave1", "", 12, 12)
			."<br/>&nbsp;</td>
			<tr>
				<td align='center' style='background-color: #8cf273; color:#000'>Repita Nueva Clave".
				frm_password("nuevaclave2", "", 12, 12)
			."<br/>&nbsp;</td>
			</tr>
			<tr><td  colspan='2' align='center'><br/>&nbsp;".frm_button("grabar", "Grabar", $accion)."<br/>&nbsp;</td></tr>
		</table><br/>
		</form>
		</div>
		</center><br/>";
		$htm .= $tabla->Footer();
		return $htm;
	}

//----------------------------------------------------------------------------------------------------

    function validaUsuario($frmEntrada){
    	$xr = new xajaxResponse();    
        // $respuesta->alert("entr� a validar usuario.");
        // return $respuesta;
         $bool = 0;
    if($frmEntrada["usuario"]=="" && $frmEntrada["password"]==""){
        $salida = "Debe ingresar usuario y clave.";
    } else{
      if($frmEntrada["usuario"]==""){
        $salida = "Debe ingresar usuario para continuar.";
      }else{
        if($frmEntrada["password"]==""){
            $salida = "Debe ingresar la clave para continuar.";
        }else{
        
        $llogin=$frmEntrada["usuario"];
        $password=$frmEntrada["password"];
        $bd = new CLS_PARROT();
        $numReg =  $bd->numRegistros("user","username='".$llogin."'");
        if($numReg == 0 ){
          $salida="Usuario no existe!.";
        }else{
         $Kpassword = MD5($password);
         $numReg =  $bd->numRegistros("user","username='$llogin' and password = '$Kpassword'");
         if($numReg == 1){
            $salida = "Acceso aceptado...";
            // $salida = menuPrincipal();//			OOOOOJJJJJOOOOO
               $xr->assign("menu","style.display", "block");
               $xr->script("xajax_showGrid('CLS_COMPROBANTES');");		
               $bool = 1;
               return $xr;
            }else{       //  Clave de acceso inapropiada...
               $salida = "Clave incorrecta";
          }
         }
        }
       }
    }
    //  aqui se hace algo con $salida.
    $xr = $xr->script("aviso('$salida')");
     return $xr;
  }
//----------------------------------------------------------------------------------------------------

}
/* Procesos validados con mysql:

 borrar
 existe
 frmlogin
 getUsuario
 incluir
 modificar
 validaUsuario		** OJO ** Por validar.

*/

/* Procesos validados con mysqli:

 borrar
 existe
 frmlogin
 getUsuario
 incluir
 modificar
 validaUsuario		** OJO ** Por validar.

*/
/*
$miClase = new clsUSUARIO();
$P = "delgadoerrade";
$c1 = "odagledesoj";*/
//$r = $miClase->incluir($P, $c1);*/

//	SELECT COUNT( * ) AS numero FROM usuarios WHERE usuario = 'Rosaira'AND 
//	
/*echo ("clave = '2eae20f25a6d70838357f5efbbcaa923' ");
echo("<br/>");
echo ("<br/>"."$c1 = ".md5($c1));*/

/*$r = $miClase->modificar($P, $c1, "RPO1993");

if($r){
	echo("El registro se actualiz� correctamente.");
}else{
	echo("El registro NO se actualiz�.");
}*/

//echo($miClase->frmLogin());
 //echo md5("123456");
//echo $miClase->frmCambioClave();
?>
