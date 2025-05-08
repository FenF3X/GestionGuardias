<?php
include("curl_conexion.php");
session_start();
/**
 * =====================
 *  consultaProfes.php
 * =====================
 * 
 * Petición genérica que se basa en recojer el listado de nombres de los profesores para los inputs
 * selects. Estos incluyen su value como su documento identificativo. Petición POST diferenciada por
 * el valor de la clave 'accion'
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   Fichero de configuración de conexion mediante cURL, maneja peticiones, cabeceras y/o 
 * parametros necesarios    
 *
 */

 /**
  * @var array $params -- Array de parámetros para realizar la petición
  * @var array $response -- Almacena la respuesta de la petición codificado en JSON
  * @var array $profesores -- Respuesta decodificada 
  */
$params = [
    'accion' => 'consultaProfes'
];
$response = curl_conexion(URL, 'POST', $params); 

$profesores = json_decode($response, true);


/**
 * @var array|SESSION $profesores -- En el servidor si falla se establece clave error y su valor
 * como mensaje de error, si no esta definido la clave error se crea esta sesion y se almacenan los valores
 */
if (isset($profesores['error'])) {
    $_SESSION['mensaje'] = ['type' => 'danger', 'text' => $profesores['error']];
} else {
    $_SESSION['profesores'] = $profesores;
}

header('location: vistas/registroAusencias.php');