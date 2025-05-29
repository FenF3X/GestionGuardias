<?php
/**
 * =====================
 *  editarMensaje.php
 * =====================
 * 
 * Realiza una petición para editar un mensaje enviado desde la vista chat.php.
 * Envía los datos del mensaje (ID, fecha, hora, contenido original y editado) mediante
 * una petición PUT al servidor REST utilizando cURL.
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   curl_conexion.php  Fichero de configuración de conexión mediante cURL, 
 *                                maneja peticiones, cabeceras y/o parámetros necesarios.
 */

session_start(); 

include("curl_conexion.php"); 

// Nombre del profesor receptor (usado en la redirección)
$nombreReceptor = $_POST['nombreReceptor'] ?? ''; // Si no viene definido, queda como cadena vacía

//parámetros necesarios para editar el mensaje
$params = [
    "accion" => "EditarMensaje",                    // Acción diferenciada
    "docentEmisor" => $_POST['idMensaje'],          // ID del mensaje 
    "fecha" => $_POST['fecha'],                     // Fecha del mensaje
    "hora" => $_POST['hora'],                       // Hora del mensaje
    "mOriginal" => $_POST['mensajeOriginal'],       // Contenido original
    "mEditado" => $_POST['mensajeEditado']          // Nuevo contenido editado
];
$respuesta = curl_conexion(URL, 'PUT', $params, [ "Authorization: Bearer " . ($_SESSION['token'] ?? '') ]);

// Se decodifica la respuesta JSON
$resp = json_decode($respuesta, true);

// Según el resultado, redirige al chat con éxito o con error
if ($resp["exito"]) {
    // Redirige al chat del receptor si fue exitoso
    header('Location: vistas/chat.php?profesor=' . urlencode($nombreReceptor));
} else {
    // Si hubo error, redirige con mensaje de error
    header("Location: vistas/chat.php?error=Error al editar");
}
