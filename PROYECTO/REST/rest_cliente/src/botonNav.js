document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.querySelector('.navbar-toggler');
    const menuContent = document.getElementById('navbarContent');

    toggleButton.addEventListener('click', function () {
      menuContent.classList.toggle('show');
    });
  });