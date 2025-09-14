document.addEventListener('DOMContentLoaded', () => {
    const gameContainer = document.getElementById('game-container');

    // Lista de 10 palabras relacionadas con redes sociales
    const socialMediaNames = [
        'Facebook',
        'Instagram',
        'TikTok',
        'Twitter',
        'LinkedIn',
        'Pinterest',
        'Snapchat',
        'YouTube',
        'WhatsApp',
        'Reddit'
    ];

    // Crear y añadir cada carta al contenedor
    socialMediaNames.forEach(word => {
        // Crear el elemento principal de la carta
        const card = document.createElement('div');
        card.classList.add('card');

        // Crear la cara frontal (la que se ve al inicio)
        const cardFront = document.createElement('div');
        cardFront.classList.add('card-face', 'card-front');
        cardFront.innerHTML = '&#x1F4F1;'; // Ícono de un móvil

        // Crear la cara trasera (la que contiene la palabra)
        const cardBack = document.createElement('div');
        cardBack.classList.add('card-face', 'card-back');
        cardBack.textContent = word;

        card.appendChild(cardFront);
        card.appendChild(cardBack);

        // Añadir el evento para voltear la carta al hacer clic
        card.addEventListener('click', () => {
            card.classList.toggle('flipped');
        });

        gameContainer.appendChild(card);
    });
});