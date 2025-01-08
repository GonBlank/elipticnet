// Escucha los clics en el body
document.body.addEventListener("click", function (event) {
    // Verifica si el elemento clicado tiene la clase "resend"
    if (event.target.classList.contains("resend")) {
        // Obtiene el valor del atributo data-id
        const transportId = event.target.getAttribute("data-id");
        const resendBtn = document.querySelector(`button.resend[data-id="${transportId}"]`);


        // Animación de carga
        function toggleButtonState(isLoading) {
            const textDiv = resendBtn.querySelector('.text');
            const loaderDiv = resendBtn.querySelector('.loader-hourglass');

            if (isLoading) {
                textDiv.classList.remove('show');
                textDiv.classList.add('hide');
                loaderDiv.classList.remove('hide');
                loaderDiv.classList.add('show');
                resendBtn.disabled = true;
            } else {
                textDiv.classList.remove('hide');
                textDiv.classList.add('show');
                loaderDiv.classList.remove('show');
                loaderDiv.classList.add('hide');
                resendBtn.disabled = false;
            }
        }

        // Cambiar el estado del botón a "cargando"
        toggleButtonState(true);

        // Realizar la solicitud de eliminación
        fetch(`../php/API/transport_resend_validation_code.php?transportId=${transportId}`, {
            method: 'GET',
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('La solicitud falló con el código de estado ' + response.status);
                }
                return response.json();
            })
            .then((data) => {
                //ShowAlert(data.type, data.title, data.message, data.type);
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
            })
            .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))//
            .finally(() => {
                // Restaurar el estado del botón
                toggleButtonState(false);
            });

    }
});
