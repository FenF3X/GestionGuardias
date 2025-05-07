<?php
/**
 * registroAusencias.php
 *
 * Formulario de consulta de un profesor a seleccionar en un dia a seleccionar con prevención de
 * no ser un dia en fin de semana y además un campo de justificación de ausencia
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @link       http://localhost/GestionGuardias/PROYECTO/REST/rest_cliente/vistas/registroAusencias.php

 * @function initSession
 * @description Inicia la sesión y valida que el usuario esté autenticado.
 */
session_start();
include("../curl_conexion.php");
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}

/**
 * @var string $rol      Rol del usuario ("admin" o "profesor").
 * @var string $nombre   Nombre del usuario para mostrar en la cabecera.
 */

$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
$params = [
    'accion' => 'consultaProfes'
];
$response = curl_conexion(URL, 'POST', $params); // Realizamos la consulta usando POST

// Decodificar la respuesta JSON
$profesores = json_decode($response, true);

// Verificar si hay errores en la respuesta
if (isset($profesores['error'])) {
    $_SESSION['mensaje'] = ['type' => 'danger', 'text' => $profesores['error']];
} else {
    $_SESSION['profesores'] = $profesores;
}

// Verificar si el usuario está autenticado y tiene permisos de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php"); // Redirigir si no es un admin
    exit();
}

// Obtener los datos de los profesores desde la sesión
$profesores = $_SESSION['profesores'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina principal de <?php echo htmlspecialchars($nombre); ?></title>
    <link rel="shortcut icon" href="../src/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../src/principal.css">
    <link rel="stylesheet" href="../src/guardias.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
  ::-webkit-scrollbar{display:none;}
        /* Estilo específico para el formulario */
        .form-container {
            max-width: 800px;
            margin: 50px auto;
        }

        .form-container .form-group {
            margin-bottom: 20px;
        }

        .form-container .btn {
            width: 100%;
        }
        #motivo::placeholder {
    color: #fff;
    opacity: 1; /* asegúrate de que no quede atenuado */
  }

  /* WebKit (Chrome, Safari, Opera, Edge Chromium) */
  #motivo::-webkit-input-placeholder {
    color: #fff;
  }

  /* Firefox 19+ */
  #motivo::-moz-placeholder {
    color: #fff;
    opacity: 1;
  }

  /* Firefox 4 – 18 */
  #motivo:-moz-placeholder {
    color: #fff;
    opacity: 1;
  }

  /* IE 10+ y Edge pre-Chromium */
  #motivo:-ms-input-placeholder {
    color: #fff;
  }
.navbar-toggler {background-color: #0f1f2d !important;  /* tu azul custom */border: 2px solid #fff !important;     /* borde blanco */}

/* 2) Icono: tres barras blancas */
.navbar-toggler-icon {
  background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='white' stroke-width='2' stroke-linecap='round' d='M4 7H26 M4 15H26 M4 23H26'/%3E%3C/svg%3E");
}


.navbar-toggler:hover {
  background-color: #18362f !important;  /* un tono ligeramente distinto si quieres */
}
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container-fluid">

    <!-- LOGO -->
    <a class="navbar-brand" href="dashboard.php">
      <img src="../src/images/sinFondoDos.png" alt="Logo AsistGuard" class="logo-navbar">
    </a>

    <!-- BOTÓN HAMBURGUESA -->
    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- CONTENIDO -->
    <div class="collapse navbar-collapse" id="navbarContent">

      <!-- MENÚ CENTRAL -->
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link text-white" href="guardiasRealizadas.php?auto=1">Guardias Realizadas</a>
        </li>
        <li class="nav-item"><a class="nav-link text-white" href="../verAusencias.php?cargar_guardias=1">Consultar Ausencias</a>
        </li>

        <?php if ($rol === 'admin'): ?>
          <li class="nav-item"><a class="nav-link text-white" href="verInformes.php">Generar informes</a></li>
          <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          Gestión de asistencia
        </a>
        <ul class="dropdown-menu dropdown-hover">
          <li><a class="dropdown-item" href="verAsistencia.php">Consultar asistencia</a></li>
          <li><a class="dropdown-item" href="registroAusencias.php">Registrar Ausencia</a></li>
        </ul>
          </li>
        <?php endif; ?>
      </ul>




      <!-- BIENVENIDA + LOGOUT A LA DERECHA -->
      <div class="d-flex align-items-center ms-auto">
        <span class="text-white me-3"><strong>Bienvenid@ <?= htmlspecialchars($nombre); ?></strong></span>
        <form method="POST" action="../logout.php" class="mb-0">
        <button 
  class="btn btn-sm btn-outline-light"
  style="background:linear-gradient(135deg, #1e3a5f, #0f1f2d);" 
  title="Cerrar sesión">
    <i class="bi bi-box-arrow-right"></i>
  </button>
        </form>
      </div>

    </div>
  </div>
</nav>

<!-- Formulario para registrar la ausencia -->
<main>
    <div class="container mt-5 form-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="m-0">Registrar Ausencia de Profesor</h3>
  <a href="chat.php"
     class="btn btn-sm btn-primary w-auto d-flex align-items-center"
     style="border: 2px solid; background: linear-gradient(135deg, #1e3a5f, #0f1f2d); padding: .25rem .5rem;">
    <i class="bi bi-chat-dots-fill fs-5"></i>
    <span class="ms-2 d-none d-md-inline fs-6">Chat</span>
  </a>
</div>


        <form action="../obtenerSesiones.php" id="busqueda"  method="POST">
            <div class="form-group">
                <label for="profesor">Seleccionar Profesor</label>
                <select id="profesor" name="document" class="input-select-custom w-100" required>
                    <option value="">Seleccione un profesor</option>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?php echo $profesor[0]; ?>"><?php echo htmlspecialchars($profesor[1]); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="form-group">
                <label for="fecha">Fecha de la Ausencia</label>
                <input type="date" id="fecha" name="fecha" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="motivo">Motivo de la Ausencia</label>
                <textarea id="motivo"
                 name="motivo" 
                 class="form-control" 
                 rows="4" 
                 style="background: linear-gradient(135deg, #1e3a5f, #0f1f2d);border: 2px solid;color:white;"
                 placeholder="Escriba el motivo de la ausencia"></textarea>
            </div>

            <button type="submit" class="btn btn-danger mt-3" style=" border: 2px solid; 
   background:linear-gradient(135deg, #0f1f2d, #18362f);">Buscar sesiones</button>
        </form>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- 2) Flatpickr y locale -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script src="../src/calendar.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#busqueda');
    const fechaInput = document.getElementById('fecha');

    form.addEventListener('submit', function (event) {
        const fechaSeleccionada = fechaInput.value;

        if (!fechaSeleccionada) return; // por si no se ha seleccionado nada

        const date = new Date(fechaSeleccionada);
        const day = date.getDay(); // Domingo = 0, Sábado = 6

        if (day === 0 || day === 6) {
            alert('Los fines de semana no son válidos.');
            event.preventDefault(); // ⚠️ Evita enviar el formulario
        }
    });
});

</script>

</body>
<footer class="bg-dark text-white py-4 mt-5" style="background: linear-gradient(135deg, #0f1f2d, #18362f) !important;">
   <div class="container text-center">
     <p class="mb-0">&copy; 2025 AsistGuard. Todos los derechos reservados.</p>
     <p>
       <a href="https://www.instagram.com/" style="color: white; text-decoration: none;">
         <img src="../src/images/instagram.png" alt="Instagram" width="24" height="24" style="background: transparent;">
       </a> |
       <a href="https://www.facebook.com/?locale=es_ES" style="color: white; text-decoration: none;">
         <img src="../src/images/facebook.png" alt="Facebook" width="24" height="24" style="background: transparent;">
       </a> |
       <a href="https://x.com/?lang=es" style="color: white; text-decoration: none;">
         <img src="../src/images/twitter.png" alt="Twitter" width="24" height="24" style="background: transparent;">
       </a> |
       <a href="https://es.linkedin.com/" style="color: white; text-decoration: none;">
         <img src="../src/images/linkedin.png" alt="LinkedIn" width="24" height="24" style="background: transparent;">
       </a></p>
   </div>
 </footer>
</html>
