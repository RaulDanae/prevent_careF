document.addEventListener('DOMContentLoaded', () => {

    const menuToggle = document.querySelector('#menuToggle');
    const nav = document.querySelector('nav');

    // 🔐 Validación defensiva
    if (menuToggle && nav) {
        menuToggle.addEventListener('click', () => {
            nav.classList.toggle('open');
        });
    }

});