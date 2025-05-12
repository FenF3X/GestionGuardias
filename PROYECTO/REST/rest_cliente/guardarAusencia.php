<?php
ini_set("error_log", __DIR__ . "/pruebas.txt");
/**
 * =============================
 *  guardarAusencia.php
 * =============================
 * 
 * Este script recibe desde un formulario las sesiones seleccionadas para una ausencia 
 * (parcial o de jornada completa), valida los datos, los convierte a JSON y los envía 
 * mediante una petición POST al servidor REST para registrar la ausencia del docente.
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   curl_conexion.php  Fichero de configuración de conexión mediante cURL, 
 *                                maneja peticiones, cabeceras y/o parámetros necesarios.
 */

include("curl_conexion.php");
session_start();

// ============================
// Validación inicial de datos
// ============================
if (isset($_POST['sesiones']) && !empty($_POST['sesiones'])) {

    $sesionesSeleccionadas = $_POST['sesiones'];

    // Validar que todas las sesiones están bien formateadas en JSON
    $jsonValido = true;
    foreach ($sesionesSeleccionadas as $sesionJson) {
        if (json_decode($sesionJson, true) === null) {
            $jsonValido = false;
            break;
        }
    }

    // Si hay alguna sesión mal formateada, se aborta
    if (!$jsonValido) {
        echo "Error: Sesiones mal formateadas.";
        exit;
    }

    // ============================
    // Preparación de parámetros
    // ============================

    $jornadaC = isset($_POST["jornada_completa"]) ? true : false;

    // Se preparan todos los datos necesarios para la petición
    $params = [
        'document' => $_SESSION["documentAusente"],       // Documento del docente ausente
        'fecha' => $_SESSION["fechaSinFormat"],           // Fecha sin formatear
        'justificada' => $_SESSION["justificada"],        // Si está justificada o no
        'jornada_completa' => $jornadaC,                   // Booleano de jornada completa
        'sesiones' => $sesionesSeleccionadas,              // Array de sesiones en JSON
        'accion' => 'registrarAusencia'                    // Acción diferenciada
    ];

    $response = curl_conexion(URL, 'POST', json_encode($params), [
        "Content-Type: application/json"
    ]);

    $estado = json_decode($response, true);

   
    if ($response === false || $response === "") {
        error_log("Respuesta vacía del backend.");
    } elseif (isset($estado["exito"])) {
        $_SESSION['registro_exitoso'] = true;
    } elseif (isset($estado["error"])) {
        $_SESSION['registro_exitoso'] = false;
        error_log("Error del servidor: " . $estado["error"]);
    } else {
        error_log("Respuesta inválida del backend: " . $estado);
    }

    // Redirigir siempre al dashboard tras la petición
    header('Location: vistas/dashboard.php');
    exit;

} else {
    // Si no se seleccionaron sesiones, se notifica
    echo "No se seleccionaron sesiones.";
}
