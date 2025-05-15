<?php
/**
 * verGuardiasRealizadas.php
 *
 * Página para consultar el historial de guardias realizadas por el docente.
 * Permite filtrar por fecha y sesión disponibles.
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @link       http://localhost/GestionGuardias/PROYECTO/REST/rest_cliente/vistas/verGuardiasRealizadas.php
 */
/**
 * @function initSessionAndFetch
 * @description Inicia la sesión, valida autenticación y realiza petición para obtener horas disponibles.
 */
session_start();
include("../curl_conexion.php");
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}
/**
 * @var string|array $rol        - Rol del usuario ('admin', profesor).
 * @var string|array $nombre     - Nombre del usuario.
 * @var string|array $documento  - Documento  del usuario.
 */
$rol = $_SESSION['rol'] ?? [];
$nombre = $_SESSION['nombre'] ?? [];
$documento = $_SESSION['document'] ?? [];
/**
 * @var array $params - Parámetros de consulta 
 * @var string $UrlGet - URL completa con query string para la petición GET.
 * @var string $response - Respuesta JSON de la API.
 * @var array $horasDisponibles - Array decodificado de horas de guardia disponibles.
 */
$params = [
    'document' => $documento,
    'accion' => 'consultaSesiones'
];
    $UrlGet = URL . '?' . http_build_query($params);
$response = curl_conexion($UrlGet,'GET');
$horasDisponibles = json_decode($response, TRUE);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina principal de <?php echo htmlspecialchars($nombre); ?></title>
    <link rel="shortcut icon" href="../src/images/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../src/guardias.css">
<link rel="stylesheet" href="../src/principal.css">
<style>
html, body {
  height: 100%;
  margin: 0;
}

body {
  display: flex;
  flex-direction: column;
}

footer {
  flex-shrink: 0;
}

</style>
</head>
<body>
  <!--
    @section Navbar
    Barra de navegación principal con logo, enlaces y logout.
  -->
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
      <!--SOLO ADMIN-->
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
<main>
  <!--
    @section main
    Foto de perfil con datos personales y enlace a chat
  -->
  <div class="container mt-5">
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
  <!-- BOTÓN DE AJUSTES DE USUARIOS -->
  <?php if ($rol === 'admin'): ?>
<a 
    href="datospersonales.php" 
    class="btn btn-primary d-flex align-items-center justify-content-center" 
    role="button"
    style=" border: 2px solid; 
   background:linear-gradient(135deg, #1e3a5f, #0f1f2d);"
  >
<i class="bi bi-gear-fill fs-4"></i>
    <span class="ms-2 d-none d-md-inline">Usuarios</span>
  </a>
    <?php endif; ?>
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
      <!--
    @section HistorialDeGuardias
      Historial de las guardias realizadas
    -->
<section>
<div class="container mt-4">
<h4 class="mb-3">Guardias realizadas</h4>
<div class="d-flex justify-content-center mb-4">
<form action="../verGuardiasRealizadas.php" method="POST" class="w-75 mx-auto">
  <div class="row g-3 align-items-center">
    
    <!-- Fecha -->
    <div class="col-12 col-md-auto d-flex align-items-center">
      <label for="fecha" class="col-form-label me-2 mb-0">Fecha:</label>
      <input 
        type="date" 
        id="fecha" 
        name="fecha" 
        class="input-select-custom w-100" 
        value="<?php echo date('Y-m-d');?>">
    </div>
    
    <!-- Sesión -->
    <div class="col-12 col-md-auto d-flex align-items-center">
      <label for="hora" class="col-form-label me-2 mb-0">Sesión:</label>
      <select 
        id="hora" 
        name="hora" 
        class="input-select-custom w-100"
      >
        <option value="" disabled selected>Selecciona una sesión</option>
        <?php foreach ($horasDisponibles as $hora): ?>
          <option value="<?= htmlspecialchars($hora[0]) ?>">
            <?= htmlspecialchars($hora[1]) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    
    <!-- Botón -->
    <div class="col-12 col-md-auto">
      <button 
        type="submit" 
        name="cargar_guardias" 
        id="cargar_guardias" 
        class="btn btn-primary w-100 w-md-auto"
        style="border:2px solid;background:linear-gradient(135deg,#1e3a5f,#0f1f2d);"
      >
        Ver mis Guardias
      </button>
    </div>
    
  </div>
</form>


</div>


<!-- Tabla responsiva -->
<?php if (!empty($_SESSION['historial']) && is_array($_SESSION['historial']) && empty($_SESSION['error'])): ?>
    <div class="table-responsive" style="padding:35px;">
  <table class="table table-guardias text-center align-middle">
    <thead class="table-dark">
      <tr>
        <th>Fecha</th>
        <th>Sesión</th>
        <th>Aula</th>
        <th>Grupo</th>
        <th>Asignatura</th>
        <th>Docente Ausente</th>
      </tr>
    </thead>
    <tbody>
    <?php 
    foreach ($_SESSION['historial'] as $registro): ?>
      <tr>
        <td><?= htmlspecialchars(date('d-m-Y', strtotime($registro[1] ?? '-'))); ?></td>
        <td><?= htmlspecialchars($registro[9] ?? '-'); ?></td>
        <td><?= htmlspecialchars($registro[6] ?? '-'); ?></td>
        <td><?= htmlspecialchars($registro[7] ?? '-'); ?></td>
        <td><?php echo htmlspecialchars($registro[8] ?? '-'); ?></td>
        <td><?= htmlspecialchars($registro[3] ?? '-'); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php else: ?>

  <div class="alert alert-info mt-4 text-center mx-auto" style="max-width: 600px;">
    No has realizado ninguna guardia todavía.
  </div>
<?php endif; ?>

</section>
…  

  <!-- 1) Bootstrap JS para dropdowns, collapse, etc -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


 <?php if (isset($_GET['auto']) && $_GET['auto'] === '1'): ?>
  <script>
    window.addEventListener('load', function() {
      document.getElementById('cargar_guardias').click();
    });
  </script>
<?php endif; ?>
</body>
<!--@section footer
Enlaces y derechos  -->
<footer class="bg-dark text-white py-4 mt-auto" style="background: linear-gradient(135deg, #0f1f2d, #18362f) !important;">
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
