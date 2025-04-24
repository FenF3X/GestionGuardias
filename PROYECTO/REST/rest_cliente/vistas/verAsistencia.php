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
<style>
  .table-responsive {
    max-width: 800px; /* ajusta el ancho máximo según prefieras */
    margin: 0 auto; /* centra la tabla horizontalmente */
  }

  .table th, .table td {
    white-space: nowrap; /* evita que el contenido se desborde en múltiples líneas */
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

<div class="container mt-5">
    <h2 class="mb-4">Consulta de asistencia del profesorado</h2>
    
    <form action="../resultadoAsistencia.php" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
            <label for="profesor">Seleccionar Profesor(opcional)</label>
                <select id="profesor" name="document" class="form-control">
                    <option value="">Seleccionar</option>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?php echo $profesor[0]; ?>"><?php echo htmlspecialchars($profesor[1]); ?></option>
                    <?php endforeach; ?>
                </select>            </div>
            <div class="col-md-6">
                <label for="tipoConsulta" class="form-label">Consultar por:</label>
                <select name="tipoConsulta" id="tipoConsulta" class="form-select" required>
                    <option value="" selected disabled>Selecciona una opción</option>
                    <option value="fecha">Fecha concreta</option>
                    <option value="mes">Mes completo</option>
                </select>
            </div>
        </div>

        <div class="row mb-3" id="inputFecha" style="display: none;">
            <div class="col">
                <label for="fecha" class="form-label">Fecha:</label>
                <input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>

        <div class="row mb-3" id="inputMes" style="display: none;">
            <div class="col">
                <label for="mes" class="form-label">Mes:</label>
                <input type="month" name="mes" id="mes" class="form-control" value="<?php echo date('Y-m'); ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Consultar asistencia</button>
    </form>
</div>
<?php if (isset($_SESSION['resultado_asistencia'])): ?>
    <hr class="my-5">
<h2 class="mb-4 mt-5 text-center">Resultado de la consulta</h2>

    <?php if (count($datosValidos) > 0): ?>
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped text-center">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>