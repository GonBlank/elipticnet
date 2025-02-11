function fetchTransports() {
    fetch('../php/API/transport_get_all_transports.php', {
        method: 'POST', // El método de la solicitud
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                ShowAlert('error', 'Error', `Response error: ${response.status}`, 'error');
                throw new Error(`[ERROR]: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                setTimeout(() => {
                    window.location.href = 'home.php';
                }, 2000);
            } else {
                removeLoadCurtain();
            }
            // Asegurarse de que los datos contienen transportes
            if (data.transports && data.transports.length > 0) {
                const emailTransportTable = document.getElementById('emailTransportTable');
                const telegramTransportTable = document.getElementById('telegramTransportTable');
                /*
                 * Si la seccion tiene transportes =>
                 * cambio la posicion del boton de agregar a la esquina superior derecha
                 */
                // Iterar sobre los transportes y verificar si existen en los datos
                Object.entries(transportTypes).forEach(([type, modalValue]) => {
                    if (data.transports.some(entry => entry.type === type)) {
                        changeButtonPosition(modalValue);
                    }
                });

                data.transports.forEach(transport => {

                    switch (transport.type) {
                        case 'email':
                            const emailRowElement = createRowTransport(transport);
                            emailTransportTable.appendChild(emailRowElement);
                            break;
                        case 'telegram':
                            const telegramRowElement = createRowTransport(transport);
                            telegramTransportTable.appendChild(telegramRowElement);

                            break;
                        default:
                            ShowAlert('warning', 'Warning', `Unsupported transport type: ${transport.type}`, 'warning');
                            break;
                    }
                });
            } else {
                ShowAlert('warning', 'Warning', 'Transports not found', 'warning');                
            }

        })
        .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'));
}

// Llamar la función al cargar la página

const transportTypes = {
    email: 'add_email',
    telegram: 'add_telegram'
};

window.onload = fetchTransports;

function createRowTransport(data) {
    const transport = document.createElement('article');
    transport.classList.add('row');
    extraInfo = transport_status(data.valid, data.id);
    transport.innerHTML = `<div class="transport_alias ${extraInfo.class}">
                            ${extraInfo.svg}
                        <h1>${data.alias}</h1>
                    </div>
                    <h1 class="transport_id"> ${data.transport_id}</h1>
                    <div class="transport_options">
                        ${extraInfo.resendCodeBtn}
                        <button class="delete openModal" data-id="${data.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-trash" viewBox="0 0 16 16">
                                <path
                                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                <path
                                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                            </svg>
                        </button>
                    </div >
    `;
    return transport; // Devuelve el elemento creado
}

function transport_status(valid, transportId) {

    if (valid) {
        return {
            'svg': `<svg xmlns = "http://www.w3.org/2000/svg" width = "16" height = "16" fill = "currentColor"
                    class="bi bi-check-circle-fill" viewBox = "0 0 16 16" >
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                    </svg > `,
            'class': 'validated',
            'resendCodeBtn': ''
        };
    } else {
        return {
            'svg': `
                    <span class="tooltip" >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z" />
                    </svg>
                    <span class="tooltip-text">Waiting for validation</span>
                    </span >`,
            'class': 'no_validated',
            'resendCodeBtn': `<span class="tooltip">
                            <button class="resend" data-id="${transportId}">
                            <div class="text show">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                                    <path
                                        d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                                </svg>
                                </div>
                            <div class="loader-hourglass hide"> <!-- Cambiado a hide para que esté oculto por defecto -->
                            <svg class="spinner-circle" id="load_latency_average" class="up-since"
                                xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 -960 960 960" width="16px">
                                <path
                                    d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
                            </svg>
                </div>
                            </button>
                            <span class="tooltip-text">Resend validation link</span>
                        </span>`


        };
    }

}


function changeButtonPosition(modalValue) {
    // Selecciona todos los botones con el atributo data-modal correspondiente
    const buttons = document.querySelectorAll(`[data-modal="${modalValue}"]`);

    // Iterar sobre todos los botones seleccionados
    buttons.forEach(button => {
        button.classList.toggle('hide'); // Alterna la clase 'hide' de forma más eficiente
    });
}