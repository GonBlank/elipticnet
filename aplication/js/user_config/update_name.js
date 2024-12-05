document.addEventListener("DOMContentLoaded", function () {
    const updateNameBtn = document.getElementById('update-name-btn');
    // Animación de carga del formulario
    function toggleButtonState(isLoading) {
        const textDiv = updateNameBtn.querySelector('.text');
        const loaderDiv = updateNameBtn.querySelector('.loader-hourglass');

        if (isLoading) {
            textDiv.classList.remove('show');
            textDiv.classList.add('hide');
            loaderDiv.classList.remove('hide');
            loaderDiv.classList.add('show');
            updateNameBtn.disabled = true;
        } else {
            textDiv.classList.remove('hide');
            textDiv.classList.add('show');
            loaderDiv.classList.remove('show');
            loaderDiv.classList.add('hide');
            updateNameBtn.disabled = false;
        }
    }

    function validateUserData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}-error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        // Obtener los valores del formulario
        const username = document.getElementById('user-name');
        let isValid = true;

        // Validar los datos del formulario
        if (!username || username.value.trim() === '') {
            isValid = false;
            document.getElementById('user-name').classList.add('error');
            document.getElementById('user-name-error').textContent = 'Username is required.';
        } else {
            const usernameLength = username.value.length;
            if (usernameLength < 3 || usernameLength > 15) {
                isValid = false;
                document.getElementById('user-name').classList.add('error');
                document.getElementById('user-name-error').textContent = 'Username must be between 3 and 15 characters.';
            }
        }
        return isValid;
    }

    updateNameBtn.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateUserData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState(true);

        // Obtener los valores del formulario
        const username = document.getElementById('user-name');

        const updatedUser = {
            username: username.value,
        };

        // Enviar los datos al backend usando fetch
        fetch('../php/user_config/update_username.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(updatedUser),
        })
            .then(response => response.json())
            .then(data => {
                if (data.state) {
                    // Capturar el ID y el mensaje de respuesta
                    ShowAlert('success', 'Success', 'Name updated successfully', 'success');

                    document.getElementById('lateralMenu-username').textContent = username.value;

                } else if (data.error) {
                    ShowAlert(data.type, data.title, data.message, data.type);
                }
            })
            .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))//
            .finally(() => {
                // Restablecer el estado del botón y cerrar el diálogo
                toggleButtonState(false);
            });
    });
});