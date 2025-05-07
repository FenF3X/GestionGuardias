/**
 * @function window.onload
 * @description Simula un clic en el botón de "Cargar Guardias" cuando la página termina de cargar.
 */
window.onload = function () {
    /**
     * @var {HTMLButtonElement|null} cargarBtn
     * @description Referencia al botón "Cargar Guardias" en la página.
     */
    const cargarBtn = document.getElementById('cargarGuardiasBtn');
    if (cargarBtn) {
      cargarBtn.click();
    }
  };
  
  /**
   * @function
   * @name initCoverGuardModalAndValidation
   * @description Inicializa la lógica del modal para cubrir guardias y la validación de selección de sesiones.
   */
  document.addEventListener('DOMContentLoaded', function () {
    /**
     * @var {HTMLElement|null} modalElement
     * @description Elemento del DOM que representa el modal de cubrir guardias.
     */
    const modalElement = document.getElementById('coverGuardModal');
    if (modalElement) {
      /**
       * @var {bootstrap.Modal} modal
       * @description Instancia de Bootstrap Modal para controlar la ventana emergente.
       */
      const modal = new bootstrap.Modal(modalElement);
  
      /**
       * Añade listener a todos los botones que disparan el modal.
       * @var {NodeListOf<HTMLElement>} modalButtons
       */
      const modalButtons = document.querySelectorAll('[data-bs-toggle="modal"]');
      modalButtons.forEach(button => {
        /**
         * @param {Event} event
         * @description Muestra información de la sesión en el modal y rellena los inputs ocultos.
         */
        button.addEventListener('click', function (event) {
          /**
           * @var {string} sesionInfo
           * @description Texto descriptivo de la sesión obtenido del atributo data-sesion.
           */
          const sesionInfo = this.getAttribute('data-sesion');
          /**
           * @var {string} sesionId
           * @description Identificador de la sesión obtenido del atributo data-sesion-id.
           */
          const sesionId = this.getAttribute('data-sesion-id');
          /**
           * @var {string} documentValue
           * @description Documento del usuario obtenido del atributo data-document.
           */
          const documentValue = this.getAttribute('data-document');
  
          document.getElementById('sesion-info').textContent =
            "Vas a cubrir la guardia: " + sesionInfo;
          document.getElementById('sesion_id_input').value = sesionId;
          document.getElementById('document_input').value = documentValue;
        });
      });
  
      /**
       * @param {Event} event
       * @description Cierra el modal cuando se confirma cubrir la guardia.
       */
      document.getElementById('confirm-cover')?.addEventListener('click', function (event) {
        modal.hide();
      });
    }
  
    /**
     * @var {HTMLButtonElement|null} guardarBtn
     * @description Botón para guardar la ausencia; requiere al menos una sesión seleccionada.
     */
    const guardarBtn = document.getElementById("guardarAusencia");
    if (guardarBtn) {
      /**
       * @param {Event} e
       * @description Valida la selección de sesiones antes de enviar el formulario.
       */
      guardarBtn.addEventListener("click", function (e) {
        /**
         * @var {NodeListOf<HTMLInputElement>} checkboxes
         * @description Todos los checkboxes de sesión.
         */
        const checkboxes = document.querySelectorAll(".checkboxSesion");
        /**
         * @var {NodeListOf<HTMLInputElement>} seleccionadas
         * @description Checkboxes de sesión que han sido marcados.
         */
        const seleccionadas = document.querySelectorAll(".checkboxSesion:checked");
  
        if (seleccionadas.length === 0) {
          alert("Por favor, selecciona al menos una sesión.");
          e.preventDefault();
          return;
        }
  
        /**
         * @var {HTMLInputElement|null} jornadaCompleta
         * @description Checkbox que indica ausencia de jornada completa.
         */
        const jornadaCompleta = document.getElementById("jornada_completa");
        if (jornadaCompleta) {
          // Si todas las sesiones están marcadas, marca jornada completa
          jornadaCompleta.checked = (checkboxes.length === seleccionadas.length);
        }
      });
    }
  });
  