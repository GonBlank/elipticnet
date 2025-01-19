//Maneja la creación, aparición y desaparicion de alertas en la app
function ShowAlert(image, title, message, type, link_text = null, link = null) {
    // Crear la estructura de la alerta
    const alertArticle = document.createElement('article');
    alertArticle.className = `alert show ${type}`;

    // Contenido dinámico del cuerpo de la alerta
    const linkHTML = link_text && link ? `<a href="${link}">${link_text}</a>` : '';

    alertArticle.innerHTML = `
            <div class="alert-icon ${type}">
                <img src="../img/icons/${image}.png" alt="${title}">
            </div>
            <div class="alert-text">
                <h1>${title}</h1>
                <p>${message} ${linkHTML}</p>
            </div>
            `;

    // Agregar la alerta dentro de <main class="dashboard">
    const body = document.querySelector('body');
    if (body) {
        body.appendChild(alertArticle);
    }

    // Manejo del tiempo de visibilidad de la alerta
    setTimeout(() => {
        alertArticle.classList.replace('show', 'hide'); // Reemplazar clase directamente
        setTimeout(() => alertArticle.remove(), 900); // Remover tras la animación
    }, 3900);
}