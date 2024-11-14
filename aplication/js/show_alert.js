function ShowAlert(image, title, message, type) {
    // Crear la estructura de la alerta
    const alertSection = document.createElement('section');
    alertSection.className = `alert show ${type}`;

    alertSection.innerHTML = `
        <div class="icon">
            <img src="../img/icons/${image}.png">
        </div>
        <div class="body">
            <h1>${title}</h1>
            <p>${message}</p>
        </div>
    `;

    // Agregar la alerta dentro de <main class="dashboard">
    const dashboard = document.querySelector('main.dashboard');
    if (dashboard) {
        dashboard.appendChild(alertSection);
    }

    // Mostrar la alerta durante 2 segundos
    setTimeout(() => {
        // Cambiar la clase para iniciar la animación de ocultar
        alertSection.classList.remove('show');
        alertSection.classList.add('hide');

        // Remover la alerta del DOM después de que la animación termine
        setTimeout(() => {
            alertSection.remove();
        }, 900); // Duración de la animación .9s
    }, 3500); // Duración en pantalla .5s
}