<?php
session_start();
include("../curl_conexion.php");
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}
$rol = $_SESSION['rol'] ?? [];
$nombre = $_SESSION['nombre'] ?? [];
$documento = $_SESSION['document'] ?? [];

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
    <link rel="stylesheet" href="../src/principal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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

      <style>
  .dropdown-menu li {
    color: white;
    font-weight: bold;
  }

  .dropdown-menu .dropdown-item {
    color: white !important;
    font-weight: bold;
    background-color: transparent !important;
    transition: color 0.3s ease;
  }

  .dropdown-menu .dropdown-item:hover {
    background-color: transparent !important;
    color: #d0f0ff !important; /* blanco azulado más claro */
  }

  .dropdown:hover .dropdown-menu {
    display: block;
    background: linear-gradient(135deg, #0f1f2d, #18362f);
  }
</style>



      <!-- BIENVENIDA + LOGOUT A LA DERECHA -->
      <div class="d-flex align-items-center ms-auto">
        <span class="text-white me-3"><strong>Bienvenid@ <?= htmlspecialchars($nombre); ?></strong></span>
        <form method="POST" action="../logout.php" class="mb-0">
          <button class="btn btn-sm btn-danger" title="Cerrar sesión">
            <i class="bi bi-box-arrow-right"></i>
          </button>
        </form>
      </div>

    </div>
  </div>
</nav>
<main>
    <div class="container mt-5 d-flex justify-content-start align-items-center perfil-contenedor">
        <div class="foto-wrapper me-4">
            <img src="../src/images/default.jpg" alt="Foto de perfil" class="foto-circular">
        </div>
        <div class="info-usuario text-start">
            <p><strong>Documento:</strong> <?php echo htmlspecialchars($documento); ?></p>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
            <p><strong>Rol:</strong> <?php echo htmlspecialchars($rol); ?></p>
        </div>
    </div>
</main>

<section>
<div class="container mt-4">
<h4 class="mb-3">Guardias realizadas</h4>
<div class="d-flex justify-content-center mb-4">
    <form action="../verGuardiasRealizadas.php" method="POST" class="w-75">
        <div class="d-flex justify-content-between gap-3">
            <div class="flex-fill">
                <label for="fecha" class="form-label">Fecha:</label>
                
                <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo date('Y-m-d');?>">
                </div>
            <div class="flex-fill">
                <label for="hora" class="form-label">Sesión:</label>
                <select id="hora" name="hora" class="form-select">
                    <option value="" disabled selected>Selecciona una sesión</option>
<?php
foreach ($horasDisponibles as $hora) {
    $id = $hora[0];
    $texto = $hora[1]; 
    echo '<option value="' . htmlspecialchars($id) . '">' . htmlspecialchars($texto) . '</option>';
}
?>
</select>

            </div>
            <div class="d-flex align-items-end">
                <button type="submit" name="cargar_guardias" id="cargar_guardias" class="btn btn-primary w-100">Ver mis Guardias</button>
            </div>
        </div>
    </form>
</div>


<!-- Tabla responsiva -->
<?php if (isset($_SESSION['historial']) && is_array($_SESSION['historial']) && empty($_SESSION['error'])): ?>
    <div class="table-responsive">
  <table class="table table-bordered table-striped text-center align-middle">
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
<script>
window.onload = function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('auto') === '1') {
        document.getElementById('cargar_guardias').click();
    }
}
</script>


</script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
