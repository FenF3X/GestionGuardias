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
  <link rel="stylesheet" href="../src/chat.css">

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
          <button 
  class="btn btn-sm btn-outline-light"
  style="background:linear-gradient(135deg, #1e3a5f, #0f1f2d);" 
  title="Cerrar sesión">
    <i class="bi bi-box-arrow-right"></i>
  </button>          </form>
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
    <button id="toggle-edit-btn" class="btn btn-sm btn-secondary me-3" style="background: linear-gradient(135deg, #9d4edd, #1e3a5f); border:2px solid;">
      Habilitar edición
    </button>

    <!-- Dropdown manual de tres puntos -->
    <div class="manual-dropdown" id="manualDropdown" style="display: none;">
            <button id="btnOpciones" class="btn btn-link text-muted p-0">
        <i class="bi bi-three-dots-vertical fs-4"></i>
      </button>
      <ul id="menuOpciones" class="dropdown-menu">
        <li><a class="dropdown-item"  id="editarSeleccion">Editar mensaje</a></li>
        <li><a class="dropdown-item" id="borrarSeleccion">Eliminar mensaje(s)</a></li>
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
          $isMe       = ($m[0] == $document);
          $sender     = $isMe ? 'Tú' : htmlspecialchars($profActual[1]);
          $cls        = $isMe ? 'from-me bg-primary text-white' : 'from-them bg-white';
          $fecha      = htmlspecialchars($m[2]);  // fecha
          $hora       = htmlspecialchars($m[3]);  // hora
          $leido       = htmlspecialchars($m[4]);  // leido
          $original   = htmlspecialchars($m[1]);  // texto original
          $mensajeId  = htmlspecialchars($m[0]);  // id del mensaje
          $checkColor = $leido ? 'text-white' : 'text-black';


        ?>
          <div class="d-flex mb-3 message-item <?= $isMe ? 'justify-content-end' : '' ?>">
            <?php if ($isMe): ?>
              <input type="checkbox"
                    class="edit-checkbox me-2"
                    style="display:none;"
                    value="<?= $mensajeId ?>"
                    data-fecha="<?= $fecha ?>"
                    data-hora="<?= $hora ?>"
                    data-original="<?= $original ?>">
            <?php endif; ?>

            <div class="msg p-2 rounded <?= $cls ?>">
              <small class="text-muted"><?= $sender ?> • <?= $hora ?></small>
              <i class="bi bi-check2-all <?= $checkColor ?> ms-2"></i>
              <div><?= nl2br($original) ?></div>
            </div>
          </div>
        <?php endforeach; ?>



            <?php endif; ?>
          </div>

          <form method="post" class="input-group">
            <input type="hidden" name="receptor"       value="<?= $profesorId ?>">
            <input type="hidden" name="nombreReceptor" value="<?= htmlspecialchars($profNombre) ?>">
            <input name="mensaje" type="text" class="form-control" placeholder="Escribe tu mensaje..." autocomplete="off"
            >
            <button class="btn btn-primary" type="submit" style="background: linear-gradient(135deg, #9d4edd, #1e3a5f); border:2px solid;">Enviar</button>
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

<!-- Modal de edición de mensaje -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editForm" method="post" action="../editarMensaje.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Editar mensaje</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <input type="hidden" name="idMensaje" id="editModalId">
      <input type="hidden" name="fecha" id="editModalFecha">
      <input type="hidden" name="hora" id="editModalHora">
      <input type="hidden" name="mensajeOriginal" id="editModalOriginal">
      <input type="hidden" name="nombreReceptor"   value="<?= htmlspecialchars($profNombre) ?>">
      <div class="modal-body">
        <div class="mb-3">
          <label for="editModalTextarea" class="form-label">Mensaje</label>
          <textarea class="form-control" name="mensajeEditado" id="editModalTextarea" rows="4"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="deleteForm" method="post" action="../eliminarMensajes.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Eliminar mensajes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <input type="hidden" name="nombreReceptor"   value="<?= htmlspecialchars($profNombre) ?>">

      <div class="modal-body">
        <p id="deleteModalBody">¿Seguro que quieres eliminar estos mensajes?</p>
        <!-- Aquí se inyectarán inputs tipo seleccionados[0][fecha], seleccionados[0][hora], seleccionados[0][mensajeOriginal], etc. -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Eliminar</button>
      </div>
    </form>
  </div>
</div>


  
<script>
(function(){
  // Elementos clave
  const toggleBtn      = document.getElementById('toggle-edit-btn');
  const manualDropdown = document.getElementById('manualDropdown');
  const editCheckboxes = () => Array.from(document.querySelectorAll('.edit-checkbox'));
  const btnOpciones    = document.getElementById('btnOpciones');
  const menuOpciones   = document.getElementById('menuOpciones');
  const form           = document.getElementById('deleteForm');
  const confirmDelete  = document.getElementById('confirmDeleteBtn');

  // 1) Toggle edición
  toggleBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    const cbs = editCheckboxes();
    const editing = cbs[0]?.style.display !== 'inline-block';
    cbs.forEach(cb => {
      cb.style.display = editing ? 'inline-block' : 'none';
      if (!editing) cb.checked = false;
    });
    this.textContent = editing ? 'Desactivar edición' : 'Habilitar edición';
    manualDropdown.style.display = editing ? 'inline-block' : 'none';
  });

  // 2) Mostrar/ocultar menú de opciones
  btnOpciones.addEventListener('click', function(e) {
    e.stopPropagation();
    menuOpciones.classList.toggle('show');
  });
  document.addEventListener('click', () => {
    menuOpciones.classList.remove('show');
  });
  menuOpciones.addEventListener('click', e => e.stopPropagation());

  // 3) Editar mensaje
  document.getElementById('editarSeleccion').addEventListener('click', function(e) {
    e.preventDefault(); e.stopPropagation();
    const sel = editCheckboxes().filter(cb => cb.style.display==='inline-block' && cb.checked);
    if (sel.length !== 1) {
      document.getElementById('alertModalBody').textContent = 'Por favor selecciona un único mensaje para editar.';
      new bootstrap.Modal(document.getElementById('alertModal')).show();
      return;
    }
    const cb = sel[0];
    document.getElementById('editModalId').value       = cb.value;
    document.getElementById('editModalOriginal').value = cb.dataset.original;
    document.getElementById('editModalFecha').value    = cb.dataset.fecha;
    document.getElementById('editModalHora').value     = cb.dataset.hora;
    document.getElementById('editModalTextarea').value = cb.dataset.original;
    new bootstrap.Modal(document.getElementById('editModal')).show();
  });

  // 4) Borrar mensajes
  document.getElementById('borrarSeleccion').addEventListener('click', function(e) {
    e.preventDefault(); e.stopPropagation();
    const sel = editCheckboxes().filter(cb => cb.style.display==='inline-block' && cb.checked);
    if (sel.length === 0) {
      document.getElementById('alertModalBody').textContent = 'Por favor selecciona al menos un mensaje para eliminar.';
      new bootstrap.Modal(document.getElementById('alertModal')).show();
      return;
    }
    document.getElementById('deleteModalBody').textContent =
      `¿Seguro que quieres eliminar ${sel.length} mensaje${sel.length>1?'s':''}?`;

    // Limpiar inputs previos
    form.querySelectorAll('input[name^="seleccionados"]').forEach(i => i.remove());

    // Añadir inputs para cada mensaje seleccionado, usando data-original
    sel.forEach((cb, idx) => {
      const mapping = {
        fecha: 'fecha',
        hora: 'hora',
        mensajeOriginal: 'original'
      };
      Object.entries(mapping).forEach(([fieldName, dataAttr]) => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = `seleccionados[${idx}][${fieldName}]`;
        inp.value = cb.dataset[dataAttr];
        form.appendChild(inp);
      });
    });

    new bootstrap.Modal(document.getElementById('deleteModal')).show();
  });

  // 5) Confirmar y enviar borrado
  confirmDelete.addEventListener('click', function() {
    form.submit();
  });

  // 6) Auto-scroll y focus en el input al cargar
  window.addEventListener('load', function(){
    const win = document.getElementById('chatWindow');
    if (win) win.scrollTop = win.scrollHeight;
    document.querySelector('input[name="mensaje"]').focus();
  });

})();
</script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>