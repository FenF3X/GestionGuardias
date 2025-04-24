<?php
session_start();
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
$documento = $_SESSION['document'];
$fecha = $_SESSION["fechaAusencia"];
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
<?php if (!empty($_SESSION["sesiones_profesor"])): ?>
  <div class="container mt-4">
  <h4 class="mb-3">Sesiones del <?php echo htmlspecialchars($fecha); ?></h4>

  <!-- Mostrar horas seleccionadas en una fila -->
  <div class="row mb-3">
  <form action="../guardarAusencia.php" method="POST">
    <!-- Campos ocultos para enviar las horas -->
<input type="hidden" name="hora_inicio" id="hora_inicio">
<input type="hidden" name="hora_fin" id="hora_fin">


  <!-- Tabla responsiva -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>Hora</th>
          <th>Día</th>
          <th>Aula</th>
          <th>Grupo</th>
          <th>Asignatura</th>
          <th>Sesion</th>
          <th>Seleccionar</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($_SESSION["sesiones_profesor"] as $index => $sesion): ?>
          <tr id="fila-<?php echo $index; ?>">
            <td><?= htmlspecialchars($sesion[1] . ' - ' . $sesion[2]) ?></td>
            <td><?= htmlspecialchars($sesion[0] ?? '-') ?></td>
            <td><?= htmlspecialchars($sesion[5] ?? '-') ?></td>
            <td><?= htmlspecialchars($sesion[4] ?? '-') ?></td>
            <td><?= htmlspecialchars($sesion[3] ?? '-') ?></td>
            <td><?= htmlspecialchars($sesion[6] ?? '-') ?></td>
            <td>
            <input type="checkbox" class="checkboxSesion" name="sesiones[]" value="<?php echo htmlspecialchars(json_encode($sesion)); ?>">
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="form-check">
          <input type="checkbox" class="form-check-input" id="jornada_completa" name="jornada_completa">
          <label class="form-check-label" for="jornada_completa">Seleccionar todas las sesiones</label>
        </div>
    <br>
    <div class="d-flex justify-content-center">
      
        <button class="btn btn-primary mx-3 w-auto" name="guardarAusencia" id="guardarAusencia">Registrar Ausencia</button>
      </form>
      <a href="dashboard.php" class="btn btn-danger mx-3 w-auto">Volver</a>
    </div>
  </div>
</div>

<?php else: ?>
  <div class="alert alert-info mt-4 text-center mx-auto" style="max-width: 600px;">
    Hoy no tienes sesiones asignadas.
  </div>
  <?php endif; ?>
</div>
  <script src="../src/ausencias.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
