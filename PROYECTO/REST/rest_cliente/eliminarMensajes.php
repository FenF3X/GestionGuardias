<?php
/**
 * =====================
 *  borrarMensaje.php
 * =====================
 * 
 * Realiza una petición de borrado de uno o varios mensajes seleccionados
 * desde la vista chat.php. 
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   curl_conexion.php  Fichero de configuración de conexión mediante cURL, 
 *                                maneja peticiones, cabeceras y/o parámetros necesarios. */

session_start(); 

include("curl_conexion.php"); 

// Se obtiene el nombre del receptor (para redirigir después del borrado)
$nombreReceptor = $_POST['nombreReceptor'] ?? '';

// parametros
$params = [
    "accion" => "BorrarMensaje",                // Acción diferenciada
    "mensajes" => $_POST["seleccionados"]       // Array de IDs de los mensajes a borrar
];


$respuesta = curl_conexion(URL, 'DELETE', $params);

// Se decodifica la respuesta JSON
$resp = json_decode($respuesta, true);

// Redirección en función del éxito o fallo de la operación
if ($resp["exito"]) {
    // Si fue exitoso, vuelve al chat del profesor receptor
    header('Location: vistas/chat.php?profesor=' . urlencode($nombreReceptor));
} else {
    // Si hubo error, muestra mensaje de error
    header("Location: vistas/chat.php?error=Error al borrar");
}
