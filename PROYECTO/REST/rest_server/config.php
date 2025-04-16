<?php
DEFINE ("SERVIDOR", "localhost");
DEFINE ("USER", "root");
DEFINE ("PASSWD", "");
DEFINE ("BASE_DATOS", "guardias");
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "\C:\xampp\php\logs\php_error.log");
function conexion_bd($serv, $user, $passwd, $bd, $sql){  
    $con_bd = @mysqli_connect($serv, $user, $passwd, $bd);
    $acentos = $con_bd->query("SET NAMES 'utf8'");
    if ($con_bd) {
       if($res = mysqli_query($con_bd, $sql)) {
          $operacion = explode(' ', $sql);
          switch($operacion[0]){ // Tiene la operación SQL: SELECT, INSERT, ...
              case "SELECT":
                  if(mysqli_num_rows($res) >= 1){ // Ha encontrado las frutas
                      $res_array = mysqli_fetch_all($res, MYSQLI_NUM);
                  }
                  else {
                      $res_array = "Error no encontrado en la BD";
                  }
                  break;
              case "INSERT":
              case "UPDATE":
              case "DELETE":
                  if(mysqli_affected_rows($con_bd) > 0){
                      $res_array=true;
                  }
                  else {
                      $res_array=false;
                  }
                  break;
          }
          $cierre_bd = @mysqli_close($con_bd); 
       }
    }
    else {
      $res_array = "Error al conetactar con la BD";
    }
    return($res_array);
  }