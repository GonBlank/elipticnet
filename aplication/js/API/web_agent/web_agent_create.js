import { toggleButtonState } from '../../functions/toggleButtonState.js';

document.addEventListener("DOMContentLoaded", function () {
    const createAgentButton = document.getElementById('createAgent');


    function validateHostData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}Error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        // Obtener los valores del formulario
        const url = document.getElementById('url').value;
        const alias = document.getElementById('alias').value;
        const requestTimeout = document.getElementById('requestTimeout').value;
        const ttfbThresholdCheck = document.getElementById('ttfbThresholdCheckbox').checked;
        const ttfbThresholdValue = document.getElementById('ttfbThresholdValue').value;

        const responseTimeThresholdCheckbox = document.getElementById('responseTimeThresholdCheckbox').checked;
        const responseTimeThresholdValue = document.getElementById('responseTimeThresholdValue').value;

        let isValid = true;

        // Validar los datos del formulario
        if (!url) {
            isValid = false;
            document.getElementById('url').classList.add('error');
            document.getElementById('urlError').textContent = 'Url is required.';
        }

        try {
            new URL(url); // Si no es una URL válida, lanzará un error
        } catch {
            isValid = false;
            document.getElementById('url').classList.add('error');
            document.getElementById('urlError').textContent = 'Invalid URL.';
        }

        if (ttfbThresholdCheck) {
            if (!ttfbThresholdValue) {
                isValid = false;
                document.getElementById('ttfbThresholdValue').classList.add('error');
                document.getElementById('ttfbThresholdValueError').textContent = 'Value required.';
            }
        }

        if (responseTimeThresholdCheckbox) {
            if (!responseTimeThresholdValue) {
                isValid = false;
                document.getElementById('responseTimeThresholdValue').classList.add('error');
                document.getElementById('responseTimeThresholdValueError').textContent = 'Value required.';
            }
        }

        if (alias.length > 25) {
            isValid = false;
            document.getElementById('alias').classList.add('error');
            document.getElementById('aliasError').textContent = 'Alias must be less than 25 characters.';
        }

        if (requestTimeout > 60) {
            isValid = false;
            document.getElementById('requestTimeout').classList.add('error');
            document.getElementById('requestTimeoutError').textContent = 'Timeout must be less than 60 sec.';
        }
        return isValid;
    }

    createAgentButton.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateHostData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState('createAgent', true);

        const url = document.getElementById('url');
        let alias = document.getElementById('alias');
        let requestTimeout = document.getElementById('requestTimeout');
        const sslExpiry = document.getElementById('sslExpiry-checkbox').checked;
        const domainExpiry = document.getElementById('domainExpiry-checkbox').checked;
        const checkSslError = document.getElementById('checkSslError-checkbox').checked;
        const ttfbThresholdValue = document.getElementById('ttfbThresholdValue').value;
        const responseTimeThresholdValue = document.getElementById('responseTimeThresholdValue').value;


        // Seleccionar el contenedor de transportes
        const transportSection = document.querySelector('.alert-transports');

        // Seleccionar solo los checkboxes dentro de .alert-transports que estén marcados
        const selectedCheckboxes = transportSection.querySelectorAll('.checkbox-wrapper-13 input[type="checkbox"]:checked');

        // Obtener los IDs de los checkboxes seleccionados
        const TransportsSelected = Array.from(selectedCheckboxes).map(checkbox => checkbox.id);

        // Crear un objeto con los datos del nuevo agente
        const newWebAgent = {
            url: url.value,
            alias: alias.value,
            requestTimeout: requestTimeout.value,
            sslExpiry: sslExpiry,
            domainExpiry: domainExpiry,
            checkSslError: checkSslError,
            transports: TransportsSelected,
            ttfbThresholdValue: parseFloat(ttfbThresholdValue),
            responseTimeThresholdValue: parseFloat(responseTimeThresholdValue)
        };

        console.log(newWebAgent);

        fetch('../php/API/web_agent/web_agent_create.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(newWebAgent),
        })
            .then(response => response.json())
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                if (!data.error) {
                    url.value = '';
                    alias.value = '';
                    requestTimeout.value = '';
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 2000);
                }
            })
            .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'))
            .finally(() => {
                toggleButtonState('createAgent', false);

            });
    });
});