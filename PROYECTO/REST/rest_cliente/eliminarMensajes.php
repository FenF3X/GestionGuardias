<?php
session_start();
include("curl_conexion.php");

$nombreReceptor = $_POST['nombreReceptor'] ?? '';
$params = [
    "accion" => "BorrarMensaje",
    "mensajes" => $_POST["seleccionados"]
];

$respuesta = curl_conexion(URL, 'DELETE',$params);
$resp = json_decode($respuesta,true);


if ($resp["exito"]) {
    header('Location: vistas/chat.php?profesor=' . urlencode($nombreReceptor));
} else{
    header("Location: vistas/chat.php?error=Error al borrar");
}
