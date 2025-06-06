<?php
/**
 * ==========================
 *  generarInforme.php
 * ==========================
 * 
 * Este script gestiona la generación de distintos tipos de informes (día, semana, mes, trimestre, etc.)
 * usando peticiones GET a través de cURL al servidor REST. Según el tipo de informe recibido por GET,
 * construye los parámetros necesarios y redirige a la vista correspondiente con los resultados.
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   curl_conexion.php  Fichero de configuración de conexión mediante cURL, 
 *                                maneja peticiones, cabeceras y/o parámetros necesarios.
 */

session_start();
include("curl_conexion.php");

// Obtenemos el tipo de informe desde la URL (GET)
$tipo = $_GET['tipoInforme'] ?? null;

//parámetros comunes para cualquier tipo de informe
$params = [
    'document' => $_SESSION['document'],
    'accion' => 'generarInforme',
    'tipo' => $tipo
];

// parámetros específicos según el tipo de informe
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
    case 'plazo':
        $params['plazoInicio'] = $_GET['fecha_inicio'] ?? '';
        $params['plazoFin'] = $_GET['fecha_fin'] ?? '';
        break;
    case 'trimestre':
        $params['trimestre'] = $_GET['trimestre'] ?? '';
        break;
    case 'docent':
        $params['docente'] = $_GET['docent'] ?? '';
        break;
    case 'curso':
        $params['ano'] = trim($_GET['anoCurso']) ?? ''; 
        break;
    default:
        echo "Error: tipo de informe no válido.";
        exit;
}

// Generamos la URL con los parámetros para la petición GET
$url = URL . '?' . http_build_query($params);
error_log("voy a hacer la petición a: " . $url);
$response = curl_conexion($url, 'GET', null, [ "Authorization: Bearer " . ($_SESSION['token'] ?? '') ]);
$data = json_decode($response, true);
error_log("vuelvo de la petición: " . print_r($data, true));

// Si hay resultados válidos, los guardamos en sesión y redirigimos a la vista de resultados
if (!empty($data) && is_array($data)) {
    $_SESSION['resultado_informe'] = $data;
    $_SESSION['tipo_informe'] = $tipo;
    header('Location: vistas/verResultados.php');
    exit;
} else {
    // Si no hay datos, mostramos alerta y redirigimos a la vista de selección de informe
    $_SESSION['alert_message'] = 'No se encontraron resultados para la consulta.';
    header('Location: vistas/verInformes.php');
}
