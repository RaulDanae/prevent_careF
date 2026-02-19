document.querySelector('form').addEventListener('submit', function(e) {
    const inputs = document.querySelectorAll('input');
    let valido = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'red';
            valido = false;
        } else {
            input.style.borderColor = '#ccc';
        }
    });

    if (!valido) {
        e.preventDefault();
        alert('Completa todos los campos');
    }
});

/// Procedimiento para transicion de fondo  /////

const images = [
    BASE_URL + '/assets/img/fondo_1.png',
    BASE_URL + '/assets/img/fondo_2.png',
    BASE_URL + '/assets/img/fondo_3.png'
];

let currentIndex = 0;
const bg = document.getElementById('bgSlider');

// Imagen inicial
bg.style.backgroundImage = `url(${images[currentIndex]})`;

setInterval(() => {
    // Fade out
    bg.style.opacity = 0;

    setTimeout(() => {
        currentIndex = (currentIndex + 1) % images.length;
        bg.style.backgroundImage = `url(${images[currentIndex]})`;

        // Fade in
        bg.style.opacity = 1;
    }, 1000); // coincide con la transición CSS

}, 10000); // 10 segundos