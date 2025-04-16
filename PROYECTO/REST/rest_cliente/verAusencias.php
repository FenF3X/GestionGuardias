<?php
include("curl_conexion.php");
session_start();

if (isset($_GET["cargar_guardias"])) {
   
   $params_get = [
        'document' => $_SESSION['document'],
       'accion' => 'verGuardias',
   ];
   $url_get = URL . '?' . http_build_query($params_get);

   // Realizar la petición GET
   $response = curl_conexion($url_get, 'GET'); // Pasamos la URL completa a curl_conexion

   // Decodificar la respuesta JSON
   $guardiasPen = json_decode($response, true);

   // Verificar si la respuesta contiene datos válidos
   if (is_array($guardiasPen) && isset($guardiasPen[0]) && is_array($guardiasPen[0])) {
    $_SESSION["guardiasPen"] = $guardiasPen;
} else {
    unset($_SESSION["guardiasPen"]);
    error_log("Error al recibir las guardias: Respuesta inválida o no contiene sesiones válidas");
    header('Location: vistas/consultaAusencias.php');
    exit;
}



   // Redirigir al usuario
   header('Location: vistas/consultaAusencias.php');
   
}elseif (isset($_POST["asignar"])) {
    $params = [
        'sesion' => $_POST["sesion_id"],
        'documentAus' => $_POST["document"],
        'document' => $_SESSION["document"],
        'cubierto' => 1,
        'accion' => 'asignarGuardia'
    ];

    // Convertimos los datos a JSON y añadimos el header
    $response = curl_conexion(URL, 'POST', json_encode($params), [
        'Content-Type: application/json'
    ]);

    $resp = json_decode($response, true);

    if (isset($resp["exito"]) && $resp["exito"]) {
        header('Location: vistas/consultaAusencias.php?success=1');
    } else {
        header('Location: vistas/consultaAusencias.php?success=1');
    }

    exit();
}


