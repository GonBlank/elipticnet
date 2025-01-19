import { toggleButtonState } from '../functions/toggleButtonState.js';

document.addEventListener("DOMContentLoaded", function () {
    const addEmailBtn = document.getElementById('addEmailBtn');

    function validateUserData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}-error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        // Obtener los valores del formulario
        const alias = document.getElementById('email_alias');
        const email = document.getElementById('transport-email');

        let isValid = true;

        // Validar los datos del formulario
        if (!alias || alias.value.trim() === '') {
            isValid = false;
            document.getElementById('email_alias').classList.add('error');
            document.getElementById('email_alias-error').textContent = 'Alias is required.';
        } else {
            const aliasLength = alias.value.length;
            if (aliasLength < 3 || aliasLength > 15) {
                isValid = false;
                document.getElementById('email_alias').classList.add('error');
                document.getElementById('email_alias-error').textContent = 'alias must be between 3 and 15 characters.';
            }
        }

        if (!email || email.value.trim() === '') {
            isValid = false;
            document.getElementById('transport-email').classList.add('error');
            document.getElementById('transport-email-error').textContent = 'Email is required.';
        } else {
            const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailRegex.test(email.value)) {
                isValid = false;
                document.getElementById('transport-email').classList.add('error');
                document.getElementById('transport-email-error').textContent = 'Invalid email format.';
            }
        }

        return isValid;
    }

    addEmailBtn.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateUserData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState('addEmailBtn', true);

        // Obtener los valores del formulario
        const alias = document.getElementById('email_alias');
        const transport_id = document.getElementById('transport-email');

        // Crear un objeto con los datos del nuevo host
        // Crear un objeto con los datos del nuevo usuario
        const newTransport = {
            alias: alias.value,
            transport_id: transport_id.value,
            type: 'email'
        };

        // Enviar los datos al backend usando fetch
        fetch('../php/API/transport_add.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(newTransport),
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
                    alias.value = '';
                    transport_id.value = '';

                    setTimeout(function () {
                        location.reload();
                    }, 3900);
                }

            })
            .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'))
            .finally(() => {
                // Restablecer el estado del botón
                toggleButtonState('addEmailBtn', false);

                // Cerrar el diálogo
                const dialog = document.getElementById('add_email');
                dialog.close(); // Cerrar el diálogo
            });
    });
});
