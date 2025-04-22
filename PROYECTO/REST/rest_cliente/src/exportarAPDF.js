document.getElementById("exportarPDF").addEventListener("click", function () {
  const contenido = document.querySelector(".tabla2PDF");
  const footerLogo = document.getElementById("footerLogo");

  const opt = {
    margin: [10, 10, 30, 10], // aumenta el margen inferior para dejar espacio al logo alto
    filename: 'informe-filtrado.pdf',
    image: { type: 'jpeg', quality: 1 },
    html2canvas: { scale: 4, useCORS: true },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
  };

  html2pdf().set(opt).from(contenido).toPdf().get('pdf').then(function (pdf) {
    const totalPages = pdf.internal.getNumberOfPages();
    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();
    const logoWidth = 40;
    const logoHeight = 20; // ahora más alto
    const x = (pageWidth - logoWidth) / 2;
    const y = pageHeight - logoHeight - 10; // deja espacio antes del borde

    for (let i = 1; i <= totalPages; i++) {
      pdf.setPage(i);
      pdf.addImage(footerLogo, 'PNG', x, y, logoWidth, logoHeight);
    }
  }).save();
});
