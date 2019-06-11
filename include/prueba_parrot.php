<?php
include_once("cls_parrot.php");
include_once("cls_usuario.php");

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
		//return $resultado;
		/*$xr = new xajaxResponse();
		$xr->assign($idElemento, $atributo, $resultado);
		return $xr; */
		return $resultado;	
	}

	echo (asignarEnXajax("contenedor", "innerHTML", "CLS_USUARIO","frmLogin"));


?>