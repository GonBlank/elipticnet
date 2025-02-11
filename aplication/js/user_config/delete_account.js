document.addEventListener("DOMContentLoaded", function () {
    const deleteBtn = document.getElementById('delete-btn');

    function toggleButtonState(isLoading) {
        const textDiv = deleteBtn.querySelector('.text');
        const loaderDiv = deleteBtn.querySelector('.loader-hourglass');

        if (isLoading) {
            textDiv.classList.remove('show');
            textDiv.classList.add('hide');
            loaderDiv.classList.remove('hide');
            loaderDiv.classList.add('show');
            deleteBtn.disabled = true;
        } else {
            textDiv.classList.remove('hide');
            textDiv.classList.add('show');
            loaderDiv.classList.remove('show');
            loaderDiv.classList.add('hide');
            deleteBtn.disabled = false;
        }
    }

    // Botón para confirmar la eliminación
    deleteBtn.addEventListener("click", async () => {
        toggleButtonState(true);

        // Enviar los datos al backend usando fetch
        fetch('../php/user_config/delete_account.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
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

                if (!data.error) {
                    setTimeout(() => {
                        window.location.href = "login.php";
                    }, 4000); // 4000 ms = 4 segundos
                }

            })
            .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'))
            .finally(() => {
                toggleButtonState(false);
                // Cerrar el diálogo
                const dialog = document.getElementById('delete-account');
                dialog.close();
            });


    });
});
