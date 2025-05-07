<?php
/**
 * consultaAusencias.php
 *
 * Página de consulta de las Ausencias de los profesores por día actual. Preparado para horas de guardia
 * para el docente 
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @link       http://localhost/GestionGuardias/PROYECTO/REST/rest_cliente/vistas/consultaAusencias.php
 */
session_start();
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
$documento = $_SESSION['document'];

/**
 * @var string $rol      - Rol del usuario actual (p.ej. 'admin').
 * @var string $nombre   - Nombre del usuario mostrado en cabecera.
 * @var string $documento- Documento o ID del usuario.
 */


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
    <link rel="stylesheet" href="../src/principal.css">
    <link rel="stylesheet" href="../src/guardias.css">


</head>
<body class="d-flex flex-column min-vh-100">
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
        <li class="nav-item"><a id="link-cargar-guardias" class="nav-link text-white" href="../verAusencias.php?cargar_guardias=1">Consultar Ausencias</a>


        </li>
      <!-- SOLO ADMIN -->

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
 <!--
    @section Carga de Guardias
    Botón para recargar guardias pendientes mediante GET.
  -->
<section>
    <div class="d-flex justify-content-center mb-3">
        <form action="../verAusencias.php" method="GET">
            <button type="submit" 
            name="cargar_guardias" 
            class="btn btn-primary"
            style="background:linear-gradient(135deg, #1e3a5f, #0f1f2d);border:0;">Cargar Guardias</button>
        </form>
    </div>
    <?php if (!empty($_SESSION["guardiasPen"]) && isset($_SESSION["guardiasPen"])): ?>
        <div class="container mt-4">
            <h4 class="mb-3">Guardias pendientes de hoy (<?php echo htmlspecialchars(date('d-m-Y')); ?>):</h4>
            
            <!-- Formulario para cargar guardias alineado a la derecha -->
            <div class="d-flex justify-content-end mb-3">
                
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Sesión</th>
                            <th>Aula</th>
                            <th>Grupo</th>
                            <th>Asignatura</th>
                            <th>Docente Ausente</th>
                            <th>Guardia asignada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION["guardiasPen"] as $index => $sesion): ?>
                            <tr id="fila-<?php echo $index; ?>">
                                <td><?php echo htmlspecialchars($sesion[0] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($sesion[1] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($sesion[2] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($sesion[3] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($sesion[5] ?? '-'); ?></td>
                                <td>
    <?php if ($sesion[6] == 0): ?>
        <span class="badge bg-danger d-inline-flex align-items-center me-2">Pendiente</span>
        <button 
  class="badge bg-warning btn-sm w-auto w-sm-100" 
  style="border:0;"
  data-bs-toggle="modal" 
  data-bs-target="#coverGuardModal" 
  data-sesion="<?php echo htmlspecialchars('Sesion: ' . $sesion[0] . ' - Aula: ' . $sesion[1] . ' - Grupo: ' . $sesion[2] . '  Asignatura: ' . $sesion[3]); ?>"
  data-sesion-id="<?php echo htmlspecialchars($sesion[0]); ?>"
  data-document="<?php echo htmlspecialchars($sesion[4]); ?>">
    Cubrir Guardia
</button>

    <?php else: ?>
        <span class="badge bg-success d-inline-flex align-items-center me-2">Asignado</span>
    <?php endif; ?>
</td>


                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
  <div class="alert alert-info mt-4 text-center fw-bold d-flex align-items-center justify-content-center gap-2" style="max-width: 400px; margin: 0 auto;">
    <i class="bi bi-exclamation-triangle-fill"></i>
    No hay guardias pendientes
  </div>
<?php endif; ?>


<!--@section Modal Cobertura
      Modal de Bootstrap para confirmar cobertura de guardia.-->
      <div class="modal fade" id="coverGuardModal" tabindex="-1" aria-labelledby="coverGuardModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="coverGuardModalLabel">Confirmar cobertura de la guardia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="sesion-info"></p> <!-- Aquí se mostrará la sesión que se va a cubrir -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style=" border: 2px solid; 
   background:linear-gradient(135deg, #1e3a5f, #0f1f2d);">Cancelar guardia</button>
        <form action="../verAusencias.php" method="POST" class="d-inline">
  <input type="hidden" id="sesion_id_input" name="sesion_id" value="">
  <input type="hidden" id="document_input" name="document" value="">
  <input type="hidden" name="asignar" value="1">
  <input type="hidden" name="redirigir" value="1"> <!-- para saber que después debe redirigir -->
  <button type="submit" class="btn btn-primary" id="confirm-cover" style=" border: 2px solid; 
   background:linear-gradient(135deg, #0f1f2d, #18362f);">Asignarme guardia</button>
</form>



        </div>
    </div>
  </div>
</div>

</section>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <div class="position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 1050; width: 100%; max-width: 500px;">
    <div class="alert alert-primary text-center fw-bold mb-0" role="alert">
      Guardia asignada correctamente. Pulsa 👇 para ver los cambios.
    </div>
  </div>
<?php endif; ?>

<script src="../src/guardias.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!--
    @section Footer
    Pie de página con derechos y redes sociales.
  -->
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
</body>
</html>
