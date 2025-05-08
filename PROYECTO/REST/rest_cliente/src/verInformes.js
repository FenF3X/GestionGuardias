/**
 *  ==========================
 *       verInformes.js
 *  ==========================
 * Script que controla la visibilidad de los campos de filtro de informes
 * según la opción seleccionada en el select.
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 */

document.addEventListener('DOMContentLoaded', () => {
  /**
   * @var {HTMLSelectElement} tipoSelect
   * @description Select que determina el tipo de informe a generar.
   */
  const tipoSelect = document.getElementById('tipoInforme');

  /**
   * @var {{ dia: HTMLElement, semana: HTMLElement, mes: HTMLElement, trimestre: HTMLElement, docent: HTMLElement }} campos
   * @description Mapa de campos de entrada asociados a cada tipo de informe.
   * @property {HTMLElement} dia         - Contenedor del campo para seleccionar un día.
   * @property {HTMLElement} semana      - Contenedor del campo para seleccionar una semana.
   * @property {HTMLElement} mes         - Contenedor del campo para seleccionar un mes.
   * @property {HTMLElement} trimestre   - Contenedor del campo para seleccionar un trimestre.
   * @property {HTMLElement} docent      - Contenedor del campo para seleccionar un docente.
   */
  const campos = {
    dia: document.getElementById('campo-dia'),
    semana: document.getElementById('campo-semana'),
    mes: document.getElementById('campo-mes'),
    trimestre: document.getElementById('campo-trimestre'),
    docent: document.getElementById('campo-docent'),
    plazo: document.getElementById('campo-plazo')    
  };

  /**
   * @function actualizarCampos
   * @description Oculta todos los contenedores de campos y muestra solo
   *              aquél correspondiente al informe seleccionado.
   */
  function actualizarCampos() {
    // Oculta todos los campos
    Object.values(campos).forEach(campo => {
      campo.style.display = 'none';
    });

    // Muestra el campo correspondiente al valor seleccionado
    const seleccionado = tipoSelect.value;
    if (seleccionado && campos[seleccionado]) {
      campos[seleccionado].style.display = 'block';
    }
  }

  // Listener para actualizar al cambiar la selección
  tipoSelect.addEventListener('change', actualizarCampos);

  // Inicializa la vista al cargar la página
  actualizarCampos();
});
