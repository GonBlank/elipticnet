/*
 * Configura la zona horaria y el idioma que tiene el navegador del cliente
 * Actúa para usuarios que se registran usando google
 */


const configData = {
    timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    language: (navigator.language || navigator.userLanguage).split('-')[0],
};

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
