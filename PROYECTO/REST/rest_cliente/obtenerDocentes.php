<?php
include("curl_conexion.php");
session_start();


// Parámetros que se pasan al servidor
$params = [
    'accion' => 'consultaProfes',
];

// Realizar la solicitud cURL al servidor
$response = curl_conexion(URL, 'POST', $params); // Usamos GET para la consulta

// Decodificar la respuesta JSON
$profesores = json_decode($response, true);

// Verificar si hay errores en la respuesta
if (isset($profesores['error'])) {
    // Si hay un error, guardar el mensaje de error en la sesión y redirigir
    $_SESSION['mensaje'] = ['type' => 'danger', 'text' => $profesores['error']];
} else {
    // Si no hay errores, guardar los profesores en la sesión
    $_SESSION['profesores'] = $profesores;
}

// Redirigir al formulario para registrar ausencias
header("Location: vistas/registroAusencias.php");
exit();
?>
