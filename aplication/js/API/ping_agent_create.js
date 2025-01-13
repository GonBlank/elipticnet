import { toggleButtonState } from '../functions/toggleButtonState.js';

document.addEventListener("DOMContentLoaded", function () {
    const createAgentBtn = document.getElementById('createAgentBtn');

    function validateHostData() {
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}-error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        const hostName = document.getElementById('hostName').value;
        const hostIp = document.getElementById('hostIp').value;
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

        if (!hostName) {
            isValid = false;
            document.getElementById('hostName').classList.add('error');
            document.getElementById('hostNameError').textContent = 'Host name is required.';
        }

        if (!hostIp) {
            isValid = false;
            document.getElementById('hostIp').classList.add('error');
            document.getElementById('hostIpError').textContent = 'IP is required.';
        } else {
            // Validar formato de IP (IPv4 o IPv6)
            const ipv4Pattern = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            const ipv6Pattern = /^(?:[0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){1,7}:|(?:[0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){1,5}(?::[0-9a-fA-F]{1,4}){1,2}|(?:[0-9a-fA-F]{1,4}:){1,4}(?::[0-9a-fA-F]{1,4}){1,3}|(?:[0-9a-fA-F]{1,4}:){1,3}(?::[0-9a-fA-F]{1,4}){1,4}|(?:[0-9a-fA-F]{1,4}:){1,2}(?::[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:(?::[0-9a-fA-F]{1,4}){1,6}|::(?:[0-9a-fA-F]{1,4}){1,7}|::(?:[0-9a-fA-F]{1,4}){1,6}:(?:[0-9a-fA-F]{1,4}){1,6}|(?:[0-9a-fA-F]{1,4}:){1,7}:|(?:[0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}$/;

            if (!ipv4Pattern.test(hostIp) && !ipv6Pattern.test(hostIp)) {
                isValid = false;
                document.getElementById('hostIp').classList.add('error');
                document.getElementById('hostIpError').textContent = 'Invalid IP address format.';
            }
        }
        return isValid;
    }

    createAgentBtn.addEventListener('click', function (event) {
        event.preventDefault();

        if (!validateHostData()) {
            return;
        }

        toggleButtonState('createAgentBtn', true);

        const hostName = document.getElementById('hostName');
        const hostIp = document.getElementById('hostIp');
        const description = document.getElementById('host-description');
        const threshold_check = document.getElementById('thresholdCheckbox').checked;
        const threshold = document.getElementById('thresholdValue');

        // Seleccionar el contenedor de transportes
        const transportSection = document.querySelector('.alert-transports');

        // Seleccionar solo los checkboxes dentro de .alert-transports que estén marcados
        const selectedCheckboxes = transportSection.querySelectorAll('.checkbox-wrapper-13 input[type="checkbox"]:checked');

        // Obtener los IDs de los checkboxes seleccionados
        const TransportsSelected = Array.from(selectedCheckboxes).map(checkbox => checkbox.id);

        const newHost = {
            name: hostName.value,
            ip: hostIp.value,
            description: description.value,
            transports: TransportsSelected
        };

        // Agregar thresholdValue si threshold_check está en true
        if (threshold_check) {
            newHost.threshold = Math.floor(threshold.value); // Toma siempre la parte entera
        }

        fetch('../php/API/ping_agent_create.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(newHost),
        })
            .then(response => response.json())
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type);
                if (!data.error) {
                    hostName.value = '';
                    hostIp.value = '';
                    description.value = '';
                    threshold.value = '';
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 2000);
                }
            })
            .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))//
            .finally(() => {
                toggleButtonState('createAgentBtn', false);
            });
    });
});