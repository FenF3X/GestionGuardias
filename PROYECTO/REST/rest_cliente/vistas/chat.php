<?php
// chat.php
date_default_timezone_set('Europe/Madrid');
session_start();
include('../curl_conexion.php');  // Función curl_conexion(URL, método, params)

// 1) Verificar usuario logueado
$rol      = $_SESSION['rol'] ?? null;
$nombre   = $_SESSION['nombre'] ?? null;
$document = $_SESSION['document'] ?? null;
if (!$document) {
  header('Location: login.php');
  exit;
}

// 2) Obtener contactos escritos
$params = ['accion' => 'consultaProfesEscritos', 'documento' => $document];
$resp = curl_conexion(URL, 'POST', $params);
$profesoresEscritos = json_decode($resp, true);


// 3) Obtener lista completa de profesores
$params = ['accion' => 'consultaProfesMensaje', 'documento' => $document];
$resp = curl_conexion(URL, 'POST', $params);
$profesores = json_decode($resp, true);

// 4) Determinar profesor actual (GET o primer elemento)
$profNombre = $_GET['profesor'] ?? $profesores[0][1] ?? null;
if (!$profNombre) {
  echo '<p>No hay profesores disponibles.</p>';
  exit;
}

// 5) Buscar datos del profesor actual
$profActual = null;
$profesorId = null;
foreach ($profesores as $prof) {
  if ($prof[1] === $profNombre) {
    $profActual = $prof;
    $profesorId = $prof[0];
    break;
  }
}
if (!$profActual) {
  $profActual = $profesores[0];
  $profesorId = $profesores[0][0];
  $profNombre = $profesores[0][1];
}

// 6) Enviar mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['mensaje'])) {
  $contenido = trim($_POST['mensaje']);
  $params = [
    'accion'          => 'enviaMensaje',
    'emisor'          => $document,
    'nombreEmisor'    => $nombre,
    'receptor'        => $_POST['receptor'],
    'nombreReceptor'  => $_POST['nombreReceptor'],
    'mensaje'         => $contenido
  ];
  curl_conexion(URL, 'POST', $params);
  header('Location: chat.php?profesor=' . urlencode($_POST['nombreReceptor']));
  exit;
}

// 7) Cargar mensajes
$params = [
  'accion'   => 'consultaMensajes',
  'emisor'   => $document,
  'receptor' => $profesorId
];
$resp = curl_conexion(URL, 'POST', $params);
$mensajes = json_decode($resp, true) ?: [];
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Chat con <?= htmlspecialchars($profActual[1] ?? 'Profesor') ?></title>
  <link rel="shortcut icon" href="../src/images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="../src/principal.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .chat-window { height: 70vh; }
    .msg { max-width: 75%; word-wrap: break-word; }
    .from-me { margin-left: auto; }
    .from-them { margin-right: auto; }
    /* Estilos para la lista de contactos custom */
    .list-group {
      max-height: 300px;
      overflow-y: auto;
    }
    
    .list-group-item {
      white-space: normal;
      padding: 0.75rem 1rem;
    }
    /* Contenedor relativo para el dropdown manual */
.manual-dropdown {
  position: relative;
  display: inline-block;
}
/* Oculta el menú por defecto */
.manual-dropdown .dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  display: none;
  margin-top: 0.5rem;  
  background: linear-gradient(135deg, #0f1f2d, #18362f);

}
/* Cuando tenga la clase .show, se muestra */
.manual-dropdown .dropdown-menu.show {
  display: block;
}

  </style>
</head>
<body>
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
      <a class="navbar-brand" href="dashboard.php">
        <img src="../src/images/sinFondoDos.png" alt="Logo AsistGuard" class="logo-navbar">
      </a>
      <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link text-white" href="guardiasRealizadas.php?auto=1">Guardias Realizadas</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="../verAusencias.php?cargar_guardias=1">Consultar Ausencias</a></li>
          <?php if ($rol === 'admin'): ?>
            <li class="nav-item"><a class="nav-link text-white" href="verInformes.php">Generar informes</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">Gestión de asistencia</a>
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
            <button class="btn btn-sm btn-danger" title="Cerrar sesión"><i class="bi bi-box-arrow-right"></i></button>
          </form>
        </div>
      </div>
    </div>
  </nav>

  <!-- MAIN CHAT -->
  <main>
    <div class="container mt-5">
      <div class="perfil-contenedor d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
          <div class="foto-wrapper me-4">
            <img src="../src/images/default.jpg" alt="Foto de perfil" class="foto-circular">
          </div>
          <div class="info-usuario text-start">
            <p><strong>Documento:</strong> <?= htmlspecialchars($document) ?></p>
            <p><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
            <p><strong>Rol:</strong> <?= htmlspecialchars($rol) ?></p>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- CONTACTOS -->
        <div class="col-md-3 mb-3">
        <?php $tamaño = 14?>
          <?php if (!empty($profesoresEscritos)): ?>
            <h5>Mis mensajes</h5>
            <div class="list-group">
              <?php $tamaño = 6?>
              <?php foreach ($profesoresEscritos as $prof): 
                // Ahora cada $prof es un array con:
                // 0 => interlocutor_id
                // 1 => interlocutor_nombre
                // 2 => mensaje
                // 3 => fecha
                // 4 => hora
                $name    = htmlspecialchars($prof[1]);   
                $msg     = $prof[2]  ?? 'Sin mensajes';   
                $preview = mb_strimwidth($msg, 0, 30, '...');
                $active  = ($name === $profNombre) ? ' active' : '';
                $url     = 'chat.php?profesor=' . urlencode($name);
              ?>
                <a href="<?= $url ?>"
                   class="list-group-item list-group-item-action<?= $active ?>">
                  <div class="fw-semibold"><?= $name ?></div>
                  <small class="text-muted"><?= $preview ?></small>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <h5 class="mt-4">Otros Contactos</h5>
          <form method="get" id="frmProf2">
            <select name="profesor" class="form-select" size="<?= $tamaño ?>" onchange="frmProf2.submit()">
              <?php foreach ($profesores as $prof): ?>
              <option value="<?= htmlspecialchars($prof[1]) ?>" <?= ($prof[1] === $profNombre ? 'selected' : '') ?>>
                <?= htmlspecialchars($prof[1]) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </form>
        </div>

        <!-- VENTANA CHAT -->
        <div class="col-md-9 d-flex flex-column">
        <div class="border rounded p-3 mb-3 d-flex justify-content-between align-items-center">
  <strong>Chat con <?= htmlspecialchars($profActual[1]) ?></strong>

  <?php if(is_array($mensajes)) : ?>
  <div class="d-flex align-items-center">
    <!-- Botón Habilitar/Desactivar edición -->
    <button id="toggle-edit-btn" class="btn btn-sm btn-secondary me-3">
      Habilitar edición
    </button>

    <!-- Dropdown manual de tres puntos -->
    <div class="manual-dropdown" id="manualDropdown" style="display: none;">
            <button id="btnOpciones" class="btn btn-link text-muted p-0">
        <i class="bi bi-three-dots-vertical fs-4"></i>
      </button>
      <ul id="menuOpciones" class="dropdown-menu">
        <li><a class="dropdown-item" href="#" id="editarSeleccion">Editar</a></li>
        <li><a class="dropdown-item" href="#">Eliminar</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">Marcar como no leído</a></li>
      </ul>
    </div>
  </div>
  <?php endif; ?>
</div>

          <div id="chatWindow" class="chat-window border rounded flex-grow-1 p-3 mb-2 overflow-auto bg-light">
            <?php if (!is_array($mensajes)): ?>
              <p class="text-center text-muted">No tienes mensajes en este chat</p>
            <?php else: ?>
              <?php foreach ($mensajes as $m):
  $isMe   = ($m[0] == $document);
  $sender = $isMe ? 'Tú' : htmlspecialchars($profActual[1]);
  $cls    = $isMe ? 'from-me bg-primary text-white' : 'from-them bg-white';
  $hora   = $m[3] ?? '';
  $leido  = $m[4] ?? null;
?>
  <div class="d-flex mb-3 message-item <?= $isMe ? 'justify-content-end' : '' ?>">
    <?php if ($isMe): ?>
      <!-- checkbox sólo en mis mensajes -->
      <input type="checkbox"
             class="edit-checkbox me-2"
             style="display:none;"
             value="<?= htmlspecialchars($m[2]) /* o el ID del mensaje */ ?>">
    <?php endif; ?>

    <div class="msg p-2 rounded <?= $cls ?>">
      <small class="text-muted">
        <?= $sender ?> • <?= $hora ?>
        <?php if ($isMe): ?>
          <i class="bi bi-check2-all <?= $leido ? 'text-white' : 'text-secondary' ?>"></i>
        <?php endif; ?>
      </small>
      <div><?= nl2br(htmlspecialchars($m[1] ?? '')) ?></div>
    </div>
  </div>
<?php endforeach; ?>


            <?php endif; ?>
          </div>

          <form method="post" class="input-group">
            <input type="hidden" name="receptor"       value="<?= $profesorId ?>">
            <input type="hidden" name="nombreReceptor" value="<?= htmlspecialchars($profNombre) ?>">
            <input name="mensaje" type="text" class="form-control" placeholder="Escribe tu mensaje..." autocomplete="off">
            <button class="btn btn-primary" type="submit">Enviar</button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="bg-dark text-white py-4 mt-5" style="background: linear-gradient(135deg, #0f1f2d, #18362f)">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 AsistGuard. Todos los derechos reservados.</p>
      <p>
        <a href="https://www.instagram.com/" style="color:white;"><img src="../src/images/instagram.png" alt="Instagram" width="24"></a> |
        <a href="https://www.facebook.com/?locale=es_ES" style="color:white;"><img src="../src/images/facebook.png" alt="Facebook" width="24"></a> |
        <a href="https://x.com/?lang=es" style="color:white;"><img src="../src/images/twitter.png" alt="Twitter" width="24"></a> |
        <a href="https://es.linkedin.com/" style="color:white;"><img src="../src/images/linkedin.png" alt="LinkedIn" width="24"></a>
      </p>
    </div>
  </footer>

  <!-- Modal de alerta personalizada -->
<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alertModalLabel">Atención</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="alertModalBody">
        <!-- Aquí irá el mensaje dinámico -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>

  
 <script>
(function(){
  const toggleBtn = document.getElementById('toggle-edit-btn');
  const manualDropdown = document.getElementById('manualDropdown');
  const editCheckboxes = () => Array.from(document.querySelectorAll('.edit-checkbox'));

  // Toggle de edición (checkboxes + dropdown)
  toggleBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    const checkboxes = editCheckboxes();
    const editing = !(checkboxes.length && checkboxes[0].style.display === 'inline-block');

    checkboxes.forEach(cb => {
      cb.style.display = editing ? 'inline-block' : 'none';
      if (!editing) cb.checked = false;  // opcional: desmarca al salir
    });

    this.textContent = editing ? 'Desactivar edición' : 'Habilitar edición';
    manualDropdown.style.display = editing ? 'inline-block' : 'none';
  });

  // Dropdown manual de tres puntos
  const btnOpciones = document.getElementById('btnOpciones');
  const menuOpciones = document.getElementById('menuOpciones');

  btnOpciones.addEventListener('click', function(e){
    e.stopPropagation();
    menuOpciones.classList.toggle('show');
  });

  document.addEventListener('click', function(){
    menuOpciones.classList.remove('show');
  });

  menuOpciones.addEventListener('click', function(e){
    e.stopPropagation();
  });

  // Acción “Editar”: sólo si hay exactamente un checkbox seleccionado
  document.getElementById('editarSeleccion').addEventListener('click', function(e){
    e.preventDefault();
    e.stopPropagation();

    const seleccionados = editCheckboxes()
      .filter(cb => cb.style.display !== 'none' && cb.checked);

    if (seleccionados.length !== 1) {
      const modalBody = document.getElementById('alertModalBody');
      modalBody.textContent = 'Por favor selecciona un único mensaje para editar.';
      const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
      alertModal.show();
      return;
    }

    const mensajeId = seleccionados[0].value;
    window.location.href = `editarMensaje.php?id=${encodeURIComponent(mensajeId)}`;
  });

  // Scroll al final y focus en el input al cargar
  window.addEventListener('load', function(){
    const chatWindow = document.getElementById('chatWindow');
    if (chatWindow) chatWindow.scrollTop = chatWindow.scrollHeight;
    const messageInput = document.querySelector('input[name="mensaje"]');
    if (messageInput) messageInput.focus();
  });
})();
</script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>