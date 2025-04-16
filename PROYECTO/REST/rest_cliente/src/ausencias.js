// Función para actualizar las horas seleccionadas
function actualizarHoras() {
    var checkboxesSeleccionados = document.querySelectorAll('input[name="sesiones[]"]:checked');
    console.log(checkboxesSeleccionados);
    if (checkboxesSeleccionados.length > 0) {
        var horas = [];
        checkboxesSeleccionados.forEach(checkbox => {
            var hora = checkbox.value.split(' - '); // Separar la hora de inicio y fin
            horas.push(hora);
        });

        // Ordenar las horas por la hora de inicio
        horas.sort(function(a, b) {
            return new Date('1970/01/01 ' + a[0]) - new Date('1970/01/01 ' + b[0]);
        });

        // Asignar la hora de inicio y fin en los campos de texto
        document.getElementById('hora_inicio').value = horas[0][0]; // Primer hora de inicio
        document.getElementById('hora_fin').value = horas[horas.length - 1][1]; // Última hora de fin

        // Actualizar los valores ocultos para enviarlos con el formulario
        document.querySelector('input[name="hora_inicio"]').value = horas[0][0];
        document.querySelector('input[name="hora_fin"]').value = horas[horas.length - 1][1];
    } else {
        // Limpiar los campos ocultos si no hay checkboxes seleccionados
        document.getElementById('hora_inicio').value = '';
        document.getElementById('hora_fin').value = '';
        document.querySelector('input[name="hora_inicio"]').value = '';
        document.querySelector('input[name="hora_fin"]').value = '';
    }
}

// Agregar evento para todos los checkboxes de las sesiones
document.querySelectorAll('input[name="sesiones[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', actualizarHoras);
});

// Lógica para el checkbox "Jornada Completa"
document.addEventListener('DOMContentLoaded', function() {
    var jornadaCompleta = document.getElementById('jornada_completa');
    var checkboxes = document.querySelectorAll('input[name="sesiones[]"]');

    // Cuando se cambia el estado del checkbox "jornada_completa", marcar/desmarcar todos los checkboxes
    jornadaCompleta.addEventListener('change', function() {
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = jornadaCompleta.checked; // Si se marca, se marcan todos
        });

        // Actualizar las horas también cuando se marca/desmarca la jornada completa
        actualizarHoras();
    });
});
