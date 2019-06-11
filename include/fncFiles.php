<?php
/*
        FUNCIONES PARA EL MANEJO DE ARCHIVOS PLANOS
*/
  /*  modo de apertura de archivo */
 
  function fduAbrirArchivo($nombreArchivo, $modo="r"){
  	$modo = strtolower($modo);
  //$modo: Lectura ("r") o Escritura ("w").
    $canal=fopen($nombreArchivo,$modo);
    if($canal<>"")
    {
      $nombre_archivo=$nombreArchivo;
    }
    return $canal;
  }

  function fduCerrarArchivo($canal){
           fclose($canal);
  }

  function fduGrabar($canal,$contenido){
    if(fwrite($canal, $contenido) === FALSE) {
        echo "No se puede escribir al archivo ($nombre_archivo)";
        exit;
    }
  }

  function fduLeerArchivo($canal)
  {
    $linea = fgets($canal);
  }
  
 
?>
