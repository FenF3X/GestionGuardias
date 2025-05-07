<?php
session_start();
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
$documento = $_SESSION['document'];
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
if (isset($_SESSION['registro_exitoso']) && $_SESSION['registro_exitoso']) {
  unset($_SESSION['registro_exitoso']);
  $mostrarModal = true; 
} else {
  $mostrarModal = false;
}
unset($_SESSION['mensaje']); 
if (isset($_SESSION["alertSinSesiones"])) {
  unset($_SESSION["sesiones_hoy"]);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina principal de <?php echo htmlspecialchars($nombre); ?></title>
    <link rel="shortcut icon" href="../src/images/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../src/principal.css">
<link rel="stylesheet" href="../src/dashboard.css">
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



    <?php if ($mensaje): ?>
      <div class="alert-container">
        <div class="alert alert-<?php echo htmlspecialchars($mensaje['type']); ?> text-center" id="mensajeAlert">
          <?php echo htmlspecialchars($mensaje['text']); ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>


<section>
<?php if (!empty($_SESSION["sesiones_hoy"])): ?>
  <div class="container mt-4">
    <h4 class="mb-3">Sesiones de hoy</h4>

    <!-- Tabla responsiva -->
    <div class="table-responsive">
      <table class="table text-center align-middle table-guardias">
        <thead class="table-dark">
          <tr>
            <th>Hora</th>
            <th>Día</th>
            <th>Aula</th>
            <th>Grupo</th>
            <th>Asignatura</th>
            <th>Sesion</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($_SESSION["sesiones_hoy"] as $sesion): ?>
            <tr>
              <td><?= htmlspecialchars($sesion[1] . ' - ' . $sesion[2]) ?></td>
              <td><?= htmlspecialchars($sesion[0] ?? '-') ?></td>
              <td><?= htmlspecialchars($sesion[5] ?? '-') ?></td>
              <td><?= htmlspecialchars($sesion[4] ?? '-') ?></td>
              <td><?= htmlspecialchars($sesion[3] ?? '-') ?></td>
              <td><?= htmlspecialchars($sesion[6] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <br>
      <div class="d-flex justify-content-center">
        <form action="../fichar.php" method="POST">
          <button 
          class="btn btn-primary mx-3 w-auto" 
          name="fentrada" 
          id="fentrada"
          style="background:linear-gradient(135deg, #1e3a5f, #0f1f2d); border:0;"
          >Fichar entrada</button>
        </form>
        <form action="../fichar.php" method="POST">
          <button 
          class="btn btn-danger mx-3 w-auto" 
          name="fsalida" 
          id="fsalida"
          style="background:linear-gradient(135deg, #1e3a5f, #0f1f2d); border:0;"
          >Fichar salida</button>
        </form>
      </div>
    </div>
  </div>
<?php else: ?>
  
  <!-- Mostrar alerta si no hay sesiones -->
  <div class="alert alert-info mt-4 text-center mx-auto" style="max-width: 600px;">
    No tienes sesiones asignadas para hoy.
  </div>
<?php endif; ?>
</section>
<!-- Modal de confirmación -->
<div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalConfirmacionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalConfirmacionLabel">Registro realizado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        El registro de ausencia se ha realizado satisfactoriamente.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border: 2px solid; background: linear-gradient(135deg, #1e3a5f, #0f1f2d);">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<?php if ($mostrarModal): ?>
    <!-- Mostrar el modal automáticamente después de 5 segundos -->
    <script>
        // Mostrar el modal después de 5 segundos
        setTimeout(function() {
            var myModal = new bootstrap.Modal(document.getElementById("modalConfirmacion"));
            myModal.show();
        }, 1000); // 5000 milisegundos = 5 segundos
    </script>
<?php endif; ?>
<script src="../src/app.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para eliminar el mensaje después de 5 segundos -->
<script>
    setTimeout(function() {
        var alertElement = document.getElementById('mensajeAlert');
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }, 5000); 

    document.getElementById('registrarAusencia').addEventListener('click', function(event) {
      const params = {
        accion: 'consultaProfes' // La acción que se va a realizar
    };
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