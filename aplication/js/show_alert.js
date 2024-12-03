function ShowAlert(image, title, message, type, link_text = null, link = null) {
    // Crear la estructura de la alerta
    const alertSection = document.createElement('section');
    alertSection.className = `alert show ${type}`;

    // Contenido dinámico del cuerpo de la alerta
    const linkHTML = link_text && link ? `<a href="${link}">${link_text}</a>` : '';
    alertSection.innerHTML = `
        <div class="icon">
            <img src="../img/icons/${image}.png" alt="${title}">
        </div>
        <div class="body">
            <h1>${title}</h1>
            <p>${message}</p>
            ${linkHTML}
        </div>
    `;

    // Agregar la alerta dentro de <main class="dashboard">
    const dashboard = document.querySelector('main.dashboard');
    if (dashboard) {
        dashboard.appendChild(alertSection);
    }

    // Manejo del tiempo de visibilidad de la alerta
    setTimeout(() => {
        alertSection.classList.replace('show', 'hide'); // Reemplazar clase directamente
        setTimeout(() => alertSection.remove(), 900); // Remover tras la animación
    }, 3900);
}
