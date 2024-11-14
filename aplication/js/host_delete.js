document.addEventListener("DOMContentLoaded", function() {
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
        try {
            // Mostrar el loader (si tienes una función para ello)
            toggleButtonState(true);

            // Realizar la solicitud de eliminación
            const response = await fetch(`../php/delete_host_by_id.php?hostId=${hostId}`);

            if (response.ok) {
                // Llamar a la función ShowAlert
                ShowAlert('success', 'Success', 'Host deleted successfully', 'success');

                // Esperar 2 segundos y luego redirigir al dashboard
                setTimeout(() => {
                    window.location.href = "../public/home.php";
                }, 2000);

                // Cerrar el diálogo
                deleteDialog.close();
            } else {
                const errorData = await response.json();
                ShowAlert('error', 'Error', errorData.detail, 'error');
            }
        } catch (error) {
            ShowAlert('error', 'Error', 'An error occurred while deleting the host.', 'error');
        } finally {
            // Ocultar el loader (si tienes una función para ello)
            toggleButtonState(false);
        }
    });
});
