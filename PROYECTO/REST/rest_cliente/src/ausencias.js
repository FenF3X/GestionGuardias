/**
 *  ==========================
 *          ausencias.js
 *  ==========================
 * Script que realiza el control de las sesiones seleccionadas que 
 * indican la ausencia por sesiones de ese docente seleccionado que 
 * llega aquí por redireccionamiento
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * 
 * @var {NodeListOf<HTMLInputElement>} checkboxesSeleccionados - Lista de todos los checkboxes de sesiones que están marcados.
 * @var {Array<[string, string]>} horas         - Array de pares [horaInicio, horaFin] extraídos de los checkboxes seleccionados.
 * @var {[string, string]} hora                - Par [horaInicio, horaFin] obtenido al hacer split del valor de un checkbox.
 *
 * @param {[string, string]} a                  - Primer par [horaInicio, horaFin] a comparar en la función de ordenación.
 * @param {[string, string]} b                  - Segundo par [horaInicio, horaFin] a comparar en la función de ordenación.
 * @var {HTMLInputElement} jornadaCompleta      - Checkbox que activa/desactiva la selección de todas las sesiones.
 * @var {NodeListOf<HTMLInputElement>} checkboxes - Lista de todos los checkboxes de sesiones (usada al marcar jornada completa).
 * @var {HTMLInputElement} checkbox             - Cada checkbox individual en los bucles forEach de los event listeners.
 */
function actualizarHoras() {
    // Obtener todos los checkboxes de sesiones marcados
    var checkboxesSeleccionados = document.querySelectorAll('input[name="sesiones[]"]:checked');

    if (checkboxesSeleccionados.length > 0) {
        var horas = [];

        // Recorrer cada checkbox marcado y extraer rango de horas
        checkboxesSeleccionados.forEach(checkbox => {
            // El valor es "HH:MM - HH:MM"
            var hora = checkbox.value.split(' - ');
            horas.push(hora);
        });

        // Ordenar por hora de inicio
        horas.sort(function(a, b) {
            // Comparamos fechas ficticias para ordenar correctamente
            return new Date('1970/01/01 ' + a[0]) - new Date('1970/01/01 ' + b[0]);
        });

        // Asignar valores a los inputs visibles
        document.getElementById('hora_inicio').value = horas[0][0];
        document.getElementById('hora_fin').value   = horas[horas.length - 1][1];

        // Asignar valores a los inputs ocultos del formulario
        document.querySelector('input[name="hora_inicio"]').value = horas[0][0];
        document.querySelector('input[name="hora_fin"]').value   = horas[horas.length - 1][1];
    } else {
        // Sin sesiones seleccionadas: limpiar campos
        document.getElementById('hora_inicio').value = null;
        document.getElementById('hora_fin').value   = null;
        document.querySelector('input[name="hora_inicio"]').value = null;
        document.querySelector('input[name="hora_fin"]').value   = null;
    }
}

/**
 * Añade un listener de cambio a cada checkbox de sesión para disparar `actualizarHoras`.
 *
 * @function initSesionListeners
 * @returns {void}
 */
function initSesionListeners() {
    document.querySelectorAll('input[name="sesiones[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', actualizarHoras);
    });
}

/**
 * Controla el checkbox "Jornada Completa":
 * - Al marcarlo, selecciona/desmarca todas las sesiones.
 * - Actualiza las horas tras el cambio.
 *
 * @function initJornadaCompleta
 * @returns {void}
 */
function initJornadaCompleta() {
    var jornadaCompleta = document.getElementById('jornada_completa');
    var checkboxes = document.querySelectorAll('input[name="sesiones[]"]');

    jornadaCompleta.addEventListener('change', function() {
        // Marcar o desmarcar todos los checkboxes según estado
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = jornadaCompleta.checked;
        });
        // Actualizar campos de hora
        actualizarHoras();
    });
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initSesionListeners();
    initJornadaCompleta();
    // Realizamos una actualización inicial por si hay preselecciones
    actualizarHoras();
});
