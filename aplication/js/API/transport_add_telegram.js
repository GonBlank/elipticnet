import { toggleButtonState } from '../functions/toggleButtonState.js';

document.addEventListener("DOMContentLoaded", function () {
    const addTelegramBtn = document.getElementById('addTelegramBtn');

    function validateUserData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}-error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        // Obtener los valores del formulario
        const alias = document.getElementById('telegramAlias');
        const telegramId = document.getElementById('telegramId');
        let isValid = true;

        // Validar alias
        if (!alias || alias.value.trim() === '') {
            isValid = false;
            alias.classList.add('error');
            document.getElementById('telegramAlias-error').textContent = 'Alias is required.';
        } else {
            const aliasLength = alias.value.length;
            if (aliasLength < 3 || aliasLength > 15) {
                isValid = false;
                alias.classList.add('error');
                document.getElementById('telegramAlias-error').textContent = 'Alias must be between 3 and 15 characters.';
            }
        }

        // Validar telegramId
        if (!telegramId || telegramId.value.trim() === '') {
            isValid = false;
            telegramId.classList.add('error');
            document.getElementById('telegramId-error').textContent = 'Telegram ID is required.';
        } else {
            const value = telegramId.value.trim();
            if (!/^-?\d+$/.test(value) || !Number.isInteger(Number(value)) || Math.abs(Number(value)) > 9999999999) {
                isValid = false;
                telegramId.classList.add('error');
                document.getElementById('telegramId-error').textContent = 'Telegram ID must be a valid integer.';
            }
        }
        return isValid;
    }

    addTelegramBtn.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateUserData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState('addTelegramBtn', true);

        // Obtener los valores del formulario
        const alias = document.getElementById('telegramAlias');
        const transport_id = document.getElementById('telegramId');

        // Crear un objeto con los datos del nuevo host
        // Crear un objeto con los datos del nuevo usuario
        const newTransport = {
            alias: alias.value,
            transport_id: transport_id.value,
            type: 'telegram'
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
                toggleButtonState('addTelegramBtn', false);
                // Cerrar el diálogo
                const dialog = document.getElementById('add_telegram');
                dialog.close(); // Cerrar el diálogo
            });
    });
});
