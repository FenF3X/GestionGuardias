<?php
/**
 * ==============================
 *  obtenerSesiones.php
 * ==============================
 * 
 * Datos necesarios para registrar una ausencia.
 * Recoge la fecha y el documento del profesor ausente desde el formulario de registroAusencias,
 * convierte la fecha al formato deseado, determina si la ausencia es justificada
 * y obtiene las sesiones del día correspondiente mediante cURL.
 * 
 * Los datos se guardan en variables de sesión para usarlos en la vista `horarioAusente.php`.
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   curl_conexion.php  Fichero de configuración de conexión mediante cURL, 
 *                                maneja peticiones, cabeceras y/o parámetros necesarios. 
*/

session_start();
include("curl_conexion.php");

$justificada = 0;

if (isset($_POST["fecha"]) && isset($_POST["document"])) {

    
    $document = $_POST["document"];
    $_SESSION["documentAusente"] = $document;

    $_SESSION["fechaSinFormat"] = $_POST["fecha"]; 
    $_SESSION["fechaAusencia"] = date('d-m-Y', strtotime($_POST["fecha"])); 

    // Determinar si la ausencia es justificada en base al campo "motivo"
    if (!empty($_POST["motivo"])) {
        $justificada = 1;
    }
    $_SESSION["justificada"] = $justificada;
    // Traducir el día de la semana a formato de letra
    $dayMap = [
        'Monday'    => 'L',
        'Tuesday'   => 'M',
        'Wednesday' => 'X',
        'Thursday'  => 'J',
        'Friday'    => 'V',
        'Saturday'  => 'S',
        'Sunday'    => 'D'
    ];
    $dia = $dayMap[date('l', strtotime($_POST["fecha"]))];

    // parámetros
    $params = [
        'document' => $document,
        'dia' => $dia,
        'accion' => 'verSesiones'       //accion diferenciada
    ];

    // Realiza la petición al servidor REST
    $response = curl_conexion(URL, 'POST', $params, [ "Authorization: Bearer " . ($_SESSION['token'] ?? '') ]); 
    $sesiones = json_decode($response, true);

    // Guardar las sesiones si no hay error, si no, guardar un array vacío
    $_SESSION["sesiones_profesor"] = 
        !empty($sesiones) && is_array($sesiones) && (!isset($sesiones["error"]) || !$sesiones["error"])
        ? $sesiones
        : [];

    // Redirigir a la vista para mostrar el horario del profesor ausente
    header("Location: vistas/horarioAusente.php");
}
