<?php
session_start();
include("curl_conexion.php");

$tipo = $_GET['tipoInforme'] ?? null;
$params = ['document'=>$_SESSION['document'], 'accion' => 'generarInforme', 'tipo' => $tipo];

switch ($tipo) {
  case 'dia':
    $params['fecha'] = $_GET['dia'] ?? '';
    break;
  case 'semana':
    $params['semana'] = $_GET['semana'] ?? '';
    break;
  case 'mes':
    $params['mes'] = $_GET['mes'] ?? '';
    break;
  case 'trimestre':
    $params['trimestre'] = $_GET['trimestre'] ?? '';
    break;
  case 'docent':
    $params['docente'] = $_GET['docent'] ?? '';
    break;
  case 'curs':
    $params['curso'] = '2025';
    break;
  default:
    echo "Error: tipo de informe no válido.";
    exit;
}
$url = URL . '?' . http_build_query($params);
$response = curl_conexion($url, 'GET');
$data = json_decode($response, true);

// Guardamos en sesión y redirigimos a la vista
if (!empty($data) && is_array($data)) {
  $_SESSION['resultado_informe'] = $data;
  $_SESSION['tipo_informe'] = $tipo;
  header('Location: vistas/verResultados.php');
  exit;
} else {
  $_SESSION['alert_message'] = 'No se encontraron resultados para la consulta.';
  header('Location: vistas/verInformes.php');

}
