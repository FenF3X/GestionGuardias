/**
 *  ==========================
 *       exportarAPDF.js
 *  ==========================
 * Script que realiza el control de las sesiones seleccionadas que 
 * indican la ausencia por sesiones de ese docente seleccionado que 
 * llega aquí por redireccionamiento
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 *
 * @var {{ jsPDF: function }} jsPDF           - Objeto destructurado desde window.jspdf, contiene el constructor de jsPDF.
 * @var {jsPDF} doc                          - Instancia de jsPDF creada con orientación landscape, unidad mm y formato A4.
 * @var {HTMLImageElement} footerLogo        - Elemento <img> del DOM que contiene el logo del footer.
 * @var {HTMLImageElement} imgFooter         - Objeto Image utilizado para cargar el logo en el pie de página del PDF.
 * @var {HTMLImageElement} iconoInforme      - Elemento <img> del DOM que contiene el icono del informe.
 * @var {HTMLImageElement} iconoImg          - Objeto Image utilizado para cargar el icono en la cabecera del PDF.
 * @var {number} pageWidth                   - Anchura de la página PDF en milímetros.
 * @var {number} iconW                       - Anchura en mm del icono que se dibuja junto al texto.
 * @var {number} iconH                       - Altura en mm del icono que se dibuja junto al texto.
 * @var {number} spacing                     - Espacio en mm entre el icono y el texto.
 * @var {string} texto                       - Cadena con el texto "Informe filtrado por ..." que aparece en la cabecera.
 * @var {number} textWidth                   - Anchura en mm que ocupa el texto calculada por jsPDF.
 * @var {number} totalWidth                  - Anchura total combinada de icono + espacio + texto.
 * @var {number} startX                      - Coordenada X inicial para centrar icono y texto.
 * @var {number} iconY                       - Coordenada Y para la posición vertical del icono.
 * @var {number} textY                       - Coordenada Y para la posición vertical del texto.
 * @var {HTMLTableElement} table             - Referencia al elemento <table> que se exporta.
 * @var {Array<Array<string>>} head          - Matriz de filas de encabezado extraídas del <thead>.
 * @var {Array<Array<string>>} body          - Matriz de filas de datos extraídas del <tbody>.
 */

/**
 * @function
 * @name exportarPDFHandler
 * @description
 * Manejador del evento 'click' en el botón de exportar a PDF.  
 * Carga los recursos gráficos (icono y logo), extrae datos de la tabla,
 * genera un documento jsPDF con cabecera, cuerpo (autoTable) y pie de página,
 * y finalmente dispara la descarga del archivo PDF.
 */
document.getElementById("exportarPDF").addEventListener("click", () => {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: "landscape", unit: "mm", format: "a4" });

  const footerLogo = document.getElementById("footerLogo");
  const imgFooter = new Image();
  imgFooter.src = footerLogo.src;

  const iconoInforme = document.getElementById("iconoInforme");
  const iconoImg = new Image();
  iconoImg.src = iconoInforme.src;

  /**
   * @function
   * @name onIconoLoad
   * @description
   * Evento onload de la imagen del icono de informe.  
   * Una vez cargada, centra la cabecera (icono + texto),
   * extrae datos de la tabla y genera la tabla con autoTable,
   * incluyendo el pie de página en cada página.
   */
  iconoImg.onload = function () {
    const pageWidth = doc.internal.pageSize.getWidth();

    const iconW = 10;
    const iconH = 10;
    const spacing = 4;
    const texto = "Informe filtrado por " + document.getElementById("tipo").value;

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

      /**
       * @callback didDrawPage
       * @description
       * Callback de autoTable que se invoca tras dibujar cada página.
       * Añade el logo del footer centrado en la parte inferior de la página.
       */
      didDrawPage: function () {
        const pageHeight = doc.internal.pageSize.height;
        const imgWidth = 40;
        const imgHeight = 15;
        const x = (doc.internal.pageSize.width - imgWidth) / 2;
        const y = pageHeight - imgHeight - 5;
        doc.addImage(imgFooter, 'PNG', x, y, imgWidth, imgHeight);
      }
    });

    // Dispara la descarga del PDF generado
    doc.save("informe-filtrado.pdf");
  };
});
