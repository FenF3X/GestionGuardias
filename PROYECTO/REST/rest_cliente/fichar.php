<?php
/**
 * =====================
 *  fichar.php
 * =====================
 * 
 * Este script gestiona el fichaje de entrada y salida del usuario.
 * Recibe datos desde un formulario y realiza peticiones POST al servidor 
 * usando cURL, según el botón pulsado (entrada o salida).
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   curl_conexion.php  Fichero de configuración de conexión mediante cURL, 
 *                                maneja peticiones, cabeceras y/o parámetros necesarios.
 */

session_start(); 
include("curl_conexion.php"); 

// =======================
// Fichaje de entrada
// =======================
if (isset($_POST['fentrada'])) {

    // Datos necesarios para el fichaje de entrada
    $document = $_SESSION['document'];              // Documento del usuario 
    $fecha = date('Y-m-d');                         // Fecha actual en formato YYYY-MM-DD
    $hora_entrada = date('H:i:s');                  // Hora actual

    // parametros
    $params = [
        'document' => $document,
        'fecha' => $fecha,
        'hora_entrada' => $hora_entrada,
        'accion' => 'ficharEntrada'                // Acción diferenciada
    ];

    $response = curl_conexion(URL, 'POST', $params); 
    $resp = json_decode($response, true);  

    // Se guarda mensaje de éxito o error en la sesión para mostrarlo después
    if (isset($resp['exito'])) {
        $_SESSION['mensaje'] = ['type' => 'success', 'text' => $resp['exito']]; 
    } elseif (isset($resp['error'])) {
        $_SESSION['mensaje'] = ['type' => 'danger', 'text' => $resp['error']]; 
    }

    // Redirige al dashboard tras fichar
    header("location: vistas/dashboard.php");
    exit();
}

// =======================
// Fichaje de salida
// =======================
if (isset($_POST['fsalida'])) {

    $document = $_SESSION['document'];
    $fecha = date('Y-m-d');
    $hora_salida = date('H:i:s');

    // Parámetros 
    $params = [
        'document' => $document,
        'fecha' => $fecha,
        'hora_salida' => $hora_salida,
        'accion' => 'ficharSalida'
    ];

    $response = curl_conexion(URL, 'POST', $params); 
    $resp = json_decode($response, true);  

    // Mensaje para mostrar en la vista
    if (isset($resp['exito'])) {
        $_SESSION['mensaje'] = ['type' => 'success', 'text' => $resp['exito']]; 
    } elseif (isset($resp['error'])) {
        $_SESSION['mensaje'] = ['type' => 'danger', 'text' => $resp['error']]; 
    }

    header("location: vistas/dashboard.php");
    exit();
}
?>
