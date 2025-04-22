<?php
include("curl_conexion.php");
session_start();
$params = [
    'accion' => 'consultaProfes'
];
$response = curl_conexion(URL, 'POST', $params); // Realizamos la consulta usando POST

// Decodificar la respuesta JSON
$profesores = json_decode($response, true);

// Verificar si hay errores en la respuesta
if (isset($profesores['error'])) {
    $_SESSION['mensaje'] = ['type' => 'danger', 'text' => $profesores['error']];
} else {
    $_SESSION['profesores'] = $profesores;
}



// Obtener los datos de los profesores desde la sesión
$profesores = $_SESSION['profesores'] ?? [];
header('location: vistas/verAsistencia.php');