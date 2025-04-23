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

<div class="mb-4 text-center d-flex justify-content-center align-items-center" id="tituloInforme">
  <img id="iconoInforme" src="../src/images/icono-informe.png" alt="Informe" style="height: 24px; margin-right: 8px;">
  <span style="font-size: 20px;">Informe filtrado por <?= htmlspecialchars($tipo) ?></span>
</div>


  
  <div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Profesor ausente</th>
            <th>Profesor guardia</th>
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
    <img src="../src/images/sinFondoDos.png" alt="AsistGuard" width="200" height="100">
  </div>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
 document.getElementById("exportarPDF").addEventListener("click", () => {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: "landscape", unit: "mm", format: "a4" });

  const footerLogo = document.getElementById("footerLogo");
  const imgFooter = new Image();
  imgFooter.src = footerLogo.src;

  const iconoInforme = document.getElementById("iconoInforme");
  const iconoImg = new Image();
  iconoImg.src = iconoInforme.src;

  iconoImg.onload = function () {
    const pageWidth = doc.internal.pageSize.getWidth();

    const iconW = 10;
    const iconH = 10;
    const spacing = 4;
    const texto = "Informe filtrado por " + <?= json_encode($tipo) ?>;

    doc.setFontSize(14);
    const textWidth = doc.getTextWidth(texto);
    const totalWidth = iconW + spacing + textWidth;
    const startX = (pageWidth - totalWidth) / 2;
    const iconY = 20;
    const textY = iconY + 7;

    // Dibujar icono y texto centrados
    doc.addImage(iconoImg, 'PNG', startX, iconY, iconW, iconH);
    doc.text(texto, startX + iconW + spacing, textY);

    // Extraer datos de la tabla
    const table = document.querySelector("table");
    const head = [];
    const body = [];

    table.querySelectorAll("thead tr").forEach(tr => {
      const row = [];
      tr.querySelectorAll("th").forEach(th => row.push(th.textContent.trim()));
      head.push(row);
    });

    table.querySelectorAll("tbody tr").forEach(tr => {
      const row = [];
      tr.querySelectorAll("td").forEach(td => row.push(td.textContent.trim()));
      body.push(row);
    });

    // Dibujar la tabla
    doc.autoTable({
      head: head,
      body: body,
      startY: 35,
      margin: { top: 30, bottom: 25 },
      styles: { fontSize: 8, halign: 'center', cellPadding: 2 },
      headStyles: { fillColor: [15, 31, 45] },
      didDrawPage: function () {
        const pageHeight = doc.internal.pageSize.height;
        const imgWidth = 40;
        const imgHeight = 15;
        const x = (doc.internal.pageSize.width - imgWidth) / 2;
        const y = pageHeight - imgHeight - 5;
        doc.addImage(imgFooter, 'PNG', x, y, imgWidth, imgHeight);
      }
    });

    doc.save("informe-filtrado.pdf");
  };
});

</script>
</body>
</html>