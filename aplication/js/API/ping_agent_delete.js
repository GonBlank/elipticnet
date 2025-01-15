document.addEventListener("DOMContentLoaded", function () {
    const deleteHostButton = document.getElementById('delete-btn');

    function toggleButtonState(isLoading) {
        const textDiv = deleteHostButton.querySelector('.text');
        const loaderDiv = deleteHostButton.querySelector('.loader-hourglass');

        if (isLoading) {
            textDiv.classList.remove('show');
            textDiv.classList.add('hide');
            loaderDiv.classList.remove('hide');
            loaderDiv.classList.add('show');
            deleteHostButton.disabled = true;
        } else {
            textDiv.classList.remove('hide');
            textDiv.classList.add('show');
            loaderDiv.classList.remove('show');
            loaderDiv.classList.add('hide');
            deleteHostButton.disabled = false;
        }
    }


    // Referencias a los botones y al diálogo
    const deleteDialog = document.getElementById("delete-host");
    const deleteBtn = document.getElementById("delete-btn");


    // Botón para confirmar la eliminación
    deleteBtn.addEventListener("click", async () => {
        toggleButtonState(true)
        fetch(`../php/API/ping_agent_delete.php?hostId=${hostId}`)
            .then(response => {
                if (!response.ok) {
                    ShowAlert('error', 'Error', `Failed to delete the host:  ${response.status}`, 'error');
                }
                return response.json();
            })
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                // Esperar 2 segundos y luego redirigir al dashboard
                setTimeout(() => {
                    window.location.href = "../public/home.php";
                }, 2000);

                // Cerrar el diálogo
                deleteDialog.close();
            })
            .catch(error => {
                ShowAlert('error', 'Error', `Failed to delete the host: ${error.message || error}`, 'error');
            })
            .finally(() => {
                // Restaurar el estado del botón
                toggleButtonState(false);
            });

    });






});
