document.addEventListener('DOMContentLoaded', () => {
    const socialContainer = document.getElementById('social-items-container');
    const featureContainer = document.getElementById('feature-items-container');
    const lineCanvas = document.getElementById('line-canvas');
    const body = document.body;
    const timerElement = document.getElementById('timer');

    const gameData = [
        { "id": 1, "name": "TikTok", "logo": "https://upload.wikimedia.org/wikipedia/en/a/a9/TikTok_logo.svg", "feature": "Plataforma centrada en videos cortos virales." },
        { "id": 2, "name": "Instagram", "logo": "https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png", "feature": "Compartir fotos y videos a través de 'Historias' y 'Reels'." },
        { "id": 3, "name": "LinkedIn", "logo": "https://upload.wikimedia.org/wikipedia/commons/c/ca/LinkedIn_logo_initials.png", "feature": "Red social profesional para contactos laborales y networking." },
        { "id": 4, "name": "X (Twitter)", "logo": "https://upload.wikimedia.org/wikipedia/commons/c/ce/X_logo_2023.svg", "feature": "Publicaciones de texto cortas limitadas a 280 caracteres." },
        { "id": 5, "name": "Facebook", "logo": "https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg", "feature": "Conectar con amigos y familiares a través de perfiles y grupos." },
        { "id": 6, "name": "Pinterest", "logo": "https://upload.wikimedia.org/wikipedia/commons/0/08/Pinterest-logo.png", "feature": "Crear tableros visuales para guardar y organizar ideas (pines)." },
        { "id": 7, "name": "Snapchat", "logo": "https://upload.wikimedia.org/wikipedia/en/c/c4/Snapchat_logo.svg", "feature": "Mensajes y fotos que desaparecen después de ser vistos." },
        { "id": 8, "name": "Reddit", "logo": "https://upload.wikimedia.org/wikipedia/en/b/bd/Reddit_Logo_Icon.svg", "feature": "Comunidades (subreddits) para discutir sobre temas específicos." },
        { "id": 9, "name": "YouTube", "logo": "https://upload.wikimedia.org/wikipedia/commons/0/09/YouTube_full-color_icon_%282017%29.svg", "feature": "Plataforma para subir, ver y compartir videos de larga duración." },
        { "id": 10, "name": "Twitch", "logo": "https://upload.wikimedia.org/wikipedia/commons/d/d3/Twitch_Glitch_Logo_Purple.svg", "feature": "Servicio de streaming en vivo, principalmente para videojuegos." }
    ];

    let activeSocialCard = null;
    let isConnecting = false;
    let matchedPairs = 0;
    let timerInterval = null;
    let permanentLines = [];

    const previewLine = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    previewLine.setAttribute('class', 'line-preview');
    lineCanvas.appendChild(previewLine);

    function initGame() {
        resetGameState();

        const shuffledFeatures = [...gameData].sort(() => Math.random() - 0.5);

        gameData.forEach(item => {
            const card = createCard('social', item);
            socialContainer.appendChild(card);
            card.addEventListener('click', handleSocialClick);
        });

        shuffledFeatures.forEach(item => {
            const card = createCard('feature', item);
            featureContainer.appendChild(card);
            card.addEventListener('click', handleFeatureClick);
        });

        startTimer();
    }

    function resetGameState() {
        socialContainer.innerHTML = '';
        featureContainer.innerHTML = '';
        lineCanvas.innerHTML = '';
        lineCanvas.appendChild(previewLine);
        matchedPairs = 0;
        permanentLines = [];
        resetConnectionState();
        stopTimer();
        if (timerElement) timerElement.textContent = '00:00';
    }

    function createCard(type, item) {
        const card = document.createElement('div');
        card.className = `card ${type}-card`;
        card.dataset.id = item.id;

        if (type === 'social') {
            const img = document.createElement('img');
            img.src = item.logo;
            img.alt = item.name; // Important for accessibility
            img.className = 'social-logo';
            card.appendChild(img);

            const span = document.createElement('span');
            span.textContent = item.name;
            card.appendChild(span);
        } else {
            const span = document.createElement('span');
            span.textContent = item.feature;
            card.appendChild(span);
        }
        return card;
    }

    function handleSocialClick(e) {
        const clickedCard = e.currentTarget;
        if (clickedCard.classList.contains('matched')) return;

        if (isConnecting && activeSocialCard === clickedCard) {
            resetConnectionState();
        } else {
            resetConnectionState();
            isConnecting = true;
            activeSocialCard = clickedCard;
            activeSocialCard.classList.add('active');
            body.classList.add('is-connecting');
            previewLine.style.display = 'block';
        }
    }

    function handleFeatureClick(e) {
        const clickedCard = e.currentTarget;
        if (!isConnecting || clickedCard.classList.contains('matched')) return;

        const socialId = activeSocialCard.dataset.id;
        const featureId = clickedCard.dataset.id;

        if (socialId === featureId) {
            // Correct match
            activeSocialCard.classList.add('matched');
            clickedCard.classList.add('matched');
            const lineInfo = {
                startElement: activeSocialCard,
                endElement: clickedCard,
                isCorrect: true
            };
            permanentLines.push(lineInfo);
            drawPermanentLines();
            matchedPairs++;
            checkGameCompletion();
        } else {
            // Incorrect match
            const lineInfo = {
                startElement: activeSocialCard,
                endElement: clickedCard,
                isCorrect: false
            };
            permanentLines.push(lineInfo);
            drawPermanentLines();
            clickedCard.classList.add('shake');
            setTimeout(() => {
                clickedCard.classList.remove('shake');
                permanentLines.pop(); // Remove incorrect line info
                drawPermanentLines();
            }, 800);
        }
        resetConnectionState();
    }

    function drawPermanentLines() {
        // Clear only permanent lines, keep preview line
        const permanentLineElements = lineCanvas.querySelectorAll('.line-correct, .line-incorrect');
        permanentLineElements.forEach(line => line.remove());

        permanentLines.forEach(lineInfo => {
            const startPoint = getElementCenter(lineInfo.startElement);
            const endPoint = getElementCenter(lineInfo.endElement);
            createPermanentLine(startPoint, endPoint, lineInfo.isCorrect);
        });
    }


    window.addEventListener('mousemove', (e) => {
        if (!isConnecting) return;
        const startPoint = getElementCenter(activeSocialCard);
        const endPoint = { x: e.clientX, y: e.clientY };
        const pathData = `M${startPoint.x},${startPoint.y} L${endPoint.x},${endPoint.y}`;
        previewLine.setAttribute('d', pathData);
    });

    function createPermanentLine(start, end, isCorrect) {
        const line = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        const pathData = `M${start.x},${start.y} L${end.x},${end.y}`;
        line.setAttribute('d', pathData);
        line.setAttribute('class', isCorrect ? 'line-correct' : 'line-incorrect');
        lineCanvas.appendChild(line);
        if (!isCorrect) {
            setTimeout(() => line.remove(), 800);
        }
    }

    function resetConnectionState() {
        isConnecting = false;
        if (activeSocialCard) {
            activeSocialCard.classList.remove('active');
        }
        activeSocialCard = null;
        body.classList.remove('is-connecting');
        previewLine.style.display = 'none';
    }

    function getElementCenter(el) {
        const rect = el.getBoundingClientRect();
        return {
            x: rect.left + rect.width / 2 + window.scrollX,
            y: rect.top + rect.height / 2 + window.scrollY
        };
    }

    function checkGameCompletion() {
        if (matchedPairs === gameData.length) {
            stopTimer();
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('feedback-modal'));
                const feedbackText = document.getElementById('feedback-text');
                feedbackText.textContent = `¡Felicidades! Has completado el juego en ${timerElement.textContent}.`;
                modal.show();
                setTimeout(() => {
                    modal.hide();
                    initGame();
                }, 4000);
            }, 500);
        }
    }

    function startTimer() {
        stopTimer(); // Ensure no multiple intervals are running
        let seconds = 0;
        if (!timerElement) return;

        timerInterval = setInterval(() => {
            seconds++;
            const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
            const secs = (seconds % 60).toString().padStart(2, '0');
            timerElement.textContent = `${mins}:${secs}`;
        }, 1000);
    }

    function stopTimer() {
        clearInterval(timerInterval);
    }

    window.addEventListener('resize', drawPermanentLines);

    initGame();
});
