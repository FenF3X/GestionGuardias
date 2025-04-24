<?php
session_start();
include("curl_conexion.php");
$justificada = false;

if (isset($_POST["fecha"]) && isset($_POST["document"])) {
    $document = $_POST["document"];
    $_SESSION["documentAusente"] = $document;
    $_SESSION["fechaSinFormat"] = $_POST["fecha"];
    $_SESSION["fechaAusencia"] = date('d-m-Y', strtotime($_POST["fecha"]));
    if (!empty($_POST["motivo"])) {
        $justificada = true;
    }
    $_SESSION["justificada"] = $justificada;
    $dayMap = [
        'Monday'    => 'L',
        'Tuesday'   => 'M',
        'Wednesday' => 'X',
        'Thursday'  => 'J',
        'Friday'    => 'V',
        'Saturday'  => 'S',
        'Sunday'    => 'D'
    ];

    $dia = $dayMap[date('l', strtotime($_POST["fecha"]))];
    $params = [
        'document' => $document,
        'dia' => $dia,
        'accion' => 'verSesiones'
    ];

    $response = curl_conexion(URL, 'POST', $params); 
    $sesiones = json_decode($response, true);

    // Verificar si la clave "error" existe antes de acceder a ella
    $_SESSION["sesiones_profesor"] = !empty($sesiones) && is_array($sesiones) && (!isset($sesiones["error"]) || !$sesiones["error"]) ? $sesiones : [];
    header("Location: vistas/horarioAusente.php");
}
?>
