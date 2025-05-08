<?php
/**
 * ==================================
 *  verGuardiasRealizadas.php
 * ==================================
 * 
 * historial de guardias realizadas por el docente 
 * actualmente en sesión. Puede filtrar por fecha y/o hora si se proporcionan.
 * Los resultados se obtienen mediante una petición POST al servidor REST y 
 * se guardan en sesión para mostrarlos en la vista `guardiasRealizadas.php`.
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   curl_conexion.php  Fichero de configuración de conexión mediante cURL, 
 *                                maneja peticiones, cabeceras y/o parámetros necesarios. 
 */

include("curl_conexion.php");
session_start();

if (isset($_POST["cargar_guardias"])) {

    // Parámetros 
    $params = [
        'document' => $_SESSION['document'],       // Documento del docente actual
        'accion' => 'historialGuardias'            // Acción diferenciada
    ];

    if (!empty($_POST['fecha'])) {
        $params['fecha'] = $_POST['fecha'];        // Filtro por fecha
    }

    if (!empty($_POST['hora'])) {
        $params['hora'] = trim($_POST['hora']);    // Filtro por hora (sin espacios)
    }

    $respuesta = curl_conexion(URL, 'POST', $params);
    $historial = json_decode($respuesta, true);

    if (is_array($historial)) {
        if (isset($historial["error"])) {
            // Si hay error, lo guardamos para mostrarlo en la vista
            $_SESSION['historial'] = "";
            $_SESSION['error'] = $historial["error"];
        } else {
            // Si la respuesta es válida, se guarda el historial
            $_SESSION['historial'] = $historial;
            unset($_SESSION['error']);
        }
    }

    // Redirigimos a la vista de guardias realizadas
    header('Location: vistas/guardiasRealizadas.php?historial=1&auto=0');
    exit;
}
