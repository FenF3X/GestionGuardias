// chat.js
document.addEventListener('DOMContentLoaded', function() {
  alert("cargado con exito")
        // ===== 1) Scroll al final del chat =====
        const chatWindow = document.getElementById('chatWindow');
        if (chatWindow) {
          chatWindow.scrollTop = chatWindow.scrollHeight;
        }
      
        // ===== 2) Fondo azul en el select “agenda” =====
        const profes = document.getElementById('agenda');
        if (profes) {
          // Al cambiar la opción
          profes.addEventListener('change', function() {
            for (let i = 0; i < profes.options.length; i++) {
              profes.options[i].classList.remove('bg-primary', 'text-white');
            }
            profes.options[profes.selectedIndex].classList.add('bg-primary', 'text-white');
          });
          // Inicializar la opción seleccionada
          profes.options[profes.selectedIndex].classList.add('bg-primary', 'text-white');
        }
      
        // ===== 3) Dropdown de opciones de mensaje =====
        // Si usas id="opciones":
        const opcionesMensaje = document.getElementById('opciones');
        // O si preferís clase:
        // const opcionesMensaje = document.querySelector('.dropCustomGroup');
      
        if (opcionesMensaje) {
          opcionesMensaje.addEventListener('click', function() {
            opcionesMensaje.style.display = 'none';
          });
          opcionesMensaje.addEventListener('mouseover', function() {
            opcionesMensaje.style.display = 'none';
          });
        }
      
      });
      