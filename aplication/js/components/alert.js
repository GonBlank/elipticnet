//Maneja la creación, aparición y desaparicion de alertas en la app
function ShowAlert(image, title, message, type, link_text = null, link = null) {
    // Crear la estructura de la alerta
    const alertArticle = document.createElement('article');
    alertArticle.className = `alert show ${type}`;

    // Contenido dinámico del cuerpo de la alerta
    const linkHTML = link_text && link ? `<a href="${link}">${link_text}</a>` : '';
    const svgImage = selectAlertImage(image);
    alertArticle.innerHTML = `
            <div class="alert-icon ${type}">
             ${svgImage}
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
        alertArticle.classList.replace('show', 'hide');
        setTimeout(() => alertArticle.remove(), 2000); // Remuele el objeto del DOM luego de 3 segundos
    }, 5500);//Tiempo que se muestra la alerta en pantalla
}

function selectAlertImage(image) {
    switch (image) {
        case 'error':
            return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="${getComputedStyle(document.documentElement).getPropertyValue('--alert-error').trim()}" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                    </svg>`;
        case 'success':
            return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="${getComputedStyle(document.documentElement).getPropertyValue('--alert-success').trim()}" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                    </svg>`;

        case 'warning':
            return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="${getComputedStyle(document.documentElement).getPropertyValue('--alert-warning').trim()}" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                    </svg>`;

        default:
            return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="${getComputedStyle(document.documentElement).getPropertyValue('--text-color').trim()}" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z" />
                    </svg>`;


    }
}