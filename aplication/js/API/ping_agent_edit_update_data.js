import { toggleButtonState } from '../functions/toggleButtonState.js';


document.addEventListener("DOMContentLoaded", function () {
    const updateAgentButton = document.getElementById('updateAgent');

    function validateHostData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}Error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        // Obtener los valores del formulario
        const hostName = document.getElementById('hostName').value;
        const threshold_check = document.getElementById('thresholdCheckbox').checked;
        const thresholdValue = document.getElementById('thresholdValue').value;
        let isValid = true;

        if (threshold_check) {
            if (!thresholdValue) {
                isValid = false;
                document.getElementById('thresholdValue').classList.add('error');
                document.getElementById('thresholdValueError').textContent = 'Value required.';
            }
        }

        // Validar los datos del formulario
        if (!hostName) {
            isValid = false;
            document.getElementById('hostName').classList.add('error');
            document.getElementById('hostNameError').textContent = 'Host name is required.';
        }

        return isValid;
    }

    updateAgentButton.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateHostData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState('updateAgentButton', true);

        // Obtener los valores del formulario
        const hostName = document.getElementById('hostName');
        const description = document.getElementById('hostDescription');
        const threshold_check = document.getElementById('thresholdCheckbox').checked;
        const threshold = document.getElementById('thresholdValue');

        // Seleccionar el contenedor de transportes
        const transportSection = document.querySelector('.alert-transports');

        // Seleccionar solo los checkboxes dentro de .alert-transports que estén marcados
        const selectedCheckboxes = transportSection.querySelectorAll('.checkbox-wrapper-13 input[type="checkbox"]:checked');

        // Obtener los IDs de los checkboxes seleccionados
        const TransportsSelected = Array.from(selectedCheckboxes).map(checkbox => checkbox.id);

        // Crear un objeto con los datos del nuevo host
        const newHost = {
            id: hostId,
            name: hostName.value,
            description: description.value,
            transports: TransportsSelected
        };


        // Agregar thresholdValue si threshold_check está en true
        if (threshold_check) {
            newHost.threshold = Math.floor(threshold.value); // Toma siempre la parte entera
        }

        fetch('../php/API/ping_agent_edit_update_data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(newHost),
        })
            .then(response => response.json())
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                if (!data.error) {
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 2000);
                }
            })
            .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))//
            .finally(() => {
                toggleButtonState('updateAgentButton', false);
            });
    });
});