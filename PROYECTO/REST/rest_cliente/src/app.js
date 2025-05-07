/**
 *  ==========================
 *          APP.JS
 *  ==========================
 * Script que realiza el inicio de sesión e incluye la fecha y la hora 
 * actual dinámicamente
 *
 * @package    GestionGuardias
 * @author     Adrian Pascual Marschal
 * @license    MIT
 * @param {HTMLElement|null} fechaElemento   Elemento donde se muestra la fecha y hora actuales.
 * @param {HTMLElement}       togglePassword  Botón/icono para alternar la visibilidad de la contraseña.
 * @param {HTMLInputElement}  passwordInput   Campo de entrada de contraseña cuyo tipo se alterna.
 * @param {HTMLElement}       eyeIcon         Icono visual que cambia según el estado de la contraseña.
 */
document.addEventListener('DOMContentLoaded', () => {
  // Seleccionamos el elemento donde mostraremos la fecha y hora actuales
  const fechaElemento = document.querySelector('.fecha-actual');
  // Input y botón para mostrar/ocultar contraseña
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("password");
  const eyeIcon = document.getElementById("eyeIcon");

  // Añadimos evento click al icono para alternar la visibilidad de la contraseña
  togglePassword.addEventListener("click", function () {
    // Comprobamos el tipo actual del input: 'password' o 'text'
    const isPassword = passwordInput.type === "password";
    // Cambiamos el tipo para mostrar/ocultar contraseña
    passwordInput.type = isPassword ? "text" : "password";
    // Cambiamos el icono en función del estado
    eyeIcon.textContent = isPassword ? "🙈" : "👁️";
  });

  /**
   * Función para actualizar la fecha y hora en pantalla.
   * - Formatea la fecha (día de la semana, día, mes, año)
   * - Formatea la hora (HH:MM:SS)
   *
   * @function actualizarFechaHora
   * @param {Intl.DateTimeFormatOptions} [opcionesFecha] Opciones de formateo de la fecha.
   * @param {('long')} [opcionesFecha.weekday]    Día de la semana completo (ej: "miércoles").
   * @param {('numeric')} [opcionesFecha.day]      Día del mes (ej: "7").
   * @param {('long')} [opcionesFecha.month]      Mes completo (ej: "mayo").
   * @param {('numeric')} [opcionesFecha.year]     Año (ej: "2025").
   * @param {Intl.DateTimeFormatOptions} [opcionesHora]    Opciones de formateo de la hora.
   * @param {('2-digit')} [opcionesHora.hour]     Hora en formato 2 dígitos (ej: "09").
   * @param {('2-digit')} [opcionesHora.minute]   Minutos en formato 2 dígitos (ej: "05").
   * @param {('2-digit')} [opcionesHora.second]   Segundos en formato 2 dígitos (ej: "07").
   * @returns {void}
   */
  function actualizarFechaHora() {
    const ahora = new Date();
    // Opciones para formatear la parte de la fecha
    const opcionesFecha = {
      weekday: 'long',  // día de la semana completo (ej: "miércoles")
      day: 'numeric',   // día del mes (ej: "7")
      month: 'long',    // mes completo (ej: "mayo")
      year: 'numeric',  // año (ej: "2025")
    };

    // Opciones para formatear la parte de la hora
    const opcionesHora = {
      hour: '2-digit',   // hora en formato 2 dígitos (ej: "09")
      minute: '2-digit', // minutos en formato 2 dígitos (ej: "05")
      second: '2-digit', // segundos en formato 2 dígitos (ej: "07")
    };

    // Obtenemos la fecha y hora formateadas en español (es-ES)
    const fecha = ahora.toLocaleDateString('es-ES', opcionesFecha);
    const hora = ahora.toLocaleTimeString('es-ES', opcionesHora);

    // Unimos fecha y hora en un solo string
    const fechaHora = `${fecha} ${hora}`;

    // Mostramos el string combinado en el elemento seleccionado
    if (fechaElemento) fechaElemento.textContent = fechaHora;
  }

  // Llamada inicial para mostrar la fecha/hora al cargar
  actualizarFechaHora();
  // Actualizamos la fecha/hora cada segundo (1000 ms)
  setInterval(actualizarFechaHora, 1000);

  /**
   * Gestión de errores al recibir parámetros en la URL
   * Si existe ?error=usuario_inexistente, mostramos un mensaje de error
   */
  const params = new URLSearchParams(window.location.search);
  const error = params.get("error");

  if (error === "usuario_inexistente") {
    // Mostramos el contenedor de error correspondiente
    document.getElementById("errorDNI").style.display = 'flex';
  }
});
