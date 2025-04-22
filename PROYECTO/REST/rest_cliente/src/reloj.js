function updateClock() {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes();
    const seconds = now.getSeconds();

    const hourDeg = (360 / 12) * (hours % 12) + (360 / 12) * (minutes / 60);
    const minuteDeg = (360 / 60) * minutes;
    const secondDeg = (360 / 60) * seconds;

    document.getElementById("hour-hand").style.transform = `rotate(${hourDeg}deg)`;
    document.getElementById("minute-hand").style.transform = `rotate(${minuteDeg}deg)`;
    document.getElementById("second-hand").style.transform = `rotate(${secondDeg}deg)`;
}

// Actualiza el reloj cada segundo
setInterval(updateClock, 1000);

// Llama a la función una vez para inicializar
updateClock();
