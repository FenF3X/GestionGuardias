<?php
DEFINE ("SERVIDOR", "localhost");
DEFINE ("USER", "root");
DEFINE ("PASSWD", "");
DEFINE ("BASE_DATOS", "guardias");
function conexion_bd($serv, $user, $passwd, $bd, $sql){  
    $con_bd = @mysqli_connect($serv, $user, $passwd, $bd);
    if (!$con_bd) {
        error_log("Error al conectar: " . mysqli_connect_error());
        return false;
    }
    $con_bd->set_charset('utf8');

    $res = mysqli_query($con_bd, $sql);
    if ($res === false) {
        // Aquí puedes leer el error de MySQL porque $con_bd sigue vivo
        error_log("MySQL error en [$sql]: " . mysqli_error($con_bd) . "\n", 3, "errores.log");
        mysqli_close($con_bd);
        return false;
    }

    // Tu lógica de SELECT / INSERT / UPDATE...
    $operacion = strtoupper(strtok($sql, " "));
    switch($operacion){
        case "SELECT":
            if(mysqli_num_rows($res) >= 1){
                $res_array = mysqli_fetch_all($res, MYSQLI_NUM);
            } else {
                $res_array = []; // o como prefieras indicar “no hay filas”
            }
            break;
        case "INSERT":
        case "UPDATE":
        case "DELETE":
            $res_array = (mysqli_affected_rows($con_bd) > 0);
            break;
        default:
            $res_array = false;
    }

    mysqli_close($con_bd);
    return $res_array;
}
