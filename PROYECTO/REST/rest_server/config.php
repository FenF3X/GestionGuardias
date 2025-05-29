<?php
/**
 * ==============================
 *  conexion_bd.php
 * ==============================
 * 
 * Este archivo define los parámetros de conexión a la base de datos
 * y proporciona una función `conexion_bd()` que permite ejecutar consultas
 * SQL (SELECT, INSERT, UPDATE, DELETE) de forma controlada y segura.
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 */

// Constantes de conexión a la base de datos
DEFINE("SERVIDOR", "localhost");    // Dirección del servidor MySQL
DEFINE("USER", "root");             // Usuario de acceso
DEFINE("PASSWD", "");               // Contraseña
DEFINE("BASE_DATOS", "guardias");   // Nombre de la base de datos
DEFINE("JWT_SECRET_KEY", "AsistGuardSave25");         
/**
 * Función que realiza una conexión a la base de datos, ejecuta una consulta SQL
 * y devuelve el resultado según el tipo de operación (SELECT, INSERT, etc.).
 * 
 * @param string $serv   Servidor de base de datos
 * @param string $user   Usuario
 * @param string $passwd Contraseña
 * @param string $bd     Nombre de la base de datos
 * @param string $sql    Consulta SQL a ejecutar
 * 
 * @return array|bool    Resultado de la consulta o false en caso de error
 */
function conexion_bd($serv, $user, $passwd, $bd, $sql)
{  
    // Conexión a la base de datos
    $con_bd = @mysqli_connect($serv, $user, $passwd, $bd);
    
    if (!$con_bd) {
        error_log("Error al conectar: " . mysqli_connect_error());
        return false;
    }

    // Establece el conjunto de caracteres a UTF-8
    $con_bd->set_charset('utf8');

    // Ejecuta la consulta
    $res = mysqli_query($con_bd, $sql);

    if ($res === false) {
        // Si falla, registra el error en un archivo de log
        error_log("MySQL error en [$sql]: " . mysqli_error($con_bd) . "\n", 3, "errores.log");
        mysqli_close($con_bd);
        return false;
    }

    // Detecta el tipo de operación (SELECT, INSERT, etc.)
    $operacion = strtoupper(strtok($sql, " ")); // Extrae la primera palabra

    switch ($operacion) {
        case "SELECT":
            // Si hay resultados, los devuelve como array; si no, array vacío
            if (mysqli_num_rows($res) >= 1) {
                $res_array = mysqli_fetch_all($res, MYSQLI_NUM);
            } else {
                $res_array = [];
            }
            break;

        case "INSERT":
        case "UPDATE":
        case "DELETE":
            // Devuelve true si se afectó al menos una fila
            $res_array = (mysqli_affected_rows($con_bd) > 0);
            break;

        default:
            // Cualquier otro tipo de operación no está soportado
            $res_array = false;
    }

    // Cierra la conexión antes de retornar
    mysqli_close($con_bd);
    return $res_array;
}
