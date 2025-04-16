<?php
include("curl_conexion.php");
session_start();

if (isset($_POST["validar"])) {
    if (isset($_POST["document"]) && isset($_POST["password"])) {
        $document = filter_input(INPUT_POST, "document", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    
        $params = [
            'document' => $document,
            'password' => $password,
            'accion' => 'InicioSesion',
        ];

        $response = curl_conexion(URL, "POST", $params);
        $resp = json_decode($response, true);

        if ($resp && isset($resp["loggeado"])) {
            if ($resp["loggeado"] === true) {

                $_SESSION["nombre"] = $resp["nombre"];
                $_SESSION["document"] = $resp["document"];
                $_SESSION["rol"] = $resp["rol"];

                // Calculamos la letra correspondiente al día actual
                $dayMap = [
                    'Monday'    => 'L',
                    'Tuesday'   => 'M',
                    'Wednesday' => 'X',
                    'Thursday'  => 'J',
                    'Friday'    => 'V',
                    'Saturday'  => 'S',
                    'Sunday'    => 'D'
                ];
                // date('l') devuelve el día en inglés (e.g., "Monday")
                $dia = $dayMap[date('l')];

                // Enviamos también el parámetro 'dia' para que se ejecute el branch correcto en el servidor
                $params_get = [
                    'document' => $_SESSION["document"],
                    'dia' => $dia,
                    'accion' => 'verHorario',
                ];
                $url_get = URL . '?' . http_build_query($params_get);
                $resp_get = curl_conexion($url_get, "GET");
                $sesiones = json_decode($resp_get, true);

                $_SESSION["sesiones_hoy"] = is_array($sesiones) ? $sesiones : [];

                header("Location: vistas/dashboard.php");
                exit;
            } else {
                $_SESSION["error_login"] = $resp["error"];
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION["error_login"] = "Respuesta inesperada del servidor.";
            header("Location: login.php");
            exit;
        }
    }
}
