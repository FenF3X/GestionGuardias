<?php
/**
 * ===============================
 *  resultadoAsistencia.php
 * ===============================
 * 
 * Consulta de ausencia de un profesor determinado en una fecha o un mes determinado
 * o bien consulta de ausencia de todos los profesores en una fecha o un mes determinado
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   curl_conexion.php  Fichero de configuración de conexión mediante cURL, 
 *                                maneja peticiones, cabeceras y/o parámetros necesarios. 
 */

session_start();

include("curl_conexion.php");

$tipoConsulta = $_POST['tipoConsulta'] ?? '';
$documento = $_POST['document'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$mes = $_POST['mes'] ?? '';

$params = [
    'accion' => 'consultarAsistencia'       //accion diferenciada
];

// Añadir filtros
if (!empty($documento)) {
    $params['document'] = $documento;
}

if ($tipoConsulta === 'fecha' && !empty($fecha)) {
    $params['fecha'] = $fecha;
} elseif ($tipoConsulta === 'mes' && !empty($mes)) {
    $params['mes'] = $mes;
}

$response = curl_conexion(URL, 'POST', $params);
$datosAsistencia = json_decode($response, true);

// Guardar resultados en sesión
if (is_array($datosAsistencia) && !empty($datosAsistencia)) {
    $_SESSION['resultado_asistencia'] = $datosAsistencia;
} else {
    $_SESSION['resultado_asistencia'] = [];
}
header('Location: vistas/verAsistencia.php?resultado=1');
exit;
