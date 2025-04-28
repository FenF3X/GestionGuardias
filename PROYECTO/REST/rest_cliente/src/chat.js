const chat = document.getElementById('chatWindow');
    if (chat) chat.scrollTop = chat.scrollHeight;

    var profes = document.querySelector("#agenda");
    if (profes) {
            for (let i = 0; i < profes.options.length; i++) {
                    if (i % 2 === 0) {
                            profes.options[i].style.backgroundColor = "#d3d3d3"; // Gris más oscuro para los pares
                    }
                    profes.options[i].style.padding = "10px"; // Agregar más padding a todos
            }

            // Aplicar fondo azul al seleccionado
            profes.addEventListener('change', function () {
                    for (let i = 0; i < profes.options.length; i++) {
                            profes.options[i].classList.remove('bg-primary', 'text-white');
                    }
                    profes.options[profes.selectedIndex].classList.add('bg-primary', 'text-white');
            });

            // Inicializar el seleccionado con fondo azul
            profes.options[profes.selectedIndex].classList.add('bg-primary', 'text-white');
    }