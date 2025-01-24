// Crear un objeto FormData para enviar el parámetro por POST
const formData = new FormData();
formData.append('languageCode', (navigator.language || navigator.userLanguage).split('-')[0]);  // Aquí agregamos correctamente el valor del validation_hash
formData.append('time_zone', Intl.DateTimeFormat().resolvedOptions().timeZone);  // Aquí agregamos correctamente el valor del validation_hash

const configData = {
    timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    language: (navigator.language || navigator.userLanguage).split('-')[0],
};

// Enviar el parámetro a validate_email.php mediante POST
fetch('../php/user_config/setUserConfig.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify(configData),
})
    .then(response => {
        if (!response.ok) {
            ShowAlert('error', 'Error', `Response error: ${response.status}`, 'error');
            throw new Error(`[Response error]: ${response.status}`);
        }
    })
    .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'));
