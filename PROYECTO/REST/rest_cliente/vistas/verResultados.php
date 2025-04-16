<?php
session_start();
if (!isset($_SESSION['document'])) {
  header("Location: ../login.php");
  exit();
}
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
$documento = $_SESSION['document'];

$resultados = $_SESSION['resultado_informe'] ?? [];
$tipo = $_SESSION['tipo_informe'] ?? 'informe';

if (empty($resultados) || !isset($resultados[0]) || !is_array($resultados[0])) {
    echo "<div class='alert alert-warning text-center m-5'>⚠️ No hay datos disponibles para mostrar.</div>";
    exit;
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Resultados del Informe</title>
  <link rel="shortcut icon" href="../src/images/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="../src/principal.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
          color: #d0f0ff !important;
        }

        .dropdown:hover .dropdown-menu {
          display: block;
          background: linear-gradient(135deg, #0f1f2d, #18362f);
        }

        
  table {
    table-layout: auto !important;
    width: 100% !important;
  }

  th, td {
    white-space: nowrap;
    word-break: break-word;
    padding: 6px;
    font-size: 11px;
  }

  .table-responsive {
    overflow-x: auto;
  }
</style>


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

<main>
  <div class="container mt-5 d-flex justify-content-start align-items-center perfil-contenedor">
    <div class="foto-wrapper me-4">
      <img src="../src/images/default.jpg" alt="Foto de perfil" class="foto-circular">
    </div>
    <div class="info-usuario text-start">
      <p><strong>Documento:</strong> <?php echo htmlspecialchars($documento); ?></p>
      <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
      <p><strong>Rol:</strong> <?php echo htmlspecialchars($rol); ?></p>
    </div>
  </div>
</main>

<div class="container mt-5 tabla2PDF">

  <h2 class="mb-4 text-center">📊 Informe filtrado por <?= htmlspecialchars($tipo) ?></h2>

  <div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Profesor ausente</th>
            <th>Aula</th>
            <th>Grupo</th>
            <th>Asignatura</th>
            <th>Sesion</th>
            <th>Dia</th>
            <th>Hora Inicio -- Hora fin</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($resultados as $fila): ?>
            <tr>
                <?php foreach ($fila as $valor): ?>
                    <td><?= htmlspecialchars($valor) ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
  </div>

  
</div>
<div class="mt-4 text-center">
    <a href="verInformes.php" class="btn btn-secondary">⬅️ Volver</a>
  </div>
<!-- Botón flotante para exportar a PDF -->
<button id="exportarPDF" class="btn btn-danger position-fixed" 
        style="top: 90px; right: 20px; z-index: 1000;">
  <i class="bi bi-file-earmark-pdf-fill"></i> Exportar a PDF
</button>
<img id="footerLogo" src="../src/images/logoenUno.png" alt="Imagen PDF" style="display:none; max-width: 250px;">

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="../src/exportarAPDF.js"></script>

</body>
</html>
