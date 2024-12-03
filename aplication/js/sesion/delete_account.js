document.getElementById('delete-btn').addEventListener('click', async () => {
    // Enviar los datos al backend usando fetch
    fetch('../php/sesion/delete_account.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then(response => response.json())
        .then(data => {
            ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);

            if (!data.error){
                setTimeout(() => {
                    window.location.href = "login.html";
                }, 4000); // 4000 ms = 4 segundos
            }

        })
        .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))//
        .finally(() => {
            // Cerrar el diálogo
            const dialog = document.getElementById('delete-account');
            dialog.close(); // Cerrar el diálogo
        });
});