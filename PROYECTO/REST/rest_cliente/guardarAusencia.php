<?php
include("curl_conexion.php");
session_start();

// Verificar si se enviaron las sesiones seleccionadas
if (isset($_POST['sesiones']) && !empty($_POST['sesiones'])) {

    $sesionesSeleccionadas = $_POST['sesiones'];

    // Comprobar si hay sesiones JSON válidas
    $jsonValido = true;
    foreach ($sesionesSeleccionadas as $sesionJson) {
        if (json_decode($sesionJson, true) === null) {
            $jsonValido = false;
            break;
        }
    }

    if (!$jsonValido) {
        echo "Error: Sesiones mal formateadas.";
        exit;
    }

    // Recoger si la ausencia es de jornada completa
    $jornadaC = isset($_POST["jornada_completa"]) ? true : false;

    // Preparar los datos para enviar como JSON
    $params = [
        'document' => $_SESSION["documentAusente"],
        'fecha' => $_SESSION["fechaSinFormat"],
        'justificada' => $_SESSION["justificada"],
        'jornada_completa' => $jornadaC,
        'sesiones' => $sesionesSeleccionadas,
        'accion' => 'registrarAusencia'
    ];

    // Enviar JSON con headers personalizados
    $response = curl_conexion(URL, 'POST', json_encode($params), [
        "Content-Type: application/json"
    ]);

    // Decodificar la respuesta
    $estado = json_decode($response, true);

if ($response === false || $response === "") {
    error_log("Respuesta vacía del backend.");
} elseif (isset($estado["exito"])) {
    $_SESSION['registro_exitoso'] = true;
} elseif (isset($estado["error"])) {
    $_SESSION['registro_exitoso'] = false;
    error_log("Error del servidor: " . $estado["error"]);
} else {
    error_log("Respuesta inválida del backend: " . $response);
}


    // Redirigir al dashboard
    header('Location: vistas/dashboard.php');
    exit;

} else {
    echo "No se seleccionaron sesiones.";
}
