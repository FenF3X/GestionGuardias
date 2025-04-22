<?php

/* 
 *Función de conexión mediante cURL con servidores REST
 * Toma la $URL y el método GET, POTS, PUT o DELETE
 * y opcionalmente parámetros para el paso tipo POST
 */
DEFINE("URL", "http://localhost/GestionGuardias/PROYECTO/REST/rest_server/index.php");
function curl_conexion($url, $metodo = 'GET', $datos = null, $headers = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Convertir método a mayúsculas por si acaso
    $metodo = strtoupper($metodo);

    if ($metodo === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);

        // Solo enviamos datos si hay contenido
        if (!empty($datos)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
        }
    }

    // Solo aplicar headers si son un array válido
    if (is_array($headers) && !empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $respuesta = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log("Error cURL: " . curl_error($ch));
        return false;
    }

    curl_close($ch);
    return $respuesta;
}
