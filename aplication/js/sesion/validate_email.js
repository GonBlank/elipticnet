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
    fetch('../php/sesion/validate_email.php', {
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
            ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
        })
        .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'));
}