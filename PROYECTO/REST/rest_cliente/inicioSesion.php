<?php
/**
 * ============================
 *  inicioSesion.php
 * ============================
 * 
 * Procesa el login del usuario.
 * Valida el formato de la contraseña (usando fecha de nacimiento), la sanitiza,
 * y hace una petición POST al servidor REST para comprobar las credenciales.
 * Si es exitoso, guarda en sesión los datos del usuario, sus sesiones del día
 * y la lista de profesores. Redirige al dashboard. Si falla, vuelve al login.
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   curl_conexion.php  Fichero de configuración de conexión mediante cURL, 
 *                                maneja peticiones, cabeceras y/o parámetros necesarios. 
 */

include("curl_conexion.php");
session_start();

if (isset($_POST["validar"])) {
    if (isset($_POST["document"]) && isset($_POST["password"])) {

        // Sanear entradas
        $document = filter_input(INPUT_POST, "document", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

        // // Limpiamos la contraseña para quedarnos solo con números
        // $limpio = preg_replace('/[^0-9]/', '', $passwordInput);

        // // Si tiene 8 dígitos, detectamos si es YYYYMMDD o DDMMYYYY
        // if (strlen($limpio) === 8) {
        //     if (substr($limpio, 0, 4) > 1900) {
        //         // Formato: YYYYMMDD
        //         $anio = substr($limpio, 0, 4);
        //         $mes  = substr($limpio, 4, 2);
        //         $dia  = substr($limpio, 6, 2);
        //     } else {
        //         // Formato: DDMMYYYY
        //         $dia  = substr($limpio, 0, 2);
        //         $mes  = substr($limpio, 2, 2);
        //         $anio = substr($limpio, 4, 4);
        //     }
        //     $password = "$dia/$mes/$anio";
        // } else {
        //     // Si contiene separadores, intentamos parsear varios formatos comunes
        //     $password = $passwordInput;
        //     $formatos = ['d-m-Y', 'Y-m-d', 'd/m/Y', 'Y/m/d'];
        //     foreach ($formatos as $formato) {
        //         $fecha = DateTime::createFromFormat($formato, $passwordInput);
        //         if ($fecha && $fecha->format($formato) === $passwordInput) {
        //             $password = $fecha->format('d/m/Y');
        //             break;
        //         }
        //     }
        // }

        $params = [
            'document' => $document,
            'password' => $password,
            'accion' => 'InicioSesion',         //accion diferenciada
        ];
        $response = curl_conexion(URL, "POST", $params);
        $resp = json_decode($response, true);

        // Si la validación fue exitosa
        if ($resp && isset($resp["loggeado"]) && $resp["loggeado"] === true) {
            $_SESSION["nombre"] = $resp["nombre"];
            $_SESSION["document"] = $resp["document"];
            $_SESSION["rol"] = $resp["rol"];

            // Obtener el día actual (en formato letra)
            $dayMap = [
                'Monday'    => 'L',
                'Tuesday'   => 'M',
                'Wednesday' => 'X',
                'Thursday'  => 'J',
                'Friday'    => 'V',
                'Saturday'  => 'S',
                'Sunday'    => 'D'
            ];
            $dia = $dayMap[date('l')];

            // Consulta de sesiones del día
            $params_get = [
                'document' => $_SESSION["document"],
                'dia' => $dia,
                'accion' => 'verHorario',       //accion diferenciada
            ];
            $url_get = URL . '?' . http_build_query($params_get);
            $resp_get = curl_conexion($url_get, "GET");
            $sesiones = json_decode($resp_get, true);

            // Guardar sesiones en la sesión o mostrar alerta
            if (isset($sesiones) && !empty($sesiones) && is_array($sesiones)) {
                $_SESSION["sesiones_hoy"] = $sesiones;
            } else {
                unset($_SESSION["sesiones_hoy"]);
                $_SESSION["alertSinSesiones"] = "No hay sesiones";
            }

            // Obtener lista de profesores para futuras funciones
            $paramsProf = ["accion" => "consultaProfes"];
            $respuesta = curl_conexion(URL, "POST", $paramsProf);
            $profesores = json_decode($respuesta, true);
            /**
             * Si el backend devuelve error, se guarda en la sesión como mensaje;
             * si no, se guarda la lista de profesores para usar en otras vistas.
             */
            if (isset($profesores['error'])) {
                $_SESSION['mensaje'] = ['type' => 'danger', 'text' => $profesores['error']];
            } else {
                $_SESSION['profesores'] = $profesores;
            }

            // Redirigir al dashboard tras login correcto
            header("Location: vistas/dashboard.php");
            exit;

        } else {
            // Login incorrecto: mensaje de error
            $_SESSION["error_login"] = $resp["error"] ?? "No validado";
            header("Location: login.php");
            exit;
        }
    }
}
