<?php
session_start();
if (!isset($_SESSION['document'])) {
    header("Location: ../login.php");
    exit();
}
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
    <style>
      /* Personalización del botón hamburguesa */
      .navbar-toggler {
        background-color: #0f1f2d !important;
        border: 2px solid #fff !important;
      }
      .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='white' stroke-width='2' stroke-linecap='round' d='M4 7H26 M4 15H26 M4 23H26'/%3E%3C/svg%3E");
      }
      .navbar-toggler:hover {
        background-color: #18362f !important;
      }

      /* Ocultar scrollbar en WebKit */
      ::-webkit-scrollbar {
        display: none;
      }

      /* Hacer que el footer permanezca abajo */
      html, body {
        height: 100%;
        margin: 0;
      }
      body {
        display: flex;
        flex-direction: column;
      }
      main {
        flex: 1;
      }
      table.table-guardias thead tr th {
background: linear-gradient(135deg, #0f1f2d, #18362f) !important;
color: #fff !important;
}
/* ==============================
   Checkboxes custom con degradado
   ============================== */
   input[type="checkbox"] {
  -webkit-appearance: none;
  appearance: none;
  width: 1.2em;
  height: 1.2em;
  margin-right: 0.5em;
  border: 2px solid #1e3a5f;
  border-radius: 0.25em;
  background-color: transparent;
  cursor: pointer;
  position: relative;
  vertical-align: middle;
  transition: background 0.2s, border-color 0.2s;
}

input[type="checkbox"]:hover {
  border-color: #0f1f2d;
}

input[type="checkbox"]:checked {
  border-color: #0f1f2d;
  background: linear-gradient(135deg, #1e3a5f, #0f1f2d);
}

input[type="checkbox"]:checked::after {
  content: "";
  position: absolute;
  top: 2px;
  left: 6px;
  width: 4px;
  height: 8px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

    </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">
        <img src="../src/images/sinFondoDos.png" alt="Logo AsistGuard" class="logo-navbar">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
              aria-controls="navbarContent" aria-expanded="false">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link text-white" href="guardiasRealizadas.php?auto=1">Guardias Realizadas</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="../verAusencias.php?cargar_guardias=1">Consultar Ausencias</a></li>
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
