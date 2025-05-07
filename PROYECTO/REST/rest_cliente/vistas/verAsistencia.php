<?php
session_start();
include("../curl_conexion.php");
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}
$rol = $_SESSION['rol'] ?? '';
$nombre = $_SESSION['nombre'] ?? '';
$documento = $_SESSION['document'] ?? '';

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
$datosValidos = [];

if (isset($_SESSION['resultado_asistencia']) && is_array($_SESSION['resultado_asistencia'])) {
    $datosValidos = array_filter($_SESSION['resultado_asistencia'], function($registro) {
        return !empty($registro[0]) && $registro[0] !== 'N';
    });
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina principal de <?php echo htmlspecialchars($nombre); ?></title>
    <link rel="shortcut icon" href="../src/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../src/principal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<link rel="stylesheet" href="../src/asistencias.css">


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
      
     <!-- DERECHA: botones en línea -->
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
  <h2 class="mb-4">Consulta de asistencia del profesorado</h2>
  <form action="../resultadoAsistencia.php" method="POST">
    <div class="row g-3 align-items-end mb-3">
      
      <!-- PROFESOR -->
      <div class="col-12 col-md-6">
        <label for="profesor" class="form-label">Seleccionar Profesor (opcional)</label>
        <select id="profesor" name="document" class="input-select-custom w-100">
          <option value="">Seleccionar</option>
          <?php foreach ($profesores as $profesor): ?>
            <option value="<?= $profesor[0] ?>">
              <?= htmlspecialchars($profesor[1]) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <!-- TIPO DE CONSULTA -->
      <div class="col-12 col-md-6">
        <label for="tipoConsulta" class="form-label">Consultar por:</label>
        <select id="tipoConsulta" name="tipoConsulta" class="input-select-custom w-100" required>
          <option value="" selected disabled>Selecciona una opción</option>
          <option value="fecha">Fecha concreta</option>
          <option value="mes">Mes completo</option>
        </select>
      </div>
      
    </div>

    <!-- INPUTS dinámicos -->
    <div class="row g-3 mb-3" id="inputFecha" style="display: none;">
      <div class="col-12 col-md-6">
        <label for="fecha" class="form-label">Fecha:</label>
        <input type="date" name="fecha" id="fecha"
        class="input-select-custom w-100" value="<?= date('Y-m-d') ?>">
      </div>
    </div>

    <div class="row g-3 mb-3" id="inputMes" style="display: none;">
      <div class="col-12 col-md-6">
        <label for="mes" class="form-label">Mes:</label>
        <input type="month" name="mes" id="mes"
        class="input-select-custom w-100" value="<?= date('Y-m') ?>">
      </div>
    </div>

    <button type="submit" class="btn btn-primary"  style=" border: 2px solid; 
   background:linear-gradient(135deg, #0f1f2d, #18362f);">Consultar asistencia</button>
  </form>
</div>

<?php if (isset($_SESSION['resultado_asistencia'])): ?>
    <hr class="my-5">
<h2 class="mb-4 mt-5 text-center">Resultado de la consulta</h2>

    <?php if (count($datosValidos) > 0): ?>
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped text-center table-guardias">
            <thead class="table-dark">
                <tr>
                    <th>Docente</th>
                    <th>Fecha</th>
                    <th>Hora de inicio</th>
                    <th>Hora de fin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datosValidos as $registro): ?>
                    <tr>
                        <td><?= htmlspecialchars($registro[0] ?? '-') ?></td>
                        <td><?= htmlspecialchars(date('d-m-Y', strtotime($registro[1] ?? '-'))) ?></td>
                        <td><?= htmlspecialchars($registro[2] ?? '-') ?></td>
                        <td><?= htmlspecialchars($registro[3] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="container" style="max-width: 700px;">
  <div class="alert alert-info mt-3 text-center">
    <i class="bi bi-info-circle-fill"></i>
    No hay registros de asistencia para los filtros seleccionados.
    <strong>Ningún profesor está presente en esa fecha o mes.</strong>
  </div>
</div>

<?php endif; ?>
<?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script src="../src/calendar.js"></script>

<script>
document.getElementById('tipoConsulta').addEventListener('change', function () {
    const inputFecha = document.getElementById('inputFecha');
    const inputMes = document.getElementById('inputMes');
    
    inputFecha.style.display = 'none';
    inputMes.style.display = 'none';

    if (this.value === 'fecha') inputFecha.style.display = 'block';
    if (this.value === 'mes') inputMes.style.display = 'block';
});
</script>
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
 </body>
</html>