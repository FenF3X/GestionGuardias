<?php 
include("config.php");
session_start(); 

$metodo = $_SERVER['REQUEST_METHOD']; 
$recurso = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);  

if ($metodo === 'GET') {
    // Verificación de parámetros
    $document = $_GET['document'] ?? null;
    if (!$document) {
        echo json_encode(["error" => "documento requerido"]);
        exit;
    }
    // Manejo de diferentes acciones
    $accion = $_GET['accion'] ?? null;
    error_log("entro server");
    if($accion === "consultaSesiones"){
        error_log("entro consultaSesiones");
        $sql = "SELECT DISTINCT sessio_orde, CONCAT('Sesion ', sessio_orde, ': ', hora_desde, ' - ', hora_fins) AS horario_completo 
            FROM sessions_horari 
            ORDER BY sessio_orde ASC";
        $result = conexion_bd(SERVIDOR,USER,PASSWD,BASE_DATOS,$sql);
        error_log("hago consulta");
        if (is_array($result)) {
            if (!empty($result)) {
                echo json_encode($result);
            } else{
                echo json_encode(["error" => "Sesiones vacias"]);
            }
        } else{
                echo json_encode(["error" => "Error en la consulta"]);
            }
    }
    elseif ($accion === "verHorario") {
        if (isset($_GET['dia'])) {
            $dia = $_GET['dia'];

            // Consulta de horario por día
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
        } else {
            echo json_encode(["error" => "Día no proporcionado"]);
        }
    } elseif ($accion === "verGuardias") {
        $fecha = date('Y-m-d');  // Obtener la fecha de hoy

        // Consulta para obtener las guardias
        $sql = "SELECT sesion, aula, grupo, asignatura, document,nombreProfe,cubierto FROM ausencias WHERE fecha = '$fecha'";
        $respuesta = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
        if (is_array($respuesta)) {
            echo json_encode($respuesta);  // Devolver la respuesta con las guardias
        } else {
            echo json_encode(["error" => "No se encontraron guardias para hoy"]);
        }
    } elseif ($accion === "verGuardiasPorFecha") {
        $fecha = $_GET['fecha'] ?? null;
        if (!$fecha) {
            echo json_encode(["error" => "Fecha no proporcionada"]);
            exit;
        }
        // Consulta para obtener las guardias
        $sql = "SELECT sesion, aula, grupo, asignatura, document,nombreProfe,fecha FROM ausencias WHERE fecha = '$fecha'";
        $respuesta = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
        if (is_array($respuesta)) {
            echo json_encode($respuesta);  // Devolver la respuesta con las guardias
        } else {
            echo json_encode(["error" => "No se encontraron guardias para " . $fecha]);
        }
    } elseif ($accion === "generarInforme") {
        $tipo = $_GET['tipo'] ?? [];

        switch ($tipo) {
            case 'dia':
                $fecha = $_GET['fecha'];
                $sql = "SELECT fecha,nombreProfe,nombreProfeReempl, aula, grupo, asignatura, sesion_orden,dia_semana, CONCAT(hora_inicio, '--', hora_fin) 
                 FROM  registro_guardias WHERE  fecha = '$fecha'; ";
                break;
            case 'semana':  
                $diaSemana = $_GET['semana'];
                $inicioSemana = date('Y-m-d', strtotime('monday this week', strtotime($diaSemana)));
                $finSemana = date('Y-m-d', strtotime('sunday this week', strtotime($diaSemana)));
                $sql = "SELECT fecha,nombreProfe,nombreProfeReempl, aula, grupo, asignatura, sesion_orden,dia_semana, CONCAT(hora_inicio, '--', hora_fin)
                 FROM registro_guardias WHERE fecha BETWEEN '$inicioSemana' AND '$finSemana'";
                break;
            case 'mes':
                $mes = $_GET['mes'];
                $sql = "SELECT fecha,nombreProfe,nombreProfeReempl, aula, grupo, asignatura, sesion_orden,dia_semana, CONCAT(hora_inicio, '--', hora_fin)
                 FROM registro_guardias WHERE DATE_FORMAT(fecha, '%Y-%m') = '$mes'";
                 break;
            case 'trimestre':
                $trimestre = $_GET['trimestre'] ?? '';
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
                $sql = "SELECT fecha,nombreProfe, nombreProfeReempl,aula, grupo, asignatura, sesion_orden,dia_semana, CONCAT(hora_inicio, '--', hora_fin) 
                FROM registro_guardias WHERE fecha BETWEEN '$inicio' AND '$fin'";
                break;
            case 'docent':
                    $docente = $_GET['docente'] ?? '';
                    $sql = "SELECT fecha,nombreProfe,nombreProfeReempl, aula, grupo, asignatura, sesion_orden,dia_semana, CONCAT(hora_inicio, '--', hora_fin)
                     FROM registro_guardias WHERE docente_guardia = '$docente'";
                break;
            case 'curs':
                $inicio = "2024-09-09";
                $fin = "2025-06-21";
                $sql = "SELECT fecha,nombreProfe,nombreProfeReempl, aula, grupo, asignatura, sesion_orden,dia_semana, CONCAT(hora_inicio, '--', hora_fin)
                 FROM registro_guardias WHERE fecha BETWEEN '$inicio' AND '$fin'";
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
elseif ($metodo === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $accion = $data['accion'] ?? ($_POST['accion'] ?? null);
    
    if ($accion === "InicioSesion") {
        // Manejo de inicio de sesión
        $document = $_POST['document'] ?? null;         
        $password = $_POST['password'] ?? null;          

        if ($document && $password) {             
            $sql = "SELECT * FROM usuarios WHERE document = '$document'";             
            $resultado = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);              

            if (is_array($resultado)) {                 
                $sqlNom = "SELECT CONCAT(nom,' ',cognom1,' ',cognom2) FROM docent WHERE document = '$document'";                 
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
                    echo json_encode(["loggeado" => true, "nombre" => $nombre_profesor, "document" => $resultado[0][1], "rol" => $resultado[0][3]]);                 
                } else {                     
                    $fechaHora = date('d-m-Y H:i:s');                     
                    $linea = "$fechaHora | Fallo | DNI: $document | Profesor: $nombre_profesor | Log In(Entrada)\n";                     
                    $archivo = fopen("registroAccesos.txt", "a");                     
                    if ($archivo) {                         
                        fwrite($archivo, $linea);                         
                        fclose($archivo);                     
                    } else {                         
                        error_log("Error al abrir el archivo");                     
                    }                     
                    echo json_encode(["loggeado" => false, "error" => "Contraseña errónea"]);                 
                }              
            } else {                 
                echo json_encode(["loggeado" => false, "error" => "Usuario inexistente"]);             
            }          
        } else {             
            echo json_encode(["loggeado" => false, "error" => "Faltan datos del usuario"]);         
        } 
    } elseif ($accion === "ficharEntrada" || $accion === "ficharSalida") {
        // Manejo de fichaje de entrada o salida
        $document = $_POST['document'] ?? null;         
        $fecha = date('Y-m-d');  // Obtén la fecha actual
        $hora_entrada = $_POST['hora_entrada'] ?? null;
        $hora_salida = $_POST['hora_salida'] ?? null;

        if ($document) {
            if ($accion === "ficharEntrada") {
                // Comprobar si ya fichó la entrada hoy
                $sqlCheckEntrada = "SELECT * FROM registro_jornada WHERE document = '$document' AND fecha = '$fecha' AND hora_entrada IS NOT NULL";
                $resultadoEntrada = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlCheckEntrada);

                if (is_array($resultadoEntrada) && count($resultadoEntrada) > 0) {
                    // Si ya existe un registro de entrada
                    echo json_encode(["error" => "Ya has fichado entrada hoy"]);
                } else {
                    // Fichaje de entrada
                    if ($hora_entrada) {
                        $sqlNombre = "SELECT CONCAT(nom, ' ', cognom1, ' ', cognom2) AS nombre FROM docent WHERE document = '$document'";
                        $resultadoNombre = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlNombre);
                        $nombre = is_array($resultadoNombre) ? $resultadoNombre[0][0] : 'Desconocido';

                        $sql = "INSERT INTO registro_jornada (document, fecha, hora_entrada, hora_salida, nombre) VALUES ('$document', '$fecha', '$hora_entrada', NULL, '$nombre')";
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
                $sqlCheckSalida = "SELECT * FROM registro_jornada WHERE document = '$document' AND fecha = '$fecha' AND hora_entrada IS NOT NULL AND hora_salida IS NULL";
                $resultadoSalida = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlCheckSalida);

                if (is_array($resultadoSalida) && count($resultadoSalida) > 0) {
                    // Si existe un registro de entrada, actualizamos con la salida
                    if ($hora_salida) {
                        $sqlUpdateSalida = "UPDATE registro_jornada SET hora_salida = '$hora_salida' WHERE document = '$document' AND fecha = '$fecha' AND hora_salida IS NULL";
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
    } elseif ($accion === "consultaProfes") {
        // Consulta para obtener todos los profesores de la tabla 'docent'
        $sql = "SELECT document, CONCAT(nom, ' ', cognom1, ' ', cognom2) AS nombre_completo FROM docent";
        $resultado = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
    
        // Verificar si la consulta fue exitosa
        if (is_array($resultado)) {
            echo json_encode($resultado);  // Devolver los datos de los profesores
        } else {
            echo json_encode(["error" => "No se encontraron docentes"]);  // Error en caso de no encontrar docentes
        }
    } elseif ($accion === "verSesiones") {
        $document = $_POST['document'];
        $dia = $_POST['dia'];

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
    } elseif ($accion === "registrarAusencia") {
        error_log("Entrando en registrarAusencia");
    
        $data = json_decode(file_get_contents("php://input"), true);
    
        $fecha = $data['fecha'] ?? null;
        $document = $data['document'] ?? null;
        $justificada = $data['justificada'] ?? null;
        $jornada_completa = $data['jornada_completa'] ?? null;
        $sesionesSeleccionadas = $data['sesiones'] ?? [];
    
        $resultadoIn = true;
    
        // Obtener nombre del profesor
        $sqlNombre = "SELECT CONCAT(nom, ' ', cognom1, ' ', cognom2) AS nombreProfe FROM docent WHERE document = '$document'";
        $resultadoNombre = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlNombre);
        $nombreProfe = is_array($resultadoNombre) ? $resultadoNombre[0][0] : 'Desconocido';
    
        if (!empty($sesionesSeleccionadas)) {
            foreach ($sesionesSeleccionadas as $sesionJson) {
                $sesion = json_decode($sesionJson, true);
    
                if (!is_array($sesion) || count($sesion) < 7) {
                    error_log("Sesión malformada: " . print_r($sesionJson, true));
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
                            document, nombreProfe, justificada, jornada_completa, fecha
                        ) VALUES (
                            '$hora_inicio', '$hora_fin', '$dia', '$aula', '$grupo', '$asignatura', '$sesion_orden',
                            '$document', '$nombreProfe', '$justificada', '$jornada_completa', '$fecha'
                        )";
    
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
                error_log("Error en la inserción");
                echo json_encode(["error" => "Error al registrar la entrada"]);
            }
        } else {
            error_log("No se enviaron sesiones.");
            echo json_encode(["error" => "No se seleccionaron sesiones"]);
        }
    }
    
    elseif ($accion === "asignarGuardia") {
        $fecha = date('Y-m-d');
        $datos = json_decode(file_get_contents("php://input"), true);
    
        if (!is_array($datos)) {
            error_log("Error: los datos recibidos no son JSON válido");
            echo json_encode(["error" => "Datos no válidos"]);
            exit;
        }
    
        $sesionAus = $datos["sesion"];
        $documentAus = $datos["documentAus"];
        $documentCubierto = $datos["document"];
        $cubiertoAus = $datos["cubierto"];
    
        $sql = "UPDATE ausencias 
        SET 
            cubierto = '$cubiertoAus', 
            NombreRemp = (
                SELECT CONCAT(nom, ' ', cognom1, ' ', cognom2)
                FROM docent 
                WHERE document = '$documentCubierto'
            )
        WHERE sesion = '$sesionAus' AND document = '$documentAus'";
        $resultadoAsignar = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
        $funcional = false;
    
        if ($resultadoAsignar > 0) {
            $sql = "SELECT * FROM ausencias WHERE sesion = '$sesionAus' AND document = '$documentAus' AND cubierto = 1";
            $resinsertinforme = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
    
            if (is_array($resinsertinforme)) {
                $hora_inicio = $resinsertinforme[0][1];
                $hora_fin = $resinsertinforme[0][2];
                $dia = $resinsertinforme[0][3];
                $aula = $resinsertinforme[0][4];
                $grupo = $resinsertinforme[0][5];
                $asignatura = $resinsertinforme[0][6];
                $sesion = $resinsertinforme[0][7];
                $document = $resinsertinforme[0][8];
                $fecha = $resinsertinforme[0][12];
                $nombreGuardia = $resinsertinforme[0][14];
    
                // Obtener nombre del docente ausente
                $sqlNombre = "SELECT CONCAT(nom, ' ', cognom1, ' ', cognom2) AS nombreProfe FROM docent WHERE document = '$document'";
                $resultadoNombre = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlNombre);
                $nombreProfe = is_array($resultadoNombre) ? $resultadoNombre[0][0] : 'Desconocido';
    
                $sqlCheck = "SELECT COUNT(*) FROM registro_guardias 
                             WHERE fecha = '$fecha' AND docente_ausente = '$document' AND sesion_orden = '$sesion'";
                $existe = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlCheck);
    
                if (is_array($existe) && $existe[0][0] == 0) {
                    $sqlInforme = "INSERT INTO registro_guardias 
                        (fecha, docente_ausente, nombreProfe, docente_guardia,nombreProfeReempl ,aula, grupo, asignatura, sesion_orden, dia_semana, hora_inicio, hora_fin) 
                        VALUES 
                        ('$fecha', '$document', '$nombreProfe', '$documentCubierto','$nombreGuardia' ,'$aula', '$grupo', '$asignatura', '$sesion', '$dia', '$hora_inicio', '$hora_fin')";
    
                    $resultadoInforme = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sqlInforme);
    
                    if ($resultadoInforme > 0) {
                        $funcional = true;
                    }
                }
            }
        }
    
        if ($funcional) {
            echo json_encode(["exito" => "Guardia asignada correctamente"]);
        } else {
            echo json_encode(["error" => "La guardia ya estaba registrada o ha fallado el guardado"]);
        }
    }
    
        
    elseif ($accion === "historialGuardias") {
        $document = $_POST['document'];
        $fecha = isset($_POST["fecha"]) ? date("Y-m-d", strtotime($_POST["fecha"])) : null;
        $sesion = isset($_POST["hora"]) ? (int)trim($_POST["hora"]) : null;
    
        if ($fecha && $sesion) {
            // Ambos seleccionados
            $sql = "SELECT * FROM registro_guardias 
                    WHERE docente_guardia = '$document' 
                    AND fecha = '$fecha' 
                    AND sesion_orden = $sesion";
        } elseif ($fecha) {
            // Solo fecha
            $sql = "SELECT * FROM registro_guardias 
                    WHERE docente_guardia = '$document' 
                    AND fecha = '$fecha'";
        } elseif ($sesion) {
            // Solo sesión
            $sql = "SELECT * FROM registro_guardias 
                    WHERE docente_guardia = '$document' 
                    AND sesion_orden = $sesion";
        } else {
            // Ni fecha ni sesión
            $sql = "SELECT * FROM registro_guardias 
                    WHERE docente_guardia = '$document'";
        }
    
        error_log("SQL generado: $sql");
    
        $historialGuard = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
    
        if (is_array($historialGuard)) {
            echo json_encode($historialGuard);
        } else {
            echo json_encode(["error" => "No se encontraron registros de guardias"]);
        }
    }
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
            // Extraemos año y mes del input tipo "2025-04"
            $anioMes = explode('-', $mes);
            $anio = $anioMes[0];
            $mesNum = $anioMes[1];
            $condiciones[] = "YEAR(fecha) = '$anio' AND MONTH(fecha) = '$mesNum'";
        }
    
        $where = count($condiciones) > 0 ? 'WHERE ' . implode(' AND ', $condiciones) : '';
    
        $sql = "SELECT nombre, fecha, hora_entrada, hora_salida FROM registro_jornada $where ORDER BY fecha, hora_entrada";
    
        error_log("Consulta asistencia: $sql");
    
        $resultado = conexion_bd(SERVIDOR, USER, PASSWD, BASE_DATOS, $sql);
    
        if (is_array($resultado)) {
            echo json_encode($resultado);
        } else {
            echo json_encode(["error" => "No se encontraron registros"]);
        }
    }
    
elseif ($metodo === 'PUT') {         
    // Por implementar         
} 
elseif ($metodo === 'DELETE') {         
    // Por implementar         
} 
else {         
    echo json_encode(["error" => "Opción incorrecta!!!!"]); 
}
}