window.onload = function () {
    // Simula un clic en el botón de "Cargar Guardias" cuando la página se carga
    const cargarBtn = document.getElementById('cargarGuardiasBtn');
    if (cargarBtn) {
        cargarBtn.click();
    }
};

document.addEventListener('DOMContentLoaded', function () {
    // Lógica del modal para cubrir guardias
    const modalElement = document.getElementById('coverGuardModal');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);

        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function () {
                const sesionInfo = this.getAttribute('data-sesion');
                const sesionId = this.getAttribute('data-sesion-id');
                const documentValue = this.getAttribute('data-document');

                document.getElementById('sesion-info').textContent = "Vas a cubrir la guardia: " + sesionInfo;
                document.getElementById('sesion_id_input').value = sesionId;
                document.getElementById('document_input').value = documentValue;
            });
        });

        document.getElementById('confirm-cover')?.addEventListener('click', function () {
            modal.hide();
        });
    }

    // Validar que se seleccione al menos una sesión
    const guardarBtn = document.getElementById("guardarAusencia");
    if (guardarBtn) {
        guardarBtn.addEventListener("click", function (e) {
            const checkboxes = document.querySelectorAll(".checkboxSesion");
            const seleccionadas = document.querySelectorAll(".checkboxSesion:checked");

            if (seleccionadas.length === 0) {
                alert("Por favor, selecciona al menos una sesión.");
                e.preventDefault();
                return;
            }

            // Marcar el checkbox de jornada completa si todas las sesiones están seleccionadas
            const jornadaCompleta = document.getElementById("jornada_completa");
            if (jornadaCompleta) {
                jornadaCompleta.checked = (checkboxes.length === seleccionadas.length);
            }
        });
    }
});
