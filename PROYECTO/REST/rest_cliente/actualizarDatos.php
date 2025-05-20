<?php
session_start();
include("curl_conexion.php");
if(isset($_POST['actualizar'])){

$nombre = htmlspecialchars($_POST['new_nombre']);
$rol = htmlspecialchars($_POST['new_rol']);
$contrasena = htmlspecialchars($_POST['new_password']);
$document = htmlspecialchars($_POST['document']);
$params = array(
    'nombre' => $nombre,
    'rol' => $rol,
    'contrasena' => $contrasena,
    'document' => $document,
    'accion' => 'actualizarDatos',
);

$response = curl_conexion(URL, 'PUT', $params);
$resp = json_decode($response, true);

if(isset($resp['exito']) && $resp['exito']){
    $_SESSION['nombre'] = $nombre;
    $_SESSION['rol'] = $rol;
}else{
    echo "<script>alert('Error al actualizar los datos.');</script>";
}
header("Location: vistas/datospersonales.php?document=".$document);
}