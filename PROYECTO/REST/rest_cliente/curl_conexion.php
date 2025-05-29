<?php
/**
 * =====================
 *  curl_conexion.php
 * =====================
 * 
 * Fichero de configuración genérico que maneja todas las peticiones cURL.
 * Se indica a qué fichero del servidor apunta (mediante la constante URL),
 * qué tipo de petición se realiza (GET, POST, PUT, DELETE...), y 
 * opcionalmente, los datos o cabeceras que se necesitan enviar.
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT  
 */

// URL base del servidor al que se harán las peticiones
DEFINE("URL", "http://localhost/GestionGuardias/PROYECTO/REST/rest_server/index.php");



/**
 * Función que gestiona una conexión cURL con diferentes tipos de métodos.
 * 
 * @param string $url     URL completa del recurso al que se hace la petición
 * @param string $metodo  Tipo de petición: GET, POST, PUT, DELETE...
 * @param mixed  $datos   Datos opcionales para enviar (array o string)
 * @param array  $headers Cabeceras HTTP personalizadas (opcional)
 * 
 * @return string|false Respuesta del servidor o false si hay error
 */
function curl_conexion($url, $metodo = 'GET', $datos = null, $headers = null) {
    // Inicializa la sesión cURL con la URL
    $ch = curl_init($url);

    // Configura cURL para que retorne el resultado como string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Normaliza el método a mayúsculas
    $metodo = strtoupper($metodo);

    // Si el método es PUT, DELETE, PATCH u otro similar
    if (in_array($metodo, ['PUT','DELETE','PATCH','OPTIONS'])) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo); // Se define el método personalizado

        // Si hay datos que enviar
        if (!empty($datos)) {
            if (is_array($datos)) {
                // Se convierte a JSON si es un array
                $payload = json_encode($datos);
                $headers[] = 'Content-Type: application/json';
            } else {
                // Si ya es string (raw, urlencoded, etc), se usa tal cual
                $payload = $datos;
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload); // Se agregan los datos
        }
    }
    // Si el método es POST
    elseif ($metodo === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);

        if (!empty($datos)) {
            if (is_array($datos)) {
                // Se convierte a formato x-www-form-urlencoded
                $payload = http_build_query($datos);
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            } else {
                $payload = $datos;
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload); // Se agregan los datos
        }
    }
    // Si es GET, no se necesita configurar nada más (los datos irían en la URL)

    // Si se pasan cabeceras, se añaden a la petición
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    // Ejecuta la petición y guarda la respuesta
    $respuesta = curl_exec($ch);

    // Si hay un error, se guarda en el log y se devuelve false
    if (curl_errno($ch)) {
        error_log("Error cURL ({$metodo}): " . curl_error($ch));
        curl_close($ch);
        return false;
    }

    // Cierra la sesión cURL y devuelve la respuesta
    curl_close($ch);
    return $respuesta;
}
