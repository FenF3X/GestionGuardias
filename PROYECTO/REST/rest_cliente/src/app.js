document.addEventListener('DOMContentLoaded', () => {
  const fechaElemento = document.querySelector('.fecha-actual');
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("password");
  const eyeIcon = document.getElementById("eyeIcon");

  togglePassword.addEventListener("click", function () {
    const isPassword = passwordInput.type === "password";
    passwordInput.type = isPassword ? "text" : "password";
    eyeIcon.textContent = isPassword ? "🙈" : "👁️";
  });

  function actualizarFechaHora() {
    const ahora = new Date();
    const opcionesFecha = {
      weekday: 'long',  // día de la semana 
      day: 'numeric',   // día del mes 
      month: 'long',    // mes completo 
      year: 'numeric',  // año 
    };

    const opcionesHora = {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    };

    // Obtener fecha formateada
    const fecha = ahora.toLocaleDateString('es-ES', opcionesFecha);
    // Obtener hora formateada
    const hora = ahora.toLocaleTimeString('es-ES', opcionesHora);

    // Unir ambas partes en un solo string
    const fechaHora = `${fecha} ${hora}`;

    if (fechaElemento) fechaElemento.textContent = fechaHora;
  }

  actualizarFechaHora();
  setInterval(actualizarFechaHora, 1000); 

  //Recojemos los parametros que enviamos en casao de error y mostramos el div de error 
    const params = new URLSearchParams(window.location.search);
    const error = params.get("error");

    if (error === "usuario_inexistente") {
      document.getElementById("errorDNI").style.display = 'flex';
    }
});