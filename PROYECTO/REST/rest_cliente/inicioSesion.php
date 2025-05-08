<?php
include("curl_conexion.php");
session_start();

if (isset($_POST["validar"])) {
    if (isset($_POST["document"]) && isset($_POST["password"])) {
        $document = filter_input(INPUT_POST, "document", FILTER_SANITIZE_SPECIAL_CHARS);
        $passwordInput = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    
$limpio = preg_replace('/[^0-9]/', '', $passwordInput);

if (strlen($limpio) === 8) {
    if (substr($limpio, 0, 4) > 1900) {
        // YYYYMMDD
        $anio = substr($limpio, 0, 4);
        $mes  = substr($limpio, 4, 2);
        $dia  = substr($limpio, 6, 2);
    } else {
        // DDMMYYYY
        $dia  = substr($limpio, 0, 2);
        $mes  = substr($limpio, 2, 2);
        $anio = substr($limpio, 4, 4);
    }
    $password = "$dia/$mes/$anio";
} else {
    // 3. Si tiene separadores tipo "-", "/" lo intentamos parsear
    $password = $passwordInput;
    $formatos = ['d-m-Y', 'Y-m-d', 'd/m/Y', 'Y/m/d'];
    foreach ($formatos as $formato) {
        $fecha = DateTime::createFromFormat($formato, $passwordInput);
        if ($fecha && $fecha->format($formato) === $passwordInput) {
            $password = $fecha->format('d/m/Y');
            break;
        }
    }
}
        
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

                if (isset($sesiones) && !empty($sesiones) && is_array($sesiones)) {
                    $_SESSION["sesiones_hoy"] = $sesiones;
                } else {
                    unset($_SESSION["sesiones_hoy"]);
                    $_SESSION["alertSinSesiones"] = "No hay sesiones";
                }

                $paramsProf = ["accion" => "consultaProfes"];
                $respuesta = curl_conexion(URL, "POST", $paramsProf);
                $profesores = json_decode($respuesta, true);

                /**
                 * @var array|SESSION $profesores -- En el servidor si falla se establece clave error y su valor
                 * como mensaje de error, si no esta definido la clave error se crea esta sesion y se almacenan los valores
                 */
                if (isset($profesores['error'])) {
                    $_SESSION['mensaje'] = ['type' => 'danger', 'text' => $profesores['error']];
                } else {
                    $_SESSION['profesores'] = $profesores;
                }

                header("Location: vistas/dashboard.php");
                exit;
            } else {
                $_SESSION["error_login"] = $resp["error"];
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION["error_login"] = "No validado";
            header("Location: login.php");
            exit;
        }
    }
}
