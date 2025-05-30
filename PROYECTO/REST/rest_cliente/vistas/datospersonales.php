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
$datosUsuario = null;
if (!empty($_GET['document'])) {
  $params = [
    'accion'   => 'obtenerUsuario',
    'document' => $_GET['document']
  ];
  $resp = curl_conexion(URL, 'POST', $params, [ "Authorization: Bearer " . ($_SESSION['token'] ?? '') ]);
  $datosUsuario = json_decode($resp, true);
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
.form-control::placeholder {
  color: #fff;
  opacity: 0.7; 
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

    <!-- 1) PERFIL + BOTÓN -->
    <div class="perfil-contenedor 
                d-flex flex-column flex-md-row 
                align-items-center justify-content-between mb-5">
      
      <!-- IZQUIERDA: foto + datos -->
      <div class="d-flex align-items-center mb-3 mb-md-0">
        <div class="foto-wrapper me-4">
          <img src="../src/images/default.jpg" alt="Foto de perfil" class="foto-circular">
        </div>
        <div class="info-usuario text-start">
          <p><strong>Documento:</strong> <?= htmlspecialchars($documento) ?></p>
          <p><strong>Nombre:</strong>     <?= htmlspecialchars($nombre) ?></p>
          <p><strong>Rol:</strong>        <?= htmlspecialchars($rol) ?></p>
        </div>
      </div>
      
      <!-- DERECHA: botón Chat -->
      <a href="chat.php" 
         class="btn btn-primary d-flex align-items-center justify-content-center"
         style="border:2px solid; background:linear-gradient(135deg, #1e3a5f, #0f1f2d);">
        <i class="bi bi-chat-dots-fill fs-4"></i>
        <span class="ms-2 d-none d-md-inline">Chat</span>
      </a>
      
    </div>
    
    <!-- 2) FORM CENTRALIZADO -->
    <div class="mx-auto" style="max-width:600px;">
    <form id="selectorUsuario" method="get" class="mb-4 text-center">
  <label for="document" class="form-label">Selecciona un usuario</label><br>
  <select name="document" id="document"
          class="input-select-custom w-100"
          style="max-width:300px;"
          onchange="this.form.submit()"
          required>
    <option value="">-- elige un profesor --</option>
    <?php foreach($profesores as $prof): ?>
      <option value="<?= htmlspecialchars($prof[0]) ?>"
        <?= (isset($_GET['document']) && $_GET['document']==$prof[0])?'selected':'' ?>>
        <?= htmlspecialchars($prof[1]) ?> (<?= $prof[0] ?>)
      </option>
    <?php endforeach; ?>
  </select>
</form>
    </div>
  </div>
</main>
<section>
    <?php if(isset($_GET["document"])): ?>
        <?php if ($_GET['document'] !== ''): ?>
    <?php foreach ($datosUsuario as $datoUsuario): ?>
    <div class="container">
    <form action="../actualizarDatos.php" method="post">
  <div class="row mb-4">
    <!-- Datos actuales (readonly) -->
    <div class="col-md-6">
      <h5>Datos actuales</h5>
      <div class="mb-3">
        <label for="current_nombre" class="form-label"><strong>Nombre:</strong></label>
        <input
          type="text"
          id="current_nombre"
          name="current_nombre"
          class="form-control"
          style="background-color: #0f1f2d;color: #fff;"
          value="<?= htmlspecialchars($datoUsuario[3]) ?>"
          readonly
        >
      </div>
      <div class="mb-3">
        <label for="current_documento" class="form-label"><strong>Documento:</strong></label>
        <input
          type="text"
          id="current_documento"
          name="current_documento"
          class="form-control"
          style="background-color: #0f1f2d;color: #fff;"
          value="<?= htmlspecialchars($datoUsuario[0]) ?>"
          readonly
        >
      </div>
      <div class="mb-3">
        <label for="current_rol" class="form-label"><strong>Rol:</strong></label>
        <input
          type="text"
          id="current_rol"
          name="current_rol"
          class="form-control"
          style="background-color: #0f1f2d;color: #fff;"
          value="<?= htmlspecialchars($datoUsuario[2]) ?>"
          readonly
        >
      </div>
      
    </div>

    <!-- Datos nuevos (editable) -->
<div class="col-md-6">
  <h5>Datos nuevos</h5>
  <div class="mb-3">
    <label for="new_nombre" class="form-label"><strong>Nombre:</strong></label>
    <input
      type="text"
      id="new_nombre"
      name="new_nombre"
      class="form-control"
      style="background-color: rgb(40, 78, 112);color: #fff;"
      placeholder="Nuevo nombre"
    >
  </div>
  <div class="mb-3">
    <label for="new_rol" class="form-label"><strong>Rol:</strong></label>
    <input
      type="text"
      id="new_rol"
      name="new_rol"
      class="form-control"
      style="background-color: rgb(40, 78, 112);color: #fff;"
      placeholder="Nuevo rol (admin o profesor)"
    >
  </div>

  <!-- Nueva fila de contraseñas -->
  <div class="row">
  <div class="col-md-6 mb-3">
    <label for="new_password" class="form-label"><strong>Contraseña nueva:</strong></label>
    <div class="input-group">
      <input
        type="password"
        id="new_password"
        name="new_password"
        class="form-control"
        style="background-color: rgb(40, 78, 112); color: #fff;"
      >
      <span
        class="input-group-text toggle-password"
        data-target="new_password"
        style="cursor: pointer; background-color: rgb(40, 78, 112); color: #fff; border-left: none;"
        title="Mostrar / Ocultar contraseña"
      >
        <i class="bi bi-eye"></i>
      </span>
    </div>
  </div>
  <div class="col-md-6 mb-3">
    <label for="confirm_password" class="form-label"><strong>Confirmar contraseña:</strong></label>
    <div class="input-group">
      <input
        type="password"
        id="confirm_password"
        name="confirm_password"
        class="form-control"
        style="background-color: rgb(40, 78, 112); color: #fff;"
      >
      <span
        class="input-group-text toggle-password"
        data-target="confirm_password"
        style="cursor: pointer; background-color: rgb(40, 78, 112); color: #fff; border-left: none;"
        title="Mostrar / Ocultar contraseña"
      >
        <i class="bi bi-eye"></i>
      </span>
    </div>
  </div>
</div>
</div>

  </div>
  <input type="hidden" name="document" value="<?= htmlspecialchars($datoUsuario[0]) ?>">
<input type="submit" onclick="return enviarForm()" value="Actualizar" name="actualizar" class="btn btn-primary" style="background:linear-gradient(135deg, #0f1f2d, #18362f); border:2px solid;">
  </form>
</div>

        <?php endforeach; ?>
       <?php else: ?>
      <div class="container">
        
        <p class="text-danger text-center" style="font-size: 18px;"><i class="bi bi-exclamation-triangle-fill text-warning"></i>
            Debe seleccionar un profesor</p>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Mensaje</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: linear-gradient(135deg, #0f1f2d, #18362f)">Cerrar</button>
      </div>
    </div>
  </div>
</div>

</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  function showMessageModal(mensaje, titulo = 'Mensaje') {
    document.getElementById('messageModalLabel').textContent = titulo;
    document.querySelector('#messageModal .modal-body').textContent = mensaje;
    const modalEl = document.getElementById('messageModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  }

  function enviarForm() {
    const newPassword     = document.getElementById("new_password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const newRol          = document.getElementById("new_rol").value.trim();
    const newNombre       = document.getElementById("new_nombre").value.trim();

    if (newPassword !== confirmPassword) {
      showMessageModal("Las contraseñas no coinciden.", "Error de contraseña");
      return false;
    }
    if (newNombre === "") {
      showMessageModal("El nombre no puede estar vacío.", "Error de nombre");
      return false;
    }
    if (newPassword === "" || confirmPassword === "") {
      showMessageModal("La contraseña no puede estar vacía.", "Error de contraseña");
      return false;
    }
    if (newRol !== "admin" && newRol !== "profesor") {
      showMessageModal("El rol debe ser 'admin' o 'profesor'.", "Error de rol");
      return false;
    }

    return true;
  }

  document.querySelectorAll('.toggle-password').forEach(el => {
    el.addEventListener('click', function(){
      const input = document.getElementById(this.dataset.target);
      const isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      this.innerHTML = isPass
        ? '<i class="bi bi-eye-slash"></i>'
        : '<i class="bi bi-eye"></i>';
    });
  });
</script>

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