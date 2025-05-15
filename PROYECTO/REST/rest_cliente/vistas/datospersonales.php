<?php
/**

 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @link       http://localhost/GestionGuardias/PROYECTO/REST/rest_cliente/vistas/verInformes.php
 *
 * @function initSessionAndFetchProfesores
 * @description Inicia la sesión, valida autenticación de administrador y obtiene la lista de profesores.
 */
session_start();
include("../curl_conexion.php");
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}

/**
 * @var string $rol         Rol del usuario ('admin' o 'profesor').
 * @var string $nombre      Nombre a mostrar en la cabecera.
 * @var string $documento   Documento/ID del usuario.
 * @var array $profesores Conjunto de profesores para manejarlos en el select

 */
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
$documento = $_SESSION['document'];
$profesores = $_SESSION['profesores'] ?? [];

if ($rol !== 'admin') {
  header('Location: dashboard.php'); // Redirige si no es admin
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Configuración de usuarios</title>
  <link rel="shortcut icon" href="../src/images/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../src/guardias.css">
<link rel="stylesheet" href="../src/principal.css">
<style>
    html, body {
  height: 100%;
  margin: 0;
}

body {
  display: flex;
  flex-direction: column;
}

footer {
  flex-shrink: 0;
}

</style>
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

<main class="flex-grow-1">
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

    <section>
        <form action="../obtenerSesiones.php" id="busqueda"  method="POST">
            <div class="form-group">
                <label for="profesor">Seleccionar Profesor</label>
                <select id="profesor" name="document" class="input-select-custom w-100" required>
                    <option value="">Seleccione un profesor</option>
                    <?php foreach ($profesores as $profesor): ?>
                        <option value="<?php echo $profesor[0]; ?>"><?php echo htmlspecialchars($profesor[1]); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="form-group">
                <label for="fecha">Fecha de la Ausencia</label>
                <input type="date" id="fecha" name="fecha" class="input-select-custom w-100" required value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="motivo">Motivo de la Ausencia</label>
                <textarea id="motivo"
                 name="motivo" 
                 class="form-control" 
                 rows="4" 
                 style="background: linear-gradient(135deg, #1e3a5f, #0f1f2d);border: 2px solid;color:white;"
                 placeholder="Escriba el motivo de la ausencia"></textarea>
            </div>

            <button type="submit" class="btn btn-danger mt-3" style=" border: 2px solid; 
   background:linear-gradient(135deg, #0f1f2d, #18362f);">Buscar sesiones</button>
        </form>
    </section>
    <!--
    @section Footer
    Pie de página con derechos y redes sociales.
  -->
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