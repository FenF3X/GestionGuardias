document.addEventListener('DOMContentLoaded', () => {
    const hiddenForm = document.getElementById('formConsultaProfes');

  if (hiddenForm && !localStorage.getItem('consultaProfesRealizada')) {
    hiddenForm.submit();
    localStorage.setItem('consultaProfesRealizada', 'true');
  }
    const tipoSelect = document.getElementById('tipoInforme');

    const campos = {
      dia: document.getElementById('campo-dia'),
      semana: document.getElementById('campo-semana'),
      mes: document.getElementById('campo-mes'),
      trimestre: document.getElementById('campo-trimestre'),
      docent: document.getElementById('campo-docent')
    };

    function actualizarCampos() {
      Object.values(campos).forEach(campo => {
        campo.style.display = 'none';
      });

      const seleccionado = tipoSelect.value;
      if (seleccionado && campos[seleccionado]) {
        campos[seleccionado].style.display = 'block';
      }
    }

    tipoSelect.addEventListener('change', actualizarCampos);
    actualizarCampos();
  });