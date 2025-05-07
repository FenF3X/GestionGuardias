<?php
/**
 * verInformes.php
 *
 * Pestaña con formulario dinámico en la muestra del informe respecto a las ausencias y por quien
 * ha sido asignado, ademas de ver la cantidad de faltas que ha realizado ese profesor. Puedes generar
 * el informe por dia, semana, mes, trimestre, curso o docente. Incluye la posibilidad de exportarlo en
 * PDF. Incluye un alert que cancela la redirección en caso de no existir ausencias sobre esos filtros
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @link       http://localhost/GestionGuardias/PROYECTO/REST/rest_cliente/vistas/verInformes.php
 *
 * @function initSessionAndFetchProfesores
 * @description Inicia la sesión, valida autenticación de administrador y obtiene la lista de profesores.
 */
session_start();
include("../curl_conexion.php");
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
$documento = $_SESSION['document'];

if ($rol !== 'admin') {
  header('Location: dashboard.php'); // Redirige si no es admin
  exit;
}
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
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Página principal de <?php echo htmlspecialchars($nombre); ?></title>
  <link rel="shortcut icon" href="../src/images/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">

<link rel="stylesheet" href="../src/guardias.css">
<link rel="stylesheet" href="../src/principal.css">

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">
      <img src="../src/images/sinFondoDos.png" alt="Logo AsistGuard" class="logo-navbar">
    </a>

    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link text-white" href="guardiasRealizadas.php?auto=1">Guardias realizadas</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="../verAusencias.php?cargar_guardias=1">Consultar ausencias</a></li>

        <?php if ($rol === 'admin'): ?>
          <li class="nav-item"><a class="nav-link text-white" href="verInformes.php">Generar informes</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Gestión de asistencia
            </a>
            <ul class="dropdown-menu dropdown-hover">
              <li><a class="dropdown-item" href="verAsistencia.php">Consultar asistencia</a></li>
              <li><a class="dropdown-item" href="registroAusencias.php">Registrar ausencia</a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>


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

<main>
  <div class="container mt-5">
    <!-- Perfil: foto + datos a la izquierda, botones a la derecha -->
    <div class="perfil-contenedor 
                d-flex flex-column flex-md-row 
                align-items-center justify-content-between">
      
      <!-- IZQUIERDA: foto + datos -->
      <div class="d-flex align-items-center mb-3 mb-md-0">
        <div class="foto-wrapper me-4">
          <img src="../src/images/default.jpg" alt="Foto de perfil" class="foto-circular">
        </div>
        <div class="info-usuario text-start">
          <p><strong>Documento:</strong> <?php echo htmlspecialchars($documento); ?></p>
          <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
          <p><strong>Rol:</strong> <?php echo htmlspecialchars($rol); ?></p>
        </div>
      </div>
      
     
     <div class="botones-usuario d-flex align-items-center gap-2 text-center text-md-end">


  <!-- CHAT -->
  <a 
    href="chat.php" 
    class="btn btn-primary d-flex align-items-center justify-content-center" 
    role="button"
    style=" border: 2px solid; 
   background:linear-gradient(135deg, #1e3a5f, #0f1f2d);"
  >
    <i class="bi bi-chat-dots-fill fs-4"></i>
    <span class="ms-2 d-none d-md-inline">Chat</span>
  </a>
    </div>



    <?php if (isset($mensaje)): ?>
      <div class="alert-container">
        <div class="alert alert-<?php echo htmlspecialchars($mensaje['type']); ?> text-center" id="mensajeAlert">
          <?php echo htmlspecialchars($mensaje['text']); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>
<div class="container mt-5">
    <?php if (isset($_SESSION['alert_message'])): ?>
        <div class="alert alert-warning text-center w-50 mx-auto" role="alert">
            <?php echo htmlspecialchars($_SESSION['alert_message']); ?>
        </div>
        <?php unset($_SESSION['alert_message']); ?>
    <?php endif; ?>
<div class="container py-5">
  <h2 class="mb-4">📄 Generador de informes de faltas</h2>

  <form method="GET" action="../generarInforme.php">
    <div class="mb-3">
      <label for="tipoInforme" class="form-label fw-bold">Selecciona el tipo de informe 👇</label>
      <select id="tipoInforme" name="tipoInforme" class="form-select input-select-custom" required>
        <option value="">-- Elige una opción --</option>
        <option value="dia">Por día</option>
        <option value="semana">Por semana</option>
        <option value="mes">Por mes</option>
        <option value="trimestre">Por trimestre</option>
        <option value="docent">Por docente</option>
        <option value="curs">Todo el curso</option>
      </select>
    </div>

    <div id="campo-dia" class="campo-dinamico mb-3">
      <label for="dia" class="form-label">Selecciona un día:</label>
      <input type="date" name="dia" id="dia" class="input-select-custom w-100" value="<?php echo date('Y-m-d'); ?>">
    </div>

    <div id="campo-semana" class="campo-dinamico mb-3">
      <label for="semana" class="form-label">Selecciona un día de la semana:</label>
      <input type="date" name="semana" id="semana" class="input-select-custom w-100" value="<?php echo date('Y-m-d'); ?>">
    </div>

    <div id="campo-mes" class="campo-dinamico mb-3">
      <label for="mes" class="form-label">Selecciona un mes:</label>
      <input type="month" name="mes" id="mes" class="input-select-custom w-100" value="<?php echo date('Y-m'); ?>">
    </div>

    <div id="campo-trimestre" class="campo-dinamico mb-3">
      <label for="trimestre" class="form-label">Selecciona un trimestre:</label>
      <select name="trimestre" id="trimestre" class="form-select input-select-custom">
        <option value="1">1º trimestre</option>
        <option value="2">2º trimestre</option>
        <option value="3">3º trimestre</option>
      </select>
    </div>

    <div id="campo-docent" class="campo-dinamico mb-3">
      <label for="docent" class="form-label">Selecciona un docente:</label>
      <select name="docent" id="docent" class="form-select input-select-custom">
        <option value="">-- Elige un docente --</option>
        <?php foreach ($profesores as $profesor): ?>
                        <option value="<?php echo $profesor[0]; ?>"><?php echo htmlspecialchars($profesor[1]); ?></option>
                    <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary mt-3" style=" border: 2px solid; 
   background:linear-gradient(135deg, #1e3a5f, #0f1f2d);">Generar informe</button>
  </form>
</div>
        </div>
<!-- 1) Bootstrap JS y Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script src="../src/calendar.js"></script>
<script src="../src/verInformes.js"></script>

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
