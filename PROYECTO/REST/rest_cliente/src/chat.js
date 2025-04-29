const chat = document.getElementById('chatWindow');
    if (chat) chat.scrollTop = chat.scrollHeight;

    var profes = document.querySelector("#agenda");
 

            // Aplicar fondo azul al seleccionado
            profes.addEventListener('change', function () {
                    for (let i = 0; i < profes.options.length; i++) {
                            profes.options[i].classList.remove('bg-primary', 'text-white');
                    }
                    profes.options[profes.selectedIndex].classList.add('bg-primary', 'text-white');
            });

            // Inicializar el seleccionado con fondo azul
            profes.options[profes.selectedIndex].classList.add('bg-primary', 'text-white');
    