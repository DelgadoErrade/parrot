<?php
require_once "perf_sql.php"; 
class 	CLS_PARROT
{
     private $link;
     private $lBaseDatos;
     public $filas;
     public $campos;
     //-------------------------------------------------------------------------
     function __construct()
      {
        $servidor="localhost";  //Cambia, de ser necesario el nombre del servidor.
        $usuario="root";  		// Idem, con el usuario...
        $clave="";        		// Establece la clave...
        $basedatos="parrot";  // Coloca el nombre de la base de datos...
        $this->link = mysql_connect ($servidor,$usuario, $clave);
        if (!$this->link) {
            die('No se pudo conectar al servidor. ' . mysql_error());
            exit;
        }
        $mBD = mysql_select_db($basedatos, $this->link);
        if (!$mBD) {
            die ('No se puede abrir la base de datos : ' . mysql_error());
            exit;
         }
          $this->lBaseDatos = $basedatos;
        }
     //-------------------------------------------------------------------------
    function __destruct(){
    	// Se libera la conexion a la base de datos.
    	//echo("<br/>Fin de la clase.  Activada por el llamado a la funci&oacute;n desctructora.<br/>");
		//mysql_close($this->link);
	}
     //-------------------------------------------------------------------------
     function nuevo_id($entidad,$atributo){
         $sqlconsulta = "SELECT MAX(".$atributo.") + 1 as nuevo FROM ".$entidad.";";
         $result = mysql_query($sqlconsulta);
         if (!$result) {
             die('Fall� la consulta: ' . mysql_error());
             exit;
         }
         else {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            $valor = $row["nuevo"];
            if(is_numeric($valor)){
              return $valor;
            }else{
             return 1;
            }
         }
     }
//-------------------------------------------------------------------------
     function numRegistros($entidad,$criterio=""){
         if($criterio==""){
             $sqlConsulta = "SELECT COUNT(*) as registros FROM $entidad";
         }else{
             $sqlConsulta = "SELECT COUNT(*) as registros FROM $entidad WHERE $criterio";
         }
		 
		 $result = mysql_query($sqlConsulta);
         if (!$result) {
             die('Fall� la consulta: ' . mysql_error());
             exit;
         }
         else {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            $valor = $row["registros"];
            if(is_numeric($valor)){
              return $valor;
            }else{
             return 0;
            }
         }
     }
//-------------------------------------------------------------------------
 function max_id($entidad,$atributo){
         $sqlconsulta = "SELECT MAX(".$atributo.") as maximo FROM ".$entidad.";";
         $result = mysql_query($sqlconsulta);
         if (!$result) {
             die('Fall� la consulta: ' . mysql_error());
             exit;
         }
         else {
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            $valor = $row["maximo"];
            if(is_numeric($valor)){
              return $valor;
            }else{
             return 1;
            }
         }
     }

//-------------------------------------------------------------------------
function consultagenerica($strsql){
    if($strsql!=""){
        $result = mysql_query($strsql);
        if(!$result){
            //die('fall� la consulta: ' . mysql_error());
            //exit;
            return false;
        }else{
            if(preg_match("/select/i",$strsql) OR preg_match("/call/i",$strsql)){
                $this->filas = mysql_num_rows($result);
                $matrizasociativa = array();
                $ifila=0;
                while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                    foreach($row as $campo=>$valor){
                        $matrizasociativa[$ifila][$campo]=$valor;
                        //echo "$row = $valor </br>";
                    }
                    $ifila++; 
                }
                return $matrizasociativa;
            }
            return true;
        }
    }else{
        return false;
    }
}

 //-----------------------------------------------------------------------
      function atributos($entidad){
         $result = mysql_query("select * from ".$entidad);
         if (!$result) {
             die('fall� la consulta: ' . mysql_error());
             exit;
         }
         $i = 0;
         $matriz=array();
         while ($i < mysql_num_fields($result)) {
             $meta = mysql_fetch_field($result, $i);
             if (!$meta) {
                die("informaci�n de atributo no disponible.<br/>\n");
                exit;
             }
             $len   = mysql_field_len($result, $i);
             $arr[$i]=array('nombre'=>$meta->name,'tipo'=>$meta->type,
                      'numerico'=>$meta->numeric,'longitud'=>$len,
                      'no_nulo'=>$meta->not_null,'pk'=>$meta->primary_key,
                      'blob'=>$meta->blob, 'clave_multiple'=>$meta->multiple_key,
                      'entidad'=>$meta->table,'clave_unica'=>$meta->unique_key,
                      'sin_signo'=>$meta->unsigned,'ceros'=>$meta->zerofill);
             $i++;
         }
         return $arr;
     }
//-----------------------------------------------------------------------
   function asig_deduInsert($tipo, $descripcion, $formula) {
      $id_asig_dedu = $this->nuevo_id("asig_dedu","id_asig_dedu"); 
      $descripcion = utf8_decode($descripcion);
      $cols = get_commas(false, 'id_asig_dedu', 'tipo', 'descripcion', 'formula');
      $vals = get_commas(true, '!!'.$id_asig_dedu, $tipo, $descripcion, $formula);
      $strSQL = get_insert('asig_dedu',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function asig_deduUpdate($tipo, $descripcion, $formula, $id_asig_dedu) {
   		$descripcion = utf8_decode($descripcion);
   		
        $strSQL = "UPDATE asig_dedu SET  tipo = '$tipo',  descripcion = '$descripcion',  formula = '$formula' WHERE  id_asig_dedu = $id_asig_dedu";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function asig_deduDelete($condicion) {
      $strSQL = "DELETE FROM asig_dedu WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function asig_deduRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM asig_dedu";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('asig_dedu',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('asig_dedu');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function comprobantesInsert($numero, $factura, $fecha_factura, $beneficiario, $cancela, $fecha_cancela) {
   	  $id_comprobantes = $this->nuevo_id("comprobantes","id_comprobantes");
		$fecha_cancela = d_ES_MYSQL($fecha_cancela);
   	  	$fecha_factura = d_ES_MYSQL($fecha_factura);
      $cols = get_commas(false, 'id_comprobantes', 'n_comprobante', 'factura', 'fecha_factura', 'beneficiario', 'cancela', 'fecha_cancela');
      $vals = get_commas(true, '!!'.$id_comprobantes, $numero, $factura, $fecha_factura, strtoupper($beneficiario), strtoupper($cancela), $fecha_cancela);
      $strSQL = get_insert('comprobantes',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function comprobantesUpdate($numero, $factura, $fecha_factura, $beneficiario, $cancela, $fecha_cancela, $id_comprobantes) {
      	$fecha_cancela = d_ES_MYSQL($fecha_cancela);
   	  	$fecha_factura = d_ES_MYSQL($fecha_factura);	
        $strSQL = "UPDATE comprobantes SET  n_comprobante = '$numero',  factura = '$factura',  fecha_factura = '$fecha_factura',  beneficiario = '$beneficiario',  cancela = '$cancela',  fecha_cancela = '$fecha_cancela' WHERE  id_comprobantes = $id_comprobantes";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function comprobantesDelete($condicion) {
      $strSQL = "DELETE FROM comprobantes WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function comprobantesRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM comprobantes";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('comprobantes',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('comprobantes');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
// Se agregó el argumento $condicion 
   function empleadosInsert($cedula, $apellidos, $nombres, $fecha_nacimiento, $telefono, $direccion, $ciudad, $zona_postal, $condicion) {
   	  $apellidos = utf8_decode(strtoupper($apellidos));
   	  $nombres = utf8_decode(strtoupper($nombres));
   	  $fecha_nacimiento = d_ES_MYSQL($fecha_nacimiento);	
      $cols = get_commas(false, 'cedula', 'apellidos', 'nombres', 'fecha_nacimiento', 'telefono', 'direccion', 'ciudad', 'zona_postal', 'condicion');
      $vals = get_commas(true, '!!'.$cedula, $apellidos, $nombres, $fecha_nacimiento, $telefono, $direccion, $ciudad, $zona_postal, $condicion);
      $strSQL = get_insert('empleados',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
// Se agregó el argumento $condicion 
 
   function empleadosUpdate($apellidos, $nombres, $fecha_nacimiento, $telefono, $direccion, $ciudad, $zona_postal, $condicion, $cedula) {
	  $apellidos = utf8_decode(strtoupper($apellidos));
   	  $nombres = utf8_decode(strtoupper($nombres));
   	  $fecha_nacimiento = d_ES_MYSQL($fecha_nacimiento);	
         $strSQL = "UPDATE empleados SET  apellidos = '$apellidos',  nombres = '$nombres',  fecha_nacimiento = '$fecha_nacimiento',  telefono = '$telefono',  direccion = '$direccion',  ciudad = '$ciudad',  zona_postal = '$zona_postal', condicion = $condicion  WHERE  cedula = $cedula";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function empleadosDelete($condicion) {
      $strSQL = "DELETE FROM empleados WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function empleadosRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM empleados";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('empleados',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('empleados');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function nominaInsert($id_quincena, $id_asig_dedu, $monto, $dias) {
   	  $id_nomina = $this->nuevo_id("nomina", "id_nomina");
      $cols = get_commas(false, 'id_nomina', 'id_quincena', 'id_asig_dedu', 'monto', 'dias');
      $vals = get_commas(true, '!!'.$id_nomina, '!!'.$id_quincena, '!!'.$id_asig_dedu, '!!'.$monto,  '!!'.$dias);
      $strSQL = get_insert('nomina',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      	return false;
      } else {
      	return true;
   	  }
   }
//-----------------------------------------------------------------------
   function nominaUpdate($id_quincena, $id_asig_dedu, $monto, $dias, $id_nomina) {
         $strSQL = "UPDATE nomina SET  id_quincena = $id_quincena,  id_asig_dedu = $id_asig_dedu,  monto = $monto, dias = $dias WHERE  id_nomina = $id_nomina";
      $result = mysql_query($strSQL);
      if(!$result){
      	return false;
      } else {
      	return true;
   	  }
   }
//-----------------------------------------------------------------------
   function nominaDelete($condicion) {
      $strSQL = "DELETE FROM nomina WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function nominaRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=15) {
        $strSQL = "SELECT * FROM nomina";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('nomina',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('nomina');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function pago_nominaInsert($id_pago_nomina, $id_nomina, $forma_pago, $banco, $referencia, $monto_pago) {
      $cols = get_commas(false, 'id_pago_nomina', 'id_nomina', 'forma_pago', 'banco', 'referencia', 'monto_pago');
      $vals = get_commas(true, '!!'.$id_pago_nomina, '!!'.$id_nomina, $forma_pago, $banco, $referencia, '!!'.$monto_pago);
      $strSQL = get_insert('pago_nomina',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function pago_nominaUpdate($id_nomina, $forma_pago, $banco, $referencia, $monto_pago, $id_pago_nomina) {
         $strSQL = "UPDATE pago_nomina SET  id_nomina = $id_nomina,  forma_pago = '$forma_pago',  banco = '$banco',  referencia = '$referencia',  monto_pago = $monto_pago WHERE  id_pago_nomina = $id_pago_nomina";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function pago_nominaDelete($condicion) {
      $strSQL = "DELETE FROM pago_nomina WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function pago_nominaRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM pago_nomina";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('pago_nomina',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('pago_nomina');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function pagosInsert($id_comprobantes, $forma_pago, $banco, $referencia, $monto_pago) {
   	  $id_pagos = $this->nuevo_id("pagos","id_pagos"); 
      $cols = get_commas(false, 'id_pagos', 'id_comprobantes', 'forma_pago', 'banco', 'referencia', 'monto_pago');
      $vals = get_commas(true, '!!'.$id_pagos, '!!'.$id_comprobantes, $forma_pago, $banco, $referencia, '!!'.$monto_pago);
      $strSQL = get_insert('pagos',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      	return false;
      } else {
      	return true;
   	  }
   }
//-----------------------------------------------------------------------
/*   function pagosUpdate($forma_pago, $banco, $referencia, $monto_pago, $id_comprobantes) {
         $strSQL = "UPDATE pagos SET  forma_pago = '$forma_pago',  banco = '$banco',  referencia = '$referencia',  monto_pago = $monto_pago WHERE  id_comprobantes = $id_comprobantes";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }*/
//-----------------------------------------------------------------------
   function pagosDelete($condicion) {
      $strSQL = "DELETE FROM pagos WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function pagosRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM pagos";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('pagos',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('pagos');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function quincenasInsert($cedula, $fecha, $desde, $hasta, $n_recibo, $n_comprobante) {
   	  $id_quincenas = $this->nuevo_id("quincenas","id_quincenas");
   	  $fecha = d_ES_MYSQL($fecha);
   	  $desde = d_ES_MYSQL($desde);
   	  $hasta = d_ES_MYSQL($hasta); 
      $cols = get_commas(false, 'id_quincenas', 'cedula', 'fecha', 'desde', 'hasta','n_recibo', 'n_comprobante');
      $vals = get_commas(true, '!!'.$id_quincenas, '!!'.$cedula, $fecha, $desde, $hasta, '!!'.$n_recibo, '!!'.$n_comprobante);
      $strSQL = get_insert('quincenas',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function quincenasUpdate($cedula, $fecha, $desde, $hasta,  $n_recibo, $n_comprobante, $id_quincenas) {
   	  $fecha = d_ES_MYSQL($fecha);
   	  $desde = d_ES_MYSQL($desde);
   	  $hasta = d_ES_MYSQL($hasta);    	
      $strSQL = "UPDATE quincenas SET  cedula = $cedula,  fecha = '$fecha',  desde = '$desde',  hasta = '$hasta',
      	n_recibo = $n_recibo, n_comprobante = $n_comprobante WHERE  id_quincenas = $id_quincenas";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function quincenasDelete($condicion) {
      $strSQL = "DELETE FROM quincenas WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function quincenasRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM quincenas";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('quincenas',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('quincenas');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function sueldosInsert($cedula, $cargo, $sueldo_mensual, $fecha) {
      $id_sueldo = $this->nuevo_id("sueldos", "id_sueldo");
      $fecha = d_ES_MYSQL($fecha); 
      $cols = get_commas(false, 'id_sueldo', 'cedula', 'cargo', 'sueldo_mensual', 'fecha');
      $vals = get_commas(true, '!!'.$id_sueldo, '!!'.$cedula, $cargo, '!!'.$sueldo_mensual, $fecha);
      $strSQL = get_insert('sueldos',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function sueldosUpdate($cedula, $cargo, $sueldo_mensual, $fecha, $id_sueldo) {
   		$fecha = d_ES_MYSQL($fecha); 
        $strSQL = "UPDATE sueldos SET  cedula = $cedula,  cargo = '$cargo',  sueldo_mensual = $sueldo_mensual,  fecha = '$fecha' WHERE  id_sueldo = $id_sueldo";
      	$result = mysql_query($strSQL);
      	if(!$result){
      		return false;
      	} else {
      		return true;
   		}
   }
//-----------------------------------------------------------------------
   function sueldosDelete($condicion) {
      $strSQL = "DELETE FROM sueldos WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function sueldosRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM sueldos";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('sueldos',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('sueldos');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function unidad_tributariaInsert($fecha, $unidad_tributaria) {
   	  $id_unidad_tributaria = $this->nuevo_id("unidad_tributaria","id_unidad_tributaria");
   	  $fecha = d_ES_MYSQL($fecha); 
      $cols = get_commas(false, 'id_unidad_tributaria', 'fecha', 'unidad_tributaria');
      $vals = get_commas(true, '!!'.$id_unidad_tributaria, $fecha, '!!'.$unidad_tributaria);
      $strSQL = get_insert('unidad_tributaria',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function unidad_tributariaUpdate($fecha, $unidad_tributaria, $id_unidad_tributaria) {
      $fecha = d_ES_MYSQL($fecha);
      $strSQL = "UPDATE unidad_tributaria SET  fecha = '$fecha',  unidad_tributaria = $unidad_tributaria WHERE  id_unidad_tributaria = $id_unidad_tributaria";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function unidad_tributariaDelete($condicion) {
      $strSQL = "DELETE FROM unidad_tributaria WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function unidad_tributariaRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM unidad_tributaria";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('unidad_tributaria',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('unidad_tributaria');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function userInsert($username, $password, $email) {
   	  $id = $this->nuevo_id("user", "id");
   	  //$password = md5($password); 
      //$cols = get_commas(false, 'id', 'username', 'password', 'email');
      //$vals = get_commas(true, '!!'.$id, $username, $password, $email);
      //$strSQL = get_insert('user',$cols, $vals);
      //$result = mysql_query($strSQL);
      $strSQL = "call prc_nuevoUsuario($id,'$username','$email','$password')";
      $result = $this->consultagenerica($strSQL);
	    if($result[0]["errno"]==0){
			$_SESSION["nuevoUsuario"] = "Registro grabado exitosamente.";
		}elseif($result[0]["errno"]==1){
			$_SESSION["nuevoUsuario"] =  utf8_decode("El usuario ya está registrado");
		}elseif($result[0]["errno"]==2){
			$_SESSION["nuevoUsuario"] =  utf8_decode("El correo electrónico ya está registrado");
		}
      if($result[0]["errno"]!=0){
      	return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function userUpdate($username, $password, $email, $id) {
   	  $password = md5($password);
      $strSQL = "UPDATE user SET  username = '$username',  password = '$password',  email = '$email' WHERE  id = $id";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function userDelete($condicion) {
      $strSQL = "DELETE FROM user WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function userRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM user";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('user',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('user');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function tbl_bancosInsert($banco, $tipo_cuenta, $num_cuenta) {
   	  $idbanco = $this->nuevo_id("tbl_bancos", "idbanco");
      $cols = get_commas(false, 'idbanco', 'banco', 'tipo_cuenta', 'num_cuenta');
      $vals = get_commas(true, '!!'.$idbanco, $banco, $tipo_cuenta, $num_cuenta);
      $strSQL = get_insert('tbl_bancos',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }   
 //-----------------------------------------------------------------------
   function tbl_bancosUpdate($banco, $tipo_cuenta, $num_cuenta, $idbanco) {
         $strSQL = "UPDATE tbl_bancos SET  banco = '$banco',  tipo_cuenta = '$tipo_cuenta',  num_cuenta = '$num_cuenta' WHERE  idbanco = $idbanco";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function tbl_bancosDelete($condicion) {
      $strSQL = "DELETE FROM tbl_bancos WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function tbl_bancosRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM tbl_bancos";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('tbl_bancos',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('tbl_bancos');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function tbl_mov_bancariosInsert($idbanco, $id_tipo_movimiento, $n_comprobante,$fecha, $referencia, $descripcion, $monto) {
   	  $id_mov_bancario = $this->nuevo_id("tbl_mov_bancarios", "id_mov_bancario");
      $cols = get_commas(false, 'id_mov_bancario', 'idbanco', 'id_tipo_movimiento', 'n_comprobante','fecha', 'referencia', 'descripcion', 'monto');
      $vals = get_commas(true, '!!'.$id_mov_bancario, '!!'.$idbanco, '!!'.$id_tipo_movimiento, '!!'.$n_comprobante, $fecha, $referencia, $descripcion, '!!'.$monto);
      $strSQL = get_insert('tbl_mov_bancarios',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function tbl_mov_bancariosUpdate($idbanco, $id_tipo_movimiento, $fecha, $referencia, $descripcion, $monto, $id_mov_bancario) {
         $strSQL = "UPDATE tbl_mov_bancarios SET  idbanco = $idbanco,  id_tipo_movimiento = $id_tipo_movimiento,  fecha = '$fecha',  referencia = '$referencia',  descripcion = '$descripcion',  monto = $monto WHERE  id_mov_bancario = $id_mov_bancario";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function tbl_mov_bancariosDelete($condicion) {
      $strSQL = "DELETE FROM tbl_mov_bancarios WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function tbl_mov_bancariosRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM tbl_mov_bancarios";
        
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('tbl_mov_bancarios',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('tbl_mov_bancarios');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------
   function tbl_tipo_movimientosInsert($tipo_movimiento, $abreviatura, $operacion) {
   	  $id_tipo_movimiento = $this->nuevo_id("tbl_tipo_movimientos","id_tipo_movimiento");	
      $cols = get_commas(false, 'id_tipo_movimiento', 'tipo_movimiento', 'abreviatura', 'operacion');
      $vals = get_commas(true, '!!'.$id_tipo_movimiento, $tipo_movimiento, $abreviatura, $operacion);
      $strSQL = get_insert('tbl_tipo_movimientos',$cols, $vals);
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function tbl_tipo_movimientosUpdate($tipo_movimiento, $abreviatura, $operacion, $id_tipo_movimiento) {
         $strSQL = "UPDATE tbl_tipo_movimientos SET  tipo_movimiento = '$tipo_movimiento',  abreviatura = '$abreviatura',  operacion = '$operacion' WHERE  id_tipo_movimiento = $id_tipo_movimiento";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function tbl_tipo_movimientosDelete($condicion) {
      $strSQL = "DELETE FROM tbl_tipo_movimientos WHERE $condicion";
      $result = mysql_query($strSQL);
      if(!$result){
      return false;
      } else {
      return true;
   }
   }
//-----------------------------------------------------------------------
   function tbl_tipo_movimientosRecords($condicion='', $campoOrden = null, $orden='asc', $start=0, $limit=5) {
        $strSQL = "SELECT * FROM tbl_tipo_movimientos";
        if($condicion!=''){
            $strSQL = $strSQL . " WHERE $condicion";
        }
        if($campoOrden!=null){
            $strSQL = $strSQL. " ORDER BY $campoOrden $orden";
        }
        $strSQL = $strSQL . " LIMIT $start, $limit";
      $result = mysql_query($strSQL);
      if(!$result){
      return '';
      } else {
      $this->filas = $this->numRegistros('tbl_tipo_movimientos',$condicion);
      $this->campos = mysql_num_fields($result);
 if($this->filas!=0){
      $matriz = $this->atributos('tbl_tipo_movimientos');
      $iMatriz = count($matriz);  // Atributos de la entidad.
      $i=0;
      foreach($matriz as $v){ 
          $atributo[$i++] = $v['nombre']; 
      }
      $j=0; // Indice de la matriz.
      while($row = mysql_fetch_array($result)) { 
         for($i=0;$i<$iMatriz;$i++){
            $matrizAsoc[$j][$atributo[$i]] = $row[$atributo[$i]];
         }
      $j++;
      }
      return $matrizAsoc;
}else{
	return 0;
}
      }
   }
//-----------------------------------------------------------------------  
   
}

/*
$bd = new CLS_PARROT;
echo "Total de registro de la tabla ASIG_DEDU: ".$bd->numRegistros("asig_dedu");
$UT = 150;
$S = 5000;
$htm = "<br/><table border='1'>";
$i = 0;
$registros = $bd->asig_deduRecords("","","",0,20);
foreach($registros as $registro){
	extract($registro);
	$x = "\$diario[]=$formula";
	eval($x);
	$item[] = $descripcion;
	$htm .= "<tr><td>".$item[$i]."</td><td>".$diario[$i]."</td></tr>";
	$i++;
}
$htm .= "</table><br/>";
echo($htm);
echo("Fin de proceso");*/
/*
	$id = 2;
	$bd = new CLS_PARROT();
	$sql = "SELECT id_asig_dedu, tipo, descripcion FROM asig_dedu";
	$resultados = $bd->consultaGenerica($sql);
	foreach($resultados as $fila){
		$id_asig_dedu[] = $fila["id_asig_dedu"];
		$tipo[] = $fila["tipo"];
		$descripcion[] = $fila["descripcion"];
		$diario[] = "";
		$dias[] = "";
	}
	$sql = "SELECT asig_dedu.id_asig_dedu, dias, monto FROM asig_dedu INNER JOIN nomina 
				ON asig_dedu.id_asig_dedu = nomina.id_asig_dedu WHERE id_quincena = $id";
	$resultados = $bd->consultagenerica($sql);
	$n = count($tipo);
	foreach($resultados as $fila){
		$indice = array_search($fila["id_asig_dedu"], $id_asig_dedu);
		$diario[$indice] = $fila["monto"];
		$dias[$indice] = $fila["dias"];
	}			
	$indice = 0;
	foreach($id_asig_dedu as $i){
		echo("$id_asig_dedu[$indice]  $tipo[$indice]  $descripcion[$indice]  $diario[$indice]  $dias[$indice]<br/>");
		$indice++;
	}
	
	echo("fin del proceso");*/	
?>