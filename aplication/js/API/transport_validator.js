// Función para obtener parámetros de la URL
function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

// Verificar si el parámetro validation_hash existe
const validationHash = getUrlParameter('validation_hash');

if (validationHash) {
    // Crear un objeto FormData para enviar el parámetro por POST
    const formData = new FormData();
    formData.append('validation_hash', validationHash);  // Aquí agregamos correctamente el valor del validation_hash
    // Enviar el parámetro a validate_email.php mediante POST
    fetch('../php/API/transport_validator.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                ShowAlert('error', 'Error', `Response error: ${response.status}`, 'error');
                throw new Error(`[Response error]: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                return null;
            }
            loadView(data);
        })
        .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'));
}

function loadView(data) {
    const image = document.getElementById('image');
    const message = document.getElementById('message');

    switch (data.type) {
        case 'success':
            image.innerHTML = `               
                <svg class="success" xmlns = "http://www.w3.org/2000/svg" width = "16" height = "16" fill = "currentColor"
            class="bi bi-check-circle-fill" viewBox = "0 0 16 16" >
                <path
                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                </svg >`;
            message.innerHTML = `<p>${data.title}, ${data.message} </p>`;
            break;

        case 'error':
            image.innerHTML = `<svg class="error" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                    <path
                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                </svg>`;
            message.innerHTML = `<p>${data.title}, ${data.message} </p>`;
            break;
        case 'warning':
            image.innerHTML = `<svg class="warning" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                    <path
                        d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                </svg>`;
            message.innerHTML = `<p> ${data.title}, ${data.message} </p>
                                <a href="${data.link}">${data.link_text}</a>`;
            break;
        default:
            image.innerHTML = `<svg class="error" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                    <path
                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2" />
                </svg>`;
            message.innerHTML = message.innerHTML = `<p>${data.title}, ${data.message} </p>`;;
            break;
    }

}