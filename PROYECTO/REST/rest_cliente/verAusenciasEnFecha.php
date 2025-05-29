<?php
include("curl_conexion.php");
session_start();

if (isset($_GET["cargar_guardias_porFecha"])) {
    $_SESSION["fechaFalta"] = $_GET['fecha'];  // Guardamos la fecha

    $params_get = [
        'document' => $_SESSION['document'],
        'fecha' => $_GET['fecha'],
        'accion' => 'verGuardiasPorFecha',
    ];
    $url_get = URL . '?' . http_build_query($params_get);

    $response = curl_conexion($url_get, 'GET',null,[ "Authorization: Bearer " . ($_SESSION['token'] ?? '') ]);
    $guardiasFecha = json_decode($response, true);

    $_SESSION["busquedaGuardiasRealizada"] = true; 

    if (is_array($guardiasFecha) && !empty($guardiasFecha)) {
        $_SESSION["guardiasFecha"] = $guardiasFecha;
    } else {
        $_SESSION["guardiasFecha"] = []; // Guardamos array vacío
        error_log("Guardias vacías o respuesta inválida: " . print_r($response, true));
    }

    header('Location: vistas/consultaAusenciaEnfecha.php');
    exit();
}
