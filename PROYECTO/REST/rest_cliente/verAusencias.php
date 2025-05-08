<?php
/**
 * ===============================
 *  verAusencias.php
 * ===============================
 *
 * Peticion que realiza: 
 * - Cargar las guardias pendientes del usuario actual (GET)
 * - Asignar una guardia a dicho usuario (POST)
 *
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   curl_conexion.php  Fichero de configuración de conexión mediante cURL, 
 *                                maneja peticiones, cabeceras y/o parámetros necesarios. 
  */

include("curl_conexion.php");
session_start();

// ===============================
// Cargar guardias pendientes
// ===============================
if (isset($_GET["cargar_guardias"])) {
   
    // parámetros 
    $params_get = [
        'document' => $_SESSION['document'],
        'accion' => 'verGuardias',              //accion diferenciada
    ];
    $url_get = URL . '?' . http_build_query($params_get);

    $response = curl_conexion($url_get, 'GET');
    $guardiasPen = json_decode($response, true);

    // Si la respuesta es válida, la guardamos en sesión
    if (is_array($guardiasPen) && isset($guardiasPen[0]) && is_array($guardiasPen[0])) {
        $_SESSION["guardiasPen"] = $guardiasPen;
    } else {
        // Si no hay datos válidos, eliminamos la variable de sesión y registramos el error
        unset($_SESSION["guardiasPen"]);
        error_log("Error al recibir las guardias: Respuesta inválida o no contiene sesiones válidas");
        header('Location: vistas/consultaAusencias.php');
        exit;
    }

    // Redirige a la vista correspondiente
    header('Location: vistas/consultaAusencias.php');
    exit;

// ===============================
// Asignar guardia a un docente
// ===============================
} elseif (isset($_POST["asignar"])) {

    
    $params = [
        'sesion' => $_POST["sesion_id"],
        'documentAus' => $_POST["document"],          // Documento del ausente
        'document' => $_SESSION["document"],          // Documento del docente que la cubrirá
        'cubierto' => 1,
        'accion' => 'asignarGuardia'                    // accion diferenciada
    ];

    $response = curl_conexion(URL, 'POST', json_encode($params), [
        'Content-Type: application/json'
    ]);

    $resp = json_decode($response, true);

    // Redirigir siempre, aunque el resultado sea error, para manejarlo desde la vista
    if (isset($resp["exito"]) && $resp["exito"]) {
        header('Location: vistas/consultaAusencias.php?success=1');
    } else {
        header('Location: vistas/consultaAusencias.php?success=0');
    }

    exit();
}
