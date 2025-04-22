<?php
session_start();

include("curl_conexion.php");

$tipoConsulta = $_POST['tipoConsulta'] ?? '';
$documento = $_POST['document'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$mes = $_POST['mes'] ?? '';

$params = [
    'accion' => 'consultarAsistencia'
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
