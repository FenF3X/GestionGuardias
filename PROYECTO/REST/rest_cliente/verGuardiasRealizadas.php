<?php
include("curl_conexion.php");
session_start();
if (isset($_POST["cargar_guardias"])) {
    $params = [
        'document' => $_SESSION['document'],
        'accion' => 'historialGuardias'
    ];

    if (!empty($_POST['fecha'])) {
        $params['fecha'] = $_POST['fecha'];
    }

    if (!empty($_POST['hora'])) {
        $params['hora'] = trim($_POST['hora']);
    }

    $respuesta = curl_conexion(URL, 'POST', $params);
    $historial = json_decode($respuesta, TRUE);

    if (is_array($historial)) {
        if (isset($historial["error"])) {
            $_SESSION['historial'] = "";
            $_SESSION['error'] = $historial["error"];
        } else {
            $_SESSION['historial'] = $historial;
            unset($_SESSION['error']);
        }
    }

    header('Location: vistas/guardiasRealizadas.php?historial=1&auto=0');
    exit;
}


