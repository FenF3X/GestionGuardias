<?php
/**
 * horarioAusente.php
 *
 * Permite al docente seleccionar las sesiones del día para registrar la ausencia del profesor referente.
 * Muestra tabla de sesiones con checkboxes y un botón para guardar la ausencia.
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @link       http://localhost/GestionGuardias/PROYECTO/REST/rest_cliente/vistas/horarioAusente.php
 * @warning **Atención:** Este enlace solo funciona cuando viene redireccionado de registroAusencias
 */

/**
 * @function initSession
 * @description Inicia la sesión y valida que el usuario esté autenticado.
 */
session_start();
if (!isset($_SESSION['document'])) {
    header("Location: ../login.php");
    exit();
}
/**
 * @var string $rol       - Rol del usuario actual (e.g., 'profesor', 'admin').
 * @var string $nombre    - Nombre del usuario para mostrar en cabecera.
 * @var string $fecha     - Fecha de la ausencia a registrar, tomada de sesión.
 */
$rol       = $_SESSION['rol'];
$nombre    = $_SESSION['nombre'];
$fecha     = $_SESSION["fechaAusencia"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal de <?= htmlspecialchars($nombre) ?></title>
    <link rel="shortcut icon" href="../src/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../src/principal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../src/ausencias.css">
</head>
<body>
    <!-- @section Navbar -->
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
          <!-- LOGO -->

      <a class="navbar-brand" href="dashboard.php">
        <img src="../src/images/sinFondoDos.png" alt="Logo AsistGuard" class="logo-navbar">
      </a>
          <!-- BOTÓN HAMBURGUESA -->

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
              aria-controls="navbarContent" aria-expanded="false">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link text-white" href="guardiasRealizadas.php?auto=1">Guardias Realizadas</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="../verAusencias.php?cargar_guardias=1">Consultar Ausencias</a></li>
          
                <!-- SOLO ADMIN -->

          <?php if ($rol === 'admin'): ?>
            <li class="nav-item"><a class="nav-link text-white" href="verInformes.php">Generar informes</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">Gestión de asistencia</a>
              <ul class="dropdown-menu dropdown-hover">
                <li><a class="dropdown-item" href="verAsistencia.php">Consultar asistencia</a></li>
                <li><a class="dropdown-item" href="registroAusencias.php">Registrar Ausencia</a></li>
              </ul>
            </li>
          <?php endif; ?>
        </ul>
              <!-- BIENVENIDA + LOGOUT A LA DERECHA -->
        <div class="d-flex align-items-center ms-auto">
          <span class="text-white me-3"><strong>Bienvenid@ <?= htmlspecialchars($nombre) ?></strong></span>
          <form method="POST" action="../logout.php" class="mb-0">
            <button class="btn btn-sm btn-outline-light"
                    style="background: linear-gradient(135deg, #1e3a5f, #0f1f2d);"
                    title="Cerrar sesión">
              <i class="bi bi-box-arrow-right"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </nav>

  <main>
      <!-- @section main
       Muestra las sesiones del profesor de ese dia seleccionado anteriormente en registroAusencias -->

    <?php if (!empty($_SESSION["sesiones_profesor"])): ?>
      <div class="container mt-4">
        <h4 class="mb-3">Sesiones del <?= htmlspecialchars($fecha) ?></h4>

        <form action="../guardarAusencia.php" method="POST">
          <!-- Campos ocultos para las horas -->
          <input type="hidden" name="hora_inicio" id="hora_inicio">
          <input type="hidden" name="hora_fin" id="hora_fin">

          <div class="table-responsive mb-3">
            <table class="table table-bordered table-striped text-center align-middle table-guardias">
              <thead class="table-dark">
                <tr>
                  <th>Hora</th>
                  <th>Día</th>
                  <th>Aula</th>
                  <th>Grupo</th>
                  <th>Asignatura</th>
                  <th>Sesión</th>
                  <th>Seleccionar</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION["sesiones_profesor"] as $index => $sesion): ?>
                  <tr>
                    <td><?= htmlspecialchars($sesion[1] . ' - ' . $sesion[2]) ?></td>
                    <td><?= htmlspecialchars($sesion[0] ?? '-') ?></td>
                    <td><?= htmlspecialchars($sesion[5] ?? '-') ?></td>
                    <td><?= htmlspecialchars($sesion[4] ?? '-') ?></td>
                    <td><?= htmlspecialchars($sesion[3] ?? '-') ?></td>
                    <td><?= htmlspecialchars($sesion[6] ?? '-') ?></td>
                    <td>
                      <input type="checkbox" class="checkboxSesion" name="sesiones[]"
                             value="<?= htmlspecialchars(json_encode($sesion)) ?>">
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="form-check mb-4">
            <input type="checkbox" class="form-check-input" id="jornada_completa" name="jornada_completa">
            <label class="form-check-label" for="jornada_completa">Seleccionar todas las sesiones</label>
          </div>

          <div class="d-flex justify-content-center">
            <button class="btn btn-primary mx-3 w-auto" name="guardarAusencia" id="guardarAusencia" style="background: linear-gradient(135deg, #1e3a5f, #0f1f2d); border:2px solid;">
              Registrar Ausencia
            </button>
            <a href="registroAusencias.php" class="btn btn-danger mx-3 w-auto" style="background: linear-gradient(135deg, #1e3a5f, #0f1f2d); border:2px solid;">Volver</a>
          </div>
        </form>
      </div>
    <?php else: ?>
      <div class="alert alert-info mt-4 text-center mx-auto" style="max-width: 600px;">
        Hoy no tienes sesiones asignadas.
      </div>
    <?php endif; ?>
  </main>
<!--@section footer
Enlaces y derechos  -->
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

  <script src="../src/ausencias.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
