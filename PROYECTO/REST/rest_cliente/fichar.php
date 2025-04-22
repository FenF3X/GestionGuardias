<?php
session_start();
include("curl_conexion.php"); // Asegúrate de que tienes esta función configurada

// Fichaje de entrada
if (isset($_POST['fentrada'])) {
    // Datos para la petición POST
    $document = $_SESSION['document'];
    $fecha = date('Y-m-d');
    $hora_entrada = date('H:i:s');

    // Parámetros para la API RESTful
    $params = [
        'document' => $document,
        'fecha' => $fecha,
        'hora_entrada' => $hora_entrada,
        'accion' => 'ficharEntrada'
    ];

    // Realizar la petición cURL al servidor
    $response = curl_conexion(URL, 'POST', $params); 
    $resp = json_decode($response, true);  // Decodificar la respuesta

    // Comprobar si la respuesta es exitosa
    if (isset($resp['exito'])) {
        $_SESSION['mensaje'] = ['type' => 'success', 'text' => $resp['exito']]; // Almacenar el mensaje de éxito en la sesión
    } elseif (isset($resp['error'])) {
        $_SESSION['mensaje'] = ['type' => 'danger', 'text' => $resp['error']]; // Almacenar el mensaje de error en la sesión
    }

    // Redirigir a la página de dashboard
    header("location: vistas/dashboard.php");
    exit();
}

// Fichaje de salida
if (isset($_POST['fsalida'])) {
    // Datos para la petición POST
    $document = $_SESSION['document'];
    $fecha = date('Y-m-d');
    $hora_salida = date('H:i:s');

    // Parámetros para la API RESTful
    $params = [
        'document' => $document,
        'fecha' => $fecha,
        'hora_salida' => $hora_salida,
        'accion' => 'ficharSalida'
    ];

    // Realizar la petición cURL al servidor
    $response = curl_conexion(URL, 'POST', $params); 
    $resp = json_decode($response, true);  // Decodificar la respuesta

    // Comprobar si la respuesta es exitosa
    if (isset($resp['exito'])) {
        $_SESSION['mensaje'] = ['type' => 'success', 'text' => $resp['exito']]; // Almacenar el mensaje de éxito en la sesión
    } elseif (isset($resp['error'])) {
        $_SESSION['mensaje'] = ['type' => 'danger', 'text' => $resp['error']]; // Almacenar el mensaje de error en la sesión
    }

    // Redirigir a la página de dashboard
    header("location: vistas/dashboard.php");
    exit();
}
?>
