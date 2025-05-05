<?php
session_start();
include("curl_conexion.php");
$nombreReceptor = $_POST['nombreReceptor'] ?? '';
$params = [
    "accion" => "EditarMensaje",
    "docentEmisor" => $_POST['idMensaje'],
    "fecha" => $_POST['fecha'],
    "hora" => $_POST['hora'],
    "mOriginal" =>  $_POST['mensajeOriginal'],
    "mEditado" => $_POST['mensajeEditado']
];
$respuesta = curl_conexion(URL, 'PUT',$params);
$resp = json_decode($respuesta,true);

if ($resp["exito"]) {
    header('Location: vistas/chat.php?profesor=' . urlencode($nombreReceptor));
} else{
    header("Location: vistas/chat.php?error=Error al editar");
}
