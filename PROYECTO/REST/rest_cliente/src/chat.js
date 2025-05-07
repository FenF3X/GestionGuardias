/**
 *  ==========================
 *       chat.js
 *  ==========================
 * Script  que controla la edición, opciones y borrado de mensajes en la interfaz de chat.
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 */
(function(){

    /**
     * @var {HTMLButtonElement|null} toggleBtn
     * @description Botón que activa o desactiva el modo edición (muestra/oculta checkboxes).
     */
    const toggleBtn      = document.getElementById('toggle-edit-btn');
  
    /**
     * @var {HTMLElement|null} manualDropdown
     * @description Contenedor de opciones manuales que aparece en modo edición.
     */
    const manualDropdown = document.getElementById('manualDropdown');
  
    /**
     * @function editCheckboxes
     * @description Obtiene todos los checkboxes de edición como un array.
     * @returns {Array<HTMLInputElement>}
     */
    const editCheckboxes = () => Array.from(document.querySelectorAll('.edit-checkbox'));
  
    /**
     * @var {HTMLButtonElement|null} btnOpciones
     * @description Botón que despliega el menú de opciones.
     */
    const btnOpciones    = document.getElementById('btnOpciones');
  
    /**
     * @var {HTMLElement|null} menuOpciones
     * @description Menú desplegable con acciones adicionales.
     */
    const menuOpciones   = document.getElementById('menuOpciones');
  
    /**
     * @var {HTMLFormElement|null} form
     * @description Formulario que envía la petición de borrado de mensajes seleccionados.
     */
    const form           = document.getElementById('deleteForm');
  
    /**
     * @var {HTMLButtonElement|null} confirmDelete
     * @description Botón que confirma el borrado en el modal de eliminación.
     */
    const confirmDelete  = document.getElementById('confirmDeleteBtn');
    
    // 1) Toggle edición
    /**
     * @event toggleBtn#click
     * @param {MouseEvent} e - Evento de clic sobre el botón de edición.
     * @description Alterna la visibilidad de los checkboxes de edición, restablece su estado
     *              y muestra u oculta el dropdown de opciones.
     */
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
    /**
     * @event btnOpciones#click
     * @param {MouseEvent} e - Evento de clic para abrir/cerrar el menú de opciones.
     * @description Alterna la clase `.show` en el menú de opciones.
     */
    btnOpciones.addEventListener('click', function(e) {
      e.stopPropagation();
      menuOpciones.classList.toggle('show');
    });
    /**
     * @event document#click
     * @description Cierra el menú de opciones al hacer clic fuera de él.
     */
    document.addEventListener('click', () => {
      menuOpciones.classList.remove('show');
    });
    /**
     * @event menuOpciones#click
     * @param {MouseEvent} e - Previene el cierre del menú al hacer clic dentro de él.
     */
    menuOpciones.addEventListener('click', e => e.stopPropagation());
    
    // 3) Editar mensaje
    /**
     * @event HTMLElement#click
     * @name editarSeleccion
     * @param {Event} e - Evento de clic para editar un mensaje.
     * @description Si exactamente un checkbox está marcado, abre el modal de edición
     *              rellenando los campos con los datos del checkbox.
     */
    document.getElementById('editarSeleccion').addEventListener('click', function(e) {
      e.preventDefault(); e.stopPropagation();
      const sel = editCheckboxes().filter(cb => cb.style.display==='inline-block' && cb.checked);
      if (sel.length !== 1) {
        document.getElementById('alertModalBody').textContent =
          'Por favor selecciona un único mensaje para editar.';
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
    /**
     * @event HTMLElement#click
     * @name borrarSeleccion
     * @param {Event} e - Evento de clic para eliminar mensajes.
     * @description Abre el modal de confirmación de borrado e inyecta inputs hidden
     *              con los datos de cada mensaje seleccionado.
     */
    document.getElementById('borrarSeleccion').addEventListener('click', function(e) {
      e.preventDefault(); e.stopPropagation();
      const sel = editCheckboxes().filter(cb => cb.style.display==='inline-block' && cb.checked);
      if (sel.length === 0) {
        document.getElementById('alertModalBody').textContent =
          'Por favor selecciona al menos un mensaje para eliminar.';
        new bootstrap.Modal(document.getElementById('alertModal')).show();
        return;
      }
      document.getElementById('deleteModalBody').textContent =
        `¿Seguro que quieres eliminar ${sel.length} mensaje${sel.length>1 ? 's' : ''}?`;
  
      // Limpiar inputs previos
      form.querySelectorAll('input[name^="seleccionados"]').forEach(i => i.remove());
  
      // Añadir inputs para cada mensaje seleccionado
      sel.forEach((cb, idx) => {
        const mapping = { fecha: 'fecha', hora: 'hora', mensajeOriginal: 'original' };
        Object.entries(mapping).forEach(([fieldName, dataAttr]) => {
          /**
           * @var {HTMLInputElement} inp
           * @description Input oculto con datos del mensaje a eliminar.
           */
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
    /**
     * @event confirmDelete#click
     * @description Al confirmar borrado, envía el formulario con los mensajes seleccionados.
     */
    confirmDelete.addEventListener('click', function() {
      form.submit();
    });
    
    // 6) Auto-scroll y focus en la ventana de chat
    /**
     * @event window#load
     * @description Al cargar la página, hace scroll al final del chat y pone focus en el input de mensaje.
     */
    window.addEventListener('load', function(){
      const win = document.getElementById('chatWindow');
      if (win) win.scrollTop = win.scrollHeight;
      document.querySelector('input[name="mensaje"]').focus();
    });
    
  })();
  