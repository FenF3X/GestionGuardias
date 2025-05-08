/**
 *  ==========================
 *          calendar.js
 *  ==========================
 * Script que controla todos los eventos de los inputs de las fechas,
 * referente a verInformes, verAsistencias, guardiasRealizdas y consultaAusencias
 * @function initializeFlatpickrInstances
 * @description Registra un listener para el evento DOMContentLoaded y configura Flatpickr en varios inputs.

 *
*/
document.addEventListener('DOMContentLoaded', () => {
     /**
     * @constant {Object} commonOpts
     * @description Opciones compartidas para todas las instancias de Flatpickr.
     * @property {boolean} disableMobile      - Desactiva la versión móvil nativa.
     * @property {boolean} altInput           - Habilita un input alternativo estilizado.
     * @property {string} altInputClass       - Clase CSS aplicada al input alternativo.
     * @property {string} locale              - Localización de Flatpickr (ej. "es").
     * @property {function(Array<Date>, string, Object)} onReady
     *    - Callback que se ejecuta una vez que el calendario está renderizado.
     */
    const commonOpts = {
      
      disableMobile: true,
      altInput: true,
      altInputClass: "input-select-custom",
      locale: "es",
          /**
       * @callback onReady
       * @description Ajusta el borde del contenedor del calendario al inicializarse.
       * @param {Array<Date>} selectedDates - Fechas inicialmente seleccionadas.
       * @param {string} dateStr            - Fecha formateada como string.
       * @param {Object} instance           - Instancia de Flatpickr.
       */
      onReady(_, __, instance) {
        instance.calendarContainer.style.border = "2px solid #1e3a5f";
      }
    };
      /**
     * @function initFechaPicker
     * @description Inicializa Flatpickr en el input de fecha.
     * @param {string} selector               - Selector CSS del input ("#fecha").
     * @param {Object} options                - Opciones específicas para el selector de fecha.
     * @param {string} options.dateFormat     - Formato de valor enviado (YYYY-MM-DD).
     * @param {string} options.altFormat      - Formato de fecha visible (j F, Y).
     * @param {string} options.monthSelectorType - Tipo de selector de mes ("dropdown").
     */
    flatpickr("#fecha", {
      ...commonOpts,
      dateFormat: "Y-m-d",
      altFormat: "j F, Y",
      monthSelectorType: "dropdown"
    });
  
  /**
     * @function initDayPicker
     * @description Inicializa Flatpickr en el input de día.
     * @param {string} selector           - Selector CSS del input ("#dia").
     * @param {Object} options            - Opciones específicas para el selector de día.
     * @param {string} options.dateFormat - Formato de valor enviado (YYYY-MM-DD).
     * @param {string} options.altFormat  - Formato de fecha visible (j F, Y).
     */
        flatpickr("#dia", {
      ...commonOpts,
      dateFormat: "Y-m-d",
      altFormat: "j F, Y"
    });
  /**
     * @function initWeekPicker
     * @description Inicializa Flatpickr en el input de semana.
     * @param {string} selector           - Selector CSS del input ("#semana").
     * @param {Object} options            - Opciones específicas para el selector de semana.
     * @param {string} options.dateFormat - Formato de valor enviado (YYYY-MM-DD).
     * @param {string} options.altFormat  - Formato de fecha visible (j F, Y).
     */
    flatpickr("#semana", {
      ...commonOpts,
      dateFormat: "Y-m-d",
      altFormat: "j F, Y"
    });
    /**
     * @function initMonthPicker
     * @description Inicializa Flatpickr con monthSelectPlugin para selección de mes completo.
     * @param {string} selector           - Selector CSS del input ("#mes").
     * @param {Object} options            - Opciones específicas para el selector de mes.
     * @param {Array} options.plugins     - Array de instancias de plugins de Flatpickr.
     */
    flatpickr("#mes", {
    disableMobile: true,
    altInput: true,
    altInputClass: "input-select-custom",
    locale: "es",
    plugins: [
        /**
         * @constructor monthSelectPlugin
         * @description Plugin de Flatpickr para seleccionar meses completos.
         * @param {Object} config
         * @param {boolean} config.shorthand    - Usa nombres completos de meses (false) o abreviados.
         * @param {string} config.dateFormat    - Formato de valor enviado (YYYY-MM).
         * @param {string} config.altFormat     - Formato visible en el input (YYYY-MM).
         */
      new monthSelectPlugin({
        shorthand: false,    // meses completos (“Enero”, “Febrero”…)
        dateFormat: "Y-m",   // valor que se envía: “2025-05”
        altFormat:  "Y-m"    // formato visible en el input: “2025-05”
      })
    ],
    /**
       * @callback onReady
       * @description Ajusta el borde del calendario tras renderizarse.
       * @param {Array<Date>} selectedDates - Fechas inicialmente seleccionadas.
       * @param {string} dateStr            - Fecha formateada como string.
       * @param {Object} instance           - Instancia de Flatpickr.
       */
    onReady(_, __, instance) {
      instance.calendarContainer.style.border = "2px solid #1e3a5f";
    }
  });
  flatpickr("#fecha_inicio", {
    ...commonOpts,
    dateFormat: "Y-m-d",
    altFormat:  "j F, Y"
  });

  // Picker para fecha fin de plazo
  flatpickr("#fecha_fin", {
    ...commonOpts,
    dateFormat: "Y-m-d",
    altFormat:  "j F, Y"
  });
  
});
document.addEventListener('DOMContentLoaded', () => {
  const commonOpts = {
    disableMobile: true,
    altInput: true,
    altInputClass: "input-select-custom",
    locale: "es",
    onReady(_, __, instance) {
      instance.calendarContainer.style.border = "2px solid #1e3a5f";
    }
  };

  // Inicializa Flatpickr en 'Desde'
  flatpickr("#fecha_inicio", {
    ...commonOpts,
    dateFormat: "Y-m-d",
    altFormat: "j F, Y"
  });

  // Inicializa Flatpickr en 'Hasta'
  flatpickr("#fecha_fin", {
    ...commonOpts,
    dateFormat: "Y-m-d",
    altFormat: "j F, Y"
  });
});
