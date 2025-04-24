<?php
session_start();
include("../curl_conexion.php");
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
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

<!-- Formulario para registrar la ausencia -->
<main>
    <div class="container mt-5 form-container">
        <h3 class="mb-3 text-center">Registrar Ausencia de Profesor</h3>
        <form action="../obtenerSesiones.php" id="busqueda"  method="POST">
            <div class="form-group">
                <label for="profesor">Seleccionar Profesor</label>
                <select id="profesor" name="document" class="form-control" required>
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
                <textarea id="motivo" name="motivo" class="form-control" rows="4" placeholder="Escriba el motivo de la ausencia"></textarea>
            </div>

            <button type="submit" class="btn btn-danger mt-3">Buscar sesiones</button>
        </form>
    </div>
</main>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
