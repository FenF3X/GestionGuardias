<?php
/**
 * verResultados.php
 *
 * Muestra los resultados de la consulta de ausencias generada en verInformes.php,
 * con opción de exportar a PDF o volver atrás para cambiar filtros.
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @link       http://localhost/GestionGuardias/PROYECTO/REST/rest_cliente/vistas/verResultados.php
 * @warning    **Atención:** Este script solo funciona si viene redireccionado desde verInformes.php
 *
 * @function initSession
 * @description Inicia la sesión y valida que el usuario esté autenticado.
 */
session_start();

// Validar sesión activa
if (!isset($_SESSION['document'])) {
    header("Location: ../login.php");
    exit();
}

/**
 * @var string $rol       Rol del usuario ("admin" o "profesor").
 * @var string $nombre    Nombre para mostrar en la cabecera.
 * @var string $documento ID/documento del usuario.
 */
$rol        = $_SESSION['rol'];
$nombre     = $_SESSION['nombre'];
$documento  = $_SESSION['document'];

// Obtener resultados e informe del filtro anterior
/**
 * @var array $resultados       Array de filas del informe obtenido.
 * @var string $tipo            Tipo de informe (día, mes, trimestre, etc.).
 */
$resultados = $_SESSION['resultado_informe'] ?? [];
$tipo        = $_SESSION['tipo_informe']   ?? 'informe';

// Si no hay datos o formato incorrecto, mostrar alerta y detener
if (empty($resultados) || !isset($resultados[0]) || !is_array($resultados[0])) {
    echo "<div class='alert alert-warning text-center w-50 mx-auto my-5'>⚠️ No hay datos disponibles para mostrar.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Resultados del Informe</title>
  <link rel="shortcut icon" href="../src/images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="../src/principal.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../src/informe.css">
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

<div class="container mt-5 tabla2PDF">

<div class="mb-4 text-center d-flex justify-content-center align-items-center" id="tituloInforme">
  <img id="iconoInforme" src="../src/images/icono-informe.png" alt="Informe" style="height: 24px; margin-right: 8px;">
  <span style="font-size: 20px;">Informe filtrado por <?= htmlspecialchars($tipo) ?></span>
  <input type="hidden" name="tipo" id="tipo" value="<?=$tipo?>">
</div>


  
  <div class="table-responsive ">
    <table class="table table-bordered table-striped text-center align-middle table-guardias">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Profesor ausente</th>
            <th>Profesor guardia</th>
            <th>Aula</th>
            <th>Grupo</th>
            <th>Asignatura</th>
            <th>Sesion</th>
            <th>Dia</th>
            <th>Hora Inicio -- Hora fin</th>
            <th>Guardias totales</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($resultados as $fila): ?>
            <tr>
                <?php foreach ($fila as $valor): ?>
                    <td><?= htmlspecialchars($valor) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
  </div>

  
</div>
<div class="mt-4 text-center">
    <a href="verInformes.php" class="btn btn-secondary">⬅️ Volver</a>
  </div>
<!-- Botón flotante para exportar a PDF -->
<button id="exportarPDF"
        class="btn btn-danger position-fixed"
        style="
          right: 20px;
          bottom: 20px;
          z-index: 1000;
          border: 2px solid;
          background: linear-gradient(135deg, #1e3a5f, #0f1f2d);
        ">
  <i class="bi bi-file-earmark-pdf-fill"></i> Exportar a PDF
</button>
<img id="footerLogo" src="../src/images/logoenUno.png" alt="Imagen PDF" style="display:none; max-width: 250px;">

  <!-- 1) Bootstrap Bundle (Popper + Collapse, Dropdowns, etc) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script src="../src/exportarAPDF.js"></script>
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