<?php
DEFINE("URL", "http://localhost/GestionGuardias/PROYECTO/REST/rest_server/index.php");

function curl_conexion($url, $metodo = 'GET', $datos = null, $headers = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Normalizamos el método
    $metodo = strtoupper($metodo);

    // Para PUT, DELETE, PATCH, etc.
    if (in_array($metodo, ['PUT','DELETE','PATCH','OPTIONS'])) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);

        if (!empty($datos)) {
            // Si pasas un array, lo enviamos como JSON
            if (is_array($datos)) {
                $payload = json_encode($datos);
                $headers[] = 'Content-Type: application/json';
            } else {
                // Si ya es un string (por ejemplo urlencoded o raw), lo enviamos tal cual
                $payload = $datos;
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
    }
    // Para POST
    elseif ($metodo === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($datos)) {
            if (is_array($datos)) {
                // POST tradicional url-encoded
                $payload = http_build_query($datos);
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            } else {
                $payload = $datos;
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
    }
    // GET no necesita configuración extra salvo headers

    // Si nos pasan headers, los seteamos
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $respuesta = curl_exec($ch);
    if (curl_errno($ch)) {
        error_log("Error cURL ({$metodo}): " . curl_error($ch));
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    return $respuesta;
}
