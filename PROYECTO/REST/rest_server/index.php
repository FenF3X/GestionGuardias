
<?php

/**
 * =========================
 *  index.php (REST Server)
 * =========================
 * 
 * servidor REST.
 * Interpreta las peticiones HTTP entrantes,
 * determina el método (GET, POST, PUT, DELETE, etc.),
 * y segun la accion maneja de forma diferente la peticion
 * 
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @includes   config.php  Archivo con la función de conexión a la BD.
 */

include("config.php"); 

// Obtener el método de la petición (GET, POST, PUT, DELETE...)
$metodo = $_SERVER['REQUEST_METHOD'];

// Obtener el recurso solicitado (ruta completa)
$recurso = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

    // =====================================
    // PETICIONES **GET**
    // =====================================
if ($metodo === 'GET') {
    // =====================================
    // Verificación del parámetro obligatorio
    // =====================================    
    
    $document = filter_input(INPUT_GET, "document", FILTER_SANITIZE_SPECIAL_CHARS);
    if (!$document) {
        echo json_encode(["error" => "documento requerido"]);
        exit;
    }
    // ==========================================
    //  acción diferenciada de la misma petición
    // ==========================================
    $accion = filter_input(INPUT_GET,"accion", FILTER_SANITIZE_SPECIAL_CHARS);
    /**
     * Acción: consultaSesiones
     * 
     * @description Devuelve todas las sesiones horarias posibles para cualquier grupo.
     * 
     * @return JSON  Array de sesiones con su orden y rango horario.
     */
    if($accion === "consultaSesiones"){
        $sql = "SELECT DISTINCT sessio_orde, 
            CONCAT('Sesion ', sessio_orde, ': ', hora_desde, ' - ', hora_fins) AS horario_completo 
            FROM sessions_horari 
            ORDER BY sessio_orde ASC";

        $result = conexion_bd(SERVIDOR,USER,PASSWD,BASE_DATOS,$sql);

        if (is_array($result)) {
            if (!empty($result)) {
                echo json_encode($result);
            } else{
                echo json_encode(["error" => "Sesiones vacias"]);
            }
        } 
        else{
                echo json_encode(["error" => "Error en la consulta"]);
        }
    }
    /**
     * Acción: verHorario
     * 
     * @description Devuelve el horario del docente para un día concreto.
     * 
     * @param string document  DNI del docente
     * @param string dia       Día de la semana (L, M, X, J, V)
     * @return JSON            Lista de sesiones del día
     */
    elseif ($accion === "verHorario") {
        
        $dia = filter_input(INPUT_GET, "dia", FILTER_SANITIZE_SPECIAL_CHARS);
        if ($dia) {
            
            $sql = "SELECT 
                hg.dia_setmana,
                hg.hora_desde,
                hg.hora_fins,
                c.nom_cas AS asignatura,
                hg.grup AS grupo,
                hg.aula AS aula,
                hg.sessio_orde AS sesion
                FROM horari_grup hg
                LEFT JOIN continguts c ON c.codi = hg.contingut
                WHERE hg.docent = '$document'
                AND hg.dia_setmana = '$dia'
                ORDER BY FIELD(hg.dia_setmana, 'L', 'M', 'X', 'J', 'V'), hg.hora_desde
            ";

            $resultado_horario = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);

            if (is_array($resultado_horario)) {
                echo json_encode($resultado_horario);
            } else {
                echo json_encode(["error" => "No se encontraron datos de horario"]);
            }
        } else {
            echo json_encode(["error" => "Día no proporcionado"]);
        }
    } 
    /**
     * Acción: verGuardias
     * 
     * @description Devuelve las ausencias (guardias) registradas en la fecha actual.
     * 
     * @param string document  DNI del docente
     * @return JSON            Lista de ausencias del día actual
     */
    elseif ($accion === "verGuardias") {
        $fecha = date('Y-m-d');  

        $sql = "SELECT 
            sesion, aula, grupo, asignatura, 
            document,nombreProfe,cubierto 
            FROM ausencias WHERE fecha = '$fecha'
        ";
        $respuesta = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
        if (is_array($respuesta)) {
            echo json_encode($respuesta);  
        } else {
            echo json_encode(["error" => "No se encontraron guardias para hoy"]);
        }
    }
    /**
     * Acción: verGuardiasPorFecha
     * 
     * @description Devuelve las ausencias (guardias) registradas en la fecha seleccionada.
     * 
     * @param string document  DNI del docente
     * @param string fecha     Fecha seleccionada en formato Y-m-d
     * @return JSON            Lista de ausencias de ese día
     */  
    elseif ($accion === "verGuardiasPorFecha") {
        $fecha = filter_input(INPUT_GET, "fecha" , FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
        if (!$fecha) {
            echo json_encode(["error" => "Fecha no proporcionada"]);
            exit;
        }

        $sql = "SELECT 
            sesion, aula, grupo, 
            asignatura, document,
            nombreProfe,fecha 
            FROM ausencias WHERE fecha = '$fecha'
        ";
        $respuesta = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);

        if (is_array($respuesta)) {
            echo json_encode($respuesta);  
        } else {
            echo json_encode(["error" => "No se encontraron guardias para " . $fecha]);
        }
    }
    /**
     * Acción: generarInforme
     * 
     * @description Genera un informe filtrado de guardias registradas.
     * 
     * @param string tipo      Tipo de filtro (dia, semana, mes, trimestre, docent, curs)
     * @param mixed  fecha     Fecha concreta, semana (YYYY-MM-DD), mes (YYYY-MM), etc.
     * @return JSON            Datos del informe según el tipo solicitado
     */
    elseif ($accion === "generarInforme") {
$tipos = isset($_GET['tipo']) && is_array($_GET['tipo']) ? $_GET['tipo'] : [];

$tipos_filtrados = array_map(function($valor) {
    return filter_var($valor, FILTER_SANITIZE_SPECIAL_CHARS);
}, $tipos);

foreach ($tipos_filtrados as $tipo) {
        switch ($tipo) {
            case 'dia':
        $fecha = filter_input(INPUT_GET, "fecha" , FILTER_SANITIZE_SPECIAL_CHARS);

                $sql = "SELECT fecha,nombreProfe,nombreProfeReempl,
                    aula, grupo, asignatura, sesion_orden,dia_semana, 
                    CONCAT(hora_inicio, '--', hora_fin),total_guardias 
                    FROM  registro_guardias WHERE  fecha = '$fecha'; 
                ";
            break;
            case 'semana':  
                $diaSemana = filter_input(INPUT_GET, "semana" , FILTER_SANITIZE_SPECIAL_CHARS);
                $inicioSemana = date('Y-m-d', strtotime('monday this week', strtotime($diaSemana)));
                $finSemana = date('Y-m-d', strtotime('sunday this week', strtotime($diaSemana)));

                $sql = "SELECT fecha,nombreProfe,nombreProfeReempl, 
                    aula, grupo, asignatura, sesion_orden,dia_semana, 
                    CONCAT(hora_inicio, '--', hora_fin),total_guardias
                    FROM registro_guardias 
                    WHERE fecha BETWEEN '$inicioSemana' AND '$finSemana'
                ";
            break;
            case 'mes':
                $mes = filter_input(INPUT_GET, "mes" , FILTER_SANITIZE_SPECIAL_CHARS);
                $sql = "SELECT fecha,nombreProfe,nombreProfeReempl, 
                    aula, grupo, asignatura, sesion_orden,dia_semana, 
                    CONCAT(hora_inicio, '--', hora_fin),total_guardias
                    FROM registro_guardias 
                    WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mes'
                ";
            break;
            case 'plazo':
                $inicio = filter_input(INPUT_GET, "plazoInicio" , FILTER_SANITIZE_SPECIAL_CHARS);
                $fin = filter_input(INPUT_GET, "plazoFin" , FILTER_SANITIZE_SPECIAL_CHARS);

                $sql = "SELECT fecha,nombreProfe, nombreProfeReempl,
                aula, grupo, asignatura, sesion_orden,dia_semana,
                CONCAT(hora_inicio, '--', hora_fin),total_guardias 
                FROM registro_guardias
                WHERE fecha BETWEEN '$inicio' AND '$fin'
            ";
            break;
            case 'trimestre':
                $trimestre = filter_input(INPUT_GET, "trimestre" , FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

                if ($trimestre == 1) {
                    $inicio = "2024-09-09";
                    $fin = "2024-12-22";
                } elseif ($trimestre == 2) {
                    $inicio = "2025-01-08";
                    $fin = "2025-04-14";
                } else {
                    $inicio = "2025-04-29";
                    $fin = "2025-06-21";
                }

                $sql = "SELECT fecha,nombreProfe, nombreProfeReempl,
                    aula, grupo, asignatura, sesion_orden,dia_semana,
                    CONCAT(hora_inicio, '--', hora_fin),total_guardias 
                    FROM registro_guardias
                    WHERE fecha BETWEEN '$inicio' AND '$fin'
                ";
            break;
            case 'docent':
                $docente = filter_input(INPUT_GET, "docente" , FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

                $sql = "SELECT fecha,nombreProfe,nombreProfeReempl,
                    aula, grupo, asignatura, sesion_orden,dia_semana,
                    CONCAT(hora_inicio, '--', hora_fin),total_guardias
                    FROM registro_guardias 
                    WHERE docente_guardia = '$docente'
                ";
            break;
            case 'curso':
                $ano = filter_input(INPUT_GET, "ano" , FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
                $inicio = $ano."-09-01";
                $fin = ($ano + 1) ."-07-1";
                $sql = "SELECT fecha,nombreProfe,nombreProfeReempl,
                    aula, grupo, asignatura, sesion_orden,dia_semana, 
                    CONCAT(hora_inicio, '--', hora_fin),total_guardias
                    FROM registro_guardias 
                    WHERE fecha BETWEEN '$inicio' AND '$fin'
                ";
                
            break;
            default:
                error_log("tipo no valido");
            break;
        }
        $result = conexion_bd(SERVIDOR,USER,PASSWD,BASE_DATOS,$sql);
        if (is_array($result)) {
            echo json_encode($result);
        }else{
            error_log("error en la consulta");
        }
    }
    }
    /**
    * Acción: InicioSesion
    * 
    * @description Verifica las credenciales del usuario (documento + contraseña).
    * Si son válidas, devuelve nombre, documento y rol. Registra el acceso.
    * 
    * @param string document  DNI del usuario
    * @param string password  Fecha de nacimiento o contraseña codificada
    * @return JSON            { loggeado: bool, nombre: string, document: string, rol: string }
    */
    if ($accion === "InicioSesion") {
        $document = $_GET['document'] ?? null;         
        $password = $_GET['password'] ?? null;          

        if ($document && $password) {             
            $sql = "SELECT * FROM usuarios WHERE document = '$document'";             
            $resultado = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);              

            if (is_array($resultado)) {                 
                $sqlNom = "SELECT 
                    nombre
                    FROM usuarios WHERE document = '$document'
                ";                 
                $resultadoNom = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlNom);                  

                if (is_array($resultadoNom)) {                     
                    $nombre_profesor = $resultadoNom[0][0];                  
                } else {                     
                    $nombre_profesor = "Desconocido";                 
                }                  

                if (password_verify($password, $resultado[0][2])) {                     
                    $fechaHora = date('d-m-Y H:i:s');                     
                    $linea = "$fechaHora | Éxito | DNI: $document | Profesor: $nombre_profesor | Log In(Entrada)\n";

                    $archivo = fopen("registroAccesos.txt", "a");                     
                    if ($archivo) {                         
                        fwrite($archivo, $linea);                         
                        fclose($archivo);                     
                    } else {                         
                        error_log("Error al abrir el archivo");                     
                    }                     
                    echo json_encode(["loggeado" => true, 
                                        "nombre" => $nombre_profesor, 
                                        "document" => $resultado[0][1], 
                                        "rol" => $resultado[0][3]
                                    ]);                 
                } 
                else {                     
                    $fechaHora = date('d-m-Y H:i:s');                     
                    $linea = "$fechaHora | Fallo | DNI: $document | Profesor: $nombre_profesor | Log In(Entrada)\n";                     
                    
                    $archivo = fopen("registroAccesos.txt", "a");                     
                    if ($archivo) {                         
                        fwrite($archivo, $linea);                         
                        fclose($archivo);                     
                    } else {                         
                        error_log("Error al abrir el archivo");                     
                    }                     
                    echo json_encode(["loggeado" => false, 
                                        "error" => "Contraseña errónea"
                                    ]);                 
                }              
            } else {                 
                echo json_encode(["loggeado" => false, "error" => "Usuario inexistente"]);             
            }          
        } else {             
            echo json_encode(["loggeado" => false, "error" => "Faltan datos del usuario"]);         
        }
    }
}
    // =====================================
    // PETICIONES **POST**
    // =====================================
elseif ($metodo === 'POST') {
    
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['accion'])) {
        $accion = filter_var($data['accion'], FILTER_SANITIZE_SPECIAL_CHARS);
    } else {
        $accion = filter_input(INPUT_POST, "accion", FILTER_SANITIZE_SPECIAL_CHARS);
    }
    
    /**
    * Acción: ficharEntrada / ficharSalida
    * 
    * @description Registra la entrada o salida del docente en el sistema.
    * 
    * @param string document       DNI del docente
    * @param string hora_entrada   Hora de entrada (solo para entrada)
    * @param string hora_salida    Hora de salida (solo para salida)
    * @return JSON                 { exito: string } o { error: string }
    */
    if ($accion === "ficharEntrada" || $accion === "ficharSalida") {

        $document = filter_input(INPUT_POST, "document", FILTER_SANITIZE_SPECIAL_CHARS);
        $fecha = date('Y-m-d');  
        $hora_entrada = filter_input(INPUT_POST, "hora_entrada", FILTER_SANITIZE_SPECIAL_CHARS);
        $hora_salida = filter_input(INPUT_POST, "hora_salida", FILTER_SANITIZE_SPECIAL_CHARS);

        if ($document) {
            if ($accion === "ficharEntrada") {
                // Comprobar si ya fichó la entrada hoy
                $sqlCheckEntrada = "SELECT * FROM registro_jornada 
                    WHERE document = '$document' 
                    AND fecha = '$fecha' 
                    AND hora_entrada IS NOT NULL
                ";
                $resultadoEntrada = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlCheckEntrada);

                if (is_array($resultadoEntrada) && count($resultadoEntrada) > 0) {
                    // Si ya existe un registro de entrada
                    echo json_encode(["error" => "Ya has fichado entrada hoy"]);
                } else {
                    // Fichaje de entrada
                    if ($hora_entrada) {

                        $sqlNombre = "SELECT CONCAT(nom, ' ', cognom1, ' ', cognom2) 
                            AS nombre FROM docent 
                            WHERE document = '$document'
                        ";

                        $resultadoNombre = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlNombre);
                        $nombre = is_array($resultadoNombre) ? $resultadoNombre[0][0] : 'Desconocido';

                        $sql = "INSERT INTO registro_jornada 
                            (document, fecha, hora_entrada, hora_salida, nombre) 
                            VALUES 
                            ('$document', '$fecha', '$hora_entrada', NULL, '$nombre')
                        ";
                        $resultFicharEnt = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);

                        if ($resultFicharEnt > 0) {
                            echo json_encode(["exito" => "Entrada registrada correctamente"]);
                        } else {
                            echo json_encode(["error" => "Error al registrar la entrada"]);
                        }
                    } else {
                        echo json_encode(["error" => "Falta la hora de entrada"]);
                    }
                }
            } elseif ($accion === "ficharSalida") {
                // Comprobar si existe un registro de entrada para poder registrar la salida
                $sqlCheckSalida = "SELECT * FROM registro_jornada 
                    WHERE document = '$document'
                    AND fecha = '$fecha' 
                    AND hora_entrada IS NOT NULL 
                    AND hora_salida IS NULL
                ";
                $resultadoSalida = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlCheckSalida);

                if (is_array($resultadoSalida) && count($resultadoSalida) > 0) {
                    // Si existe un registro de entrada, actualizamos con la salida
                    if ($hora_salida) {
                        $sqlUpdateSalida = "UPDATE registro_jornada 
                            SET hora_salida = '$hora_salida' 
                            WHERE document = '$document' 
                            AND fecha = '$fecha' 
                            AND hora_salida IS NULL
                        ";
                        $resultFicharSal = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlUpdateSalida);

                        if ($resultFicharSal > 0) {
                            echo json_encode(["exito" => "Salida registrada correctamente"]);
                        } else {
                            echo json_encode(["error" => "Error al registrar la salida"]);
                        }
                    } else {
                        echo json_encode(["error" => "Falta la hora de salida"]);
                    }
                } else {
                    echo json_encode(["error" => "No se ha fichado entrada hoy o ya se registró la salida"]);
                }
            } else {
                echo json_encode(["error" => "Acción no válida"]);
            }
        } else {
            echo json_encode(["error" => "Faltan datos del usuario"]);
        }
    }
    /**
    * Acción: consultaProfes
    * 
    * @description Devuelve la lista de todos los profesores registrados.
    * 
    * @return JSON  Lista de profesores con su documento y nombre completo.
    */
    elseif ($accion === "consultaProfes") {

        $sql = "SELECT document, nombre FROM usuarios";

        $resultado = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
    
        if (is_array($resultado)) {
            echo json_encode($resultado);  
        } else {
            echo json_encode(["error" => "No se encontraron docentes"]);  
        }
    }
    /**
    * Acción: verSesiones
    * 
    * @description Devuelve el horario del docente para un día concreto.
    * 
    * @param string document  DNI del profesor
    * @param string dia       Día de la semana en formato corto (L, M, X, etc.)
    * @return JSON            Lista de sesiones del día
    */

    elseif ($accion === "verSesiones") {
        $document = filter_input(INPUT_POST, "document", FILTER_SANITIZE_SPECIAL_CHARS);
        $dia = filter_input(INPUT_POST, "dia", FILTER_SANITIZE_SPECIAL_CHARS);

        $sql = "SELECT 
            hg.dia_setmana,
            hg.hora_desde,
            hg.hora_fins,
            c.nom_cas AS asignatura,
            hg.grup AS grupo,
            hg.aula AS aula,
            hg.sessio_orde AS sesion
        FROM horari_grup hg
        LEFT JOIN continguts c ON c.codi = hg.contingut
        WHERE hg.docent = '$document'
        AND hg.dia_setmana = '$dia'
        ORDER BY FIELD(hg.dia_setmana, 'L', 'M', 'X', 'J', 'V'), hg.hora_desde";

        $resultado_horario = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);

        if (is_array($resultado_horario)) {
            echo json_encode($resultado_horario);
        } else {
            echo json_encode(["error" => "No se encontraron datos de horario"]);
        }
    }
    /**
    * Acción: registrarAusencia
    * 
    * @description Registra una ausencia parcial o completa del docente con sus sesiones afectadas.
    * 
    * @param string fecha               Fecha de la ausencia
    * @param string document            DNI del profesor
    * @param bool   justificada         Si está justificada
    * @param bool   jornada_completa    Si es jornada completa
    * @param array  sesiones            Lista de sesiones codificadas en JSON
    * @return JSON                      { exito: string } o { error: string }
    */
    elseif ($accion === "registrarAusencia") {
    
       $data = json_decode(file_get_contents("php://input"), true);

        $fecha             = isset($data['fecha']) ? filter_var($data['fecha'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $document          = isset($data['document']) ? filter_var($data['document'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $justificada       = isset($data['justificada']) ? filter_var($data['justificada'], FILTER_VALIDATE_BOOLEAN) : null;
        $jornada_completa  = isset($data['jornada_completa']) ? filter_var($data['jornada_completa'], FILTER_VALIDATE_BOOLEAN) : null;
        $sesionesSeleccionadas = $data['sesiones'] ?? [];
        $resultadoIn = true;
        $sqlNombre = "SELECT CONCAT(nom, ' ', cognom1, ' ', cognom2) 
            AS nombreProfe 
            FROM docent 
            WHERE document = '$document'
        ";
        $resultadoNombre = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlNombre);
        $nombreProfe = is_array($resultadoNombre) ? $resultadoNombre[0][0] : 'Desconocido';
    
        if (!empty($sesionesSeleccionadas)) {
            foreach ($sesionesSeleccionadas as $sesionJson) {
                $sesionSinFiltro = json_decode($sesionJson, true);
                $sesion = array_map(fn($valor) => filter_var($valor, FILTER_SANITIZE_SPECIAL_CHARS), $sesionSinFiltro);
                error_log("Sesión procesada: " . print_r($sesion,true));
                if (!is_array($sesion) || count($sesion) < 7) {
                    $resultadoIn = false;
                    break;
                }
    
                $dia = $sesion[0];
                $hora_inicio = $sesion[1];
                $hora_fin = $sesion[2];
                $asignatura = $sesion[3];
                $grupo = $sesion[4];
                $aula = $sesion[5];
                $sesion_orden = $sesion[6];
    
                $sql = "INSERT INTO ausencias (
                    hora_inicio, hora_fin, dia, aula, grupo, asignatura, sesion,
                    document, document_cubierto, nombreProfe, justificada, jornada_completa, fecha
                    ) VALUES (
                    '$hora_inicio', '$hora_fin', '$dia', '$aula', 
                    '$grupo', '$asignatura', '$sesion_orden',
                    '$document',NULL ,'$nombreProfe', '$justificada', 
                    '$jornada_completa', '$fecha'
                    )
                ";
    
                $resultadoConsulta = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
    
                if ($resultadoConsulta === false) {
                    $resultadoIn = false;
                    error_log("Error al ejecutar la consulta SQL: " . $sql);
                    break;
                }
            }
            if ($resultadoIn) {
                
                echo json_encode(["exito" => "Entrada registrada correctamente"]);
            } else {
                error_log("Error en la inserción:" . $resultadoConsulta);
                echo json_encode(["error" => "Error al registrar la entrada"]);
            }
        } else {
            error_log("No se enviaron sesiones.");
            echo json_encode(["error" => "No se seleccionaron sesiones"]);
        }
    }
    /**
    * Acción: asignarGuardia
    * 
    * @description Marca una guardia como cubierta por un docente. Dispara un trigger en BD.
    * 
    * @param string sesion             Número de sesión
    * @param string documentAus        Profesor que falta
    * @param string document           Profesor que cubre
    * @param int    cubierto           Valor 1 para marcar como cubierta
    * @return JSON                     { exito: string } o { error: string }
    */
    elseif ($accion === "asignarGuardia") {
        $fecha = date('Y-m-d');
    
        $datos = json_decode(file_get_contents("php://input"), true);

        if (!is_array($datos)) {
            echo json_encode(["error" => "Datos no válidos"]);
            exit;
        }
    
        // Parámetros 
        $sesionAus       = filter_var($datos['sesion'], FILTER_SANITIZE_SPECIAL_CHARS);
        $documentAus     = filter_var($datos['documentAus'], FILTER_SANITIZE_SPECIAL_CHARS);
        $documentCubierto= filter_var($datos['document'], FILTER_SANITIZE_SPECIAL_CHARS);
        $cubiertoAus     = filter_var($datos['cubierto'], FILTER_SANITIZE_SPECIAL_CHARS);

        $sql = "
            UPDATE ausencias
            SET 
            cubierto = '$cubiertoAus',
            document_cubierto = '$documentCubierto',
            NombreRemp = (
                    SELECT CONCAT(nom, ' ', cognom1, ' ', cognom2)
                    FROM docent
                    WHERE document = '$documentCubierto'
                )
            WHERE sesion   = '$sesionAus'
            AND document = '$documentAus';
        ";

        $resultadoAsignar = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
        if ($resultadoAsignar === false) {
            echo json_encode(["error" => "Error al marcar la guardia como cubierta"]);
            exit;
        }
        echo json_encode(["exito" => "Guardia asignada correctamente"]);
        exit;
    }
    /**
    * Acción: historialGuardias
    * 
    * @description Devuelve las guardias realizadas por un docente, con filtros opcionales.
    * 
    * @param string document   DNI del docente
    * @param string fecha      (Opcional) Fecha a filtrar
    * @param int    hora       (Opcional) Sesión a filtrar
    * @return JSON             Lista de registros de guardias
    */
    elseif ($accion === "historialGuardias") {

        $document = filter_input(INPUT_POST, "document", FILTER_SANITIZE_SPECIAL_CHARS);
        $fecha = isset($_POST["fecha"])
    ? filter_var(date("Y-m-d", strtotime($_POST["fecha"])), FILTER_SANITIZE_SPECIAL_CHARS)
    : null;        
    $sesion = isset($_POST["hora"]) 
    ? filter_var((int)trim($_POST["hora"]), FILTER_SANITIZE_NUMBER_INT) 
    : null;
    
        if ($fecha && $sesion) {
            $sql = "SELECT * FROM registro_guardias 
                WHERE docente_guardia = '$document' 
                AND fecha = '$fecha' 
                AND sesion_orden = $sesion
            ";
        } elseif ($fecha) {
            $sql = "SELECT * FROM registro_guardias 
                WHERE docente_guardia = '$document' 
                AND fecha = '$fecha'
            ";
        } elseif ($sesion) {
            $sql = "SELECT * FROM registro_guardias 
                WHERE docente_guardia = '$document' 
                AND sesion_orden = $sesion
            ";
        } else {
            $sql = "SELECT * FROM registro_guardias 
                WHERE docente_guardia = '$document'
            ";
        }

        $historialGuard = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
    
        if (is_array($historialGuard)) {
            echo json_encode($historialGuard);
        } else {
            echo json_encode(["error" => "No se encontraron registros de guardias"]);
        }
    }
    /**
    * Acción: consultarAsistencia
    * 
    * @description Devuelve las asistencias de un docente con filtro por día o mes.
    * 
    * @param string document   DNI del docente (opcional)
    * @param string fecha      Fecha concreta (opcional)
    * @param string mes        Mes en formato YYYY-MM (opcional)
    * @return JSON             Lista de asistencias
    */
    elseif ($accion === 'consultarAsistencia') {
        $documento = $_POST['document'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $mes = $_POST['mes'] ?? null;
    
        $condiciones = [];
        
        if ($documento) {
            $condiciones[] = "document = '$documento'";
        }
    
        if ($fecha) {
            $fechaFormateada = date("Y-m-d", strtotime($fecha));
            $condiciones[] = "fecha = '$fechaFormateada'";
        }
    
        if ($mes) {
            $anioMes = explode('-', $mes);
            $anio = $anioMes[0];
            $mesNum = $anioMes[1];
            $condiciones[] = "YEAR(fecha) = '$anio' AND MONTH(fecha) = '$mesNum'";
        }
    
        $where = count($condiciones) > 0 ? 'WHERE ' . implode(' AND ', $condiciones) : '';
    
        $sql = "SELECT nombre, fecha, hora_entrada, hora_salida 
            FROM registro_jornada 
            $where ORDER BY fecha, hora_entrada
        ";
        
        $resultado = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
    
        if (is_array($resultado)) {
            echo json_encode($resultado);
        } else {
            echo json_encode(["error" => "No se encontraron registros"]);
        }
    }
    /**
    * Acción: consultaProfesEscritos
    * 
    * @description Devuelve la lista de profesores con los que se ha intercambiado mensajes.
    * Solo devuelve un mensaje por profesor, el más reciente.
    * 
    * @param string documento  DNI del usuario logueado
    * @return JSON             Lista de conversaciones con último mensaje
    */
    elseif ($accion === "consultaProfesEscritos") {
        $doc = $_POST['documento'];
        $sql = "SELECT DISTINCT *
            FROM (
                SELECT DISTINCT
                docent_receptor AS interlocutor_id,
                nombreReceptor   AS interlocutor_nombre,
                mensaje,
                fecha,
                hora
                FROM mensajes
                WHERE docent_emisor  = '$doc'
                AND docent_receptor <> '$doc'
                UNION ALL
                SELECT DISTINCT
                docent_emisor   AS interlocutor_id,
                nombreEmisor    AS interlocutor_nombre,
                mensaje,
                fecha,
                hora
                FROM mensajes
                WHERE docent_receptor = '$doc'
                AND docent_emisor   <> '$doc'
            ) AS todos_los_mensajes
            ORDER BY fecha DESC, hora DESC;
        ";
        $resultado = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
        if (is_array($resultado)) {
            $unicos = [];

            foreach ($resultado as $mensaje) {
                $dni = $mensaje[0]; 

                if (!isset($unicos[$dni])) {
                $unicos[$dni] = $mensaje;
                }
            }

            $resultadoFiltrado = array_values($unicos);
            echo json_encode($resultadoFiltrado);  

        } else {
            echo json_encode(["error" => "No se encontraron docentes"]);  
        }
    }
    /**
    * Acción: consultaProfesMensaje
    * 
    * @description Devuelve todos los profesores disponibles para enviar mensajes.
    * 
    * @return JSON  Lista de profesores con su DNI y nombre completo
    */
    elseif ($accion === "consultaProfesMensaje") {

        $sql = "SELECT document, 
            CONCAT(nom, ' ', cognom1, ' ', cognom2) 
            AS nombre_completo FROM docent
        ";
        $resultado = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
    
        if (is_array($resultado)) {
            echo json_encode($resultado);  
        } else {
            echo json_encode(["error" => "No se encontraron docentes"]);  
        }
    }
    /**
    * Acción: consultaMensajes
    * 
    * @description Devuelve todos los mensajes entre dos docentes, y marca como leídos los recibidos.
    * 
    * @param string emisor     DNI del emisor
    * @param string receptor   DNI del receptor
    * @return JSON             Lista de mensajes
    */
    elseif ($accion === "consultaMensajes") {
        $docent_emisor   = $_POST['emisor'];
        $docent_receptor = $_POST['receptor'];
    
        $sql = "SELECT docent_emisor, mensaje, fecha, hora, leido
            FROM mensajes
            WHERE (
                docent_emisor = '$docent_emisor' 
                AND docent_receptor = '$docent_receptor'
            )
            OR (
                docent_emisor = '$docent_receptor' 
                AND docent_receptor = '$docent_emisor'
            )
            ORDER BY fecha
        ";

        $resultMensajes = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
    
        if (is_array($resultMensajes) && !empty($resultMensajes)) {

            foreach ($resultMensajes as $mensaje) {

                if (!($mensaje[4])) {
                    $sqlUpd = " UPDATE mensajes
                        SET leido = NOW()
                        WHERE docent_emisor   = '$docent_receptor'
                        AND docent_receptor = '$docent_emisor'
                        AND leido IS NULL 
                    ";
                conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlUpd);
                }
            }
        }
        $sql = "SELECT docent_emisor, mensaje, fecha, hora, leido
            FROM mensajes
            WHERE (
                docent_emisor = '$docent_emisor' 
                AND docent_receptor = '$docent_receptor'
            )
            OR (
                docent_emisor = '$docent_receptor' 
                AND docent_receptor = '$docent_emisor'
            )
            ORDER BY fecha
        ";
        if (is_array($resultMensajes) && !empty($resultMensajes)) {
            $resultMensajes = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
            echo json_encode($resultMensajes);
        }
        else {
            echo json_encode("No tienes mensajes en este chat");
        }
    }
    /**
    * Acción: enviaMensaje
    * 
    * @description Inserta un nuevo mensaje en la base de datos entre dos docentes.
    * 
    * @param string emisor           DNI del emisor
    * @param string nombreEmisor     Nombre del emisor
    * @param string receptor         DNI del receptor
    * @param string nombreReceptor   Nombre del receptor
    * @param string mensaje          Texto del mensaje
    * @return JSON                   { success: true } o { error: string }
    */
    elseif ($accion == "enviaMensaje") {
        $docent_emisor   = $_POST['emisor'];
        $nombreEmisor = $_POST['nombreEmisor'];
        $docent_receptor = $_POST['receptor'];
        $nombreReceptor = $_POST['nombreReceptor'];
        $mensaje         = $_POST['mensaje'];
        $fecha           = date('Y-m-d');
        $hora            = date('H:i:s');  
    
        $sql = "INSERT INTO mensajes
            (docent_emisor, nombreEmisor, docent_receptor,
            nombreReceptor, mensaje, fecha, hora)
            VALUES(
                '$docent_emisor', '$nombreEmisor', '$docent_receptor',
                '$nombreReceptor', '" . addslashes($mensaje) . "', '$fecha', '$hora'
            )
        ";

        $mensajeEscrito = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
        if ($mensajeEscrito) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false,
                'error'   => 'No se pudo enviar el mensaje'
            ]);
        }
        exit;
    }
    elseif ($accion == "obtenerUsuario") {

        $document = $_POST['document'];
        $sql = "SELECT document,password,rol,nombre FROM usuarios WHERE document = '$document'";

        $resultado = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
        if (is_array($resultado)) {
            echo json_encode($resultado);
        } else {
            echo json_encode(["error" => "No se encontraron datos del usuario"]);
        }
    }
} 
    // =====================================
    // PETICIONES **PUT**
    // =====================================
elseif ($metodo === 'PUT') {

    $raw = file_get_contents('php://input');

    $datos = json_decode($raw, true);
    if (!is_array($datos)) {
        parse_str($raw, $datos);
    }
    /**
    * Acción: EditarMensaje 
    * 
    * @description Permite editar un mensaje ya enviado. Se localiza por emisor, fecha, hora y contenido original.
    * 
    * @param string docentEmisor     DNI del emisor del mensaje
    * @param string fecha            Fecha del mensaje (formato Y-m-d)
    * @param string hora             Hora exacta del mensaje (formato H:i:s)
    * @param string mOriginal        Texto original del mensaje
    * @param string mEditado         Texto editado que se quiere guardar
    * 
    * @return JSON                   { exito: true } si se actualizó correctamente, false si no
    */
    if ($datos["accion"] == "EditarMensaje") {
        $docentEmisor       = $datos['docentEmisor']  ?? null;
        $fecha           = $datos['fecha']         ?? null;
        $hora            = $datos['hora']          ?? null;
        $mensajeOriginal = $datos['mOriginal']     ?? null;
        $mensajeEditado  = $datos['mEditado']      ?? null;

        $sql = "UPDATE mensajes 
            SET mensaje = '$mensajeEditado' 
            WHERE docent_emisor = '$docentEmisor' 
            AND fecha = '$fecha' 
            AND hora ='$hora' 
            AND mensaje = '$mensajeOriginal'
        ";

        $result = conexion_bd(SERVIDOR, USER,PASSWD,BASE_DATOS,$sql);

        if ($result) {
            echo json_encode(["exito" => true]);
        } else{
            echo json_encode(["exito" => false]);
        }
    }
    elseif ($datos["accion"] == "actualizarDatos") {
        $nombre = $datos['nombre'] ?? null;
        $rol = $datos['rol'] ?? null;
        $passwordSinCifrar = $datos['contrasena'] ?? null;
        $documento = $datos['document'] ?? null;

        // Cifrar la contraseña
        $password = password_hash($passwordSinCifrar, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios 
            SET nombre = '$nombre', rol = '$rol', password = '$password' 
            WHERE document = '$documento'";
        $result = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
        if ($result) {
            echo json_encode(["exito" => true]);
        } else {
            echo json_encode(["exito" => false]);  
        }
    }
} 
    // =====================================
    // PETICIONES **DELETE**
    // =====================================
elseif ($metodo === 'DELETE') {         
    $raw = file_get_contents('php://input');

    $datos = json_decode($raw, true);
    if (!is_array($datos)) {
        parse_str($raw, $datos);
    }
    /**
    * Acción: BorrarMensaje
    * 
    * @description Elimina uno o varios mensajes enviados. Cada mensaje se identifica por fecha, hora y contenido exacto.
    * 
    * @param array mensajes[]        Lista de mensajes a borrar (con fecha, hora y mensajeOriginal)
    * 
    * @example JSON esperado:
    * {
    *   "accion": "BorrarMensaje",
    *   "mensajes": [
    *     { "fecha": "2025-05-08", "hora": "09:30:00", "mensajeOriginal": "Hola" },
    *     { "fecha": "2025-05-08", "hora": "09:31:00", "mensajeOriginal": "¿Estás ahí?" }
    *   ]
    * }
    * 
    * @return JSON                   { exito: true } si se borraron, false si falló
    */
    if ($datos["accion"] == "BorrarMensaje") {
        $wheres = [];

        foreach ($datos["mensajes"] as $m) {
            $fecha   = $m['fecha'];
            $hora    = $m['hora'];
            $texto   = $m['mensajeOriginal'];
            $wheres[] = "(`fecha` = '$fecha' AND `hora` = '$hora' AND `mensaje` = '$texto')";
        }
        
        $sql = "DELETE FROM `mensajes`
            WHERE " . implode(' OR ', $wheres);
        $result = conexion_bd(SERVIDOR,USER,PASSWD,BASE_DATOS,$sql);

        if ($result) {
            echo json_encode(["exito" => true]);
        } else{
            echo json_encode(["exito" => false]);
        }
    }
} 
else {         
    echo json_encode(["error" => "Opción incorrecta!!!!"]); 
}
