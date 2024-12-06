document.addEventListener("DOMContentLoaded", function () {
    const createAgentButton = document.getElementById('create_agent');

    // Animación de carga del formulario
    function toggleButtonState(isLoading) {
        const textDiv = createAgentButton.querySelector('.text');
        const loaderDiv = createAgentButton.querySelector('.loader-hourglass');

        if (isLoading) {
            textDiv.classList.remove('show');
            textDiv.classList.add('hide');
            loaderDiv.classList.remove('hide');
            loaderDiv.classList.add('show');
            createAgentButton.disabled = true;
        } else {
            textDiv.classList.remove('hide');
            textDiv.classList.add('show');
            loaderDiv.classList.remove('show');
            loaderDiv.classList.add('hide');
            createAgentButton.disabled = false;
        }
    }

    function validateHostData() {
        // Limpiar errores anteriores
        const inputs = document.querySelectorAll('.input');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorMessage = document.getElementById(`${input.id}-error`);
            if (errorMessage) errorMessage.textContent = '';
        });

        // Obtener los valores del formulario
        const hostName = document.getElementById('host-name').value;
        const hostIp = document.getElementById('host-ip').value;
        const threshold_check = document.getElementById('threshold-checkbox').checked;
        const threshold_value = document.getElementById('threshold_value').value;
        let isValid = true;

        if (threshold_check) {
            if (!threshold_value) {
                isValid = false;
                document.getElementById('threshold_value').classList.add('error');
                document.getElementById('threshold_value-error').textContent = 'Value required.';
            }
        }



        // Validar los datos del formulario
        if (!hostName) {
            isValid = false;
            document.getElementById('host-name').classList.add('error');
            document.getElementById('host-name-error').textContent = 'Host name is required.';
        }

        if (!hostIp) {
            isValid = false;
            document.getElementById('host-ip').classList.add('error');
            document.getElementById('host-ip-error').textContent = 'IP is required.';
        } else {
            // Validar formato de IP (IPv4 o IPv6)
            const ipv4Pattern = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            const ipv6Pattern = /^(?:[0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){1,7}:|(?:[0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|(?:[0-9a-fA-F]{1,4}:){1,5}(?::[0-9a-fA-F]{1,4}){1,2}|(?:[0-9a-fA-F]{1,4}:){1,4}(?::[0-9a-fA-F]{1,4}){1,3}|(?:[0-9a-fA-F]{1,4}:){1,3}(?::[0-9a-fA-F]{1,4}){1,4}|(?:[0-9a-fA-F]{1,4}:){1,2}(?::[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:(?::[0-9a-fA-F]{1,4}){1,6}|::(?:[0-9a-fA-F]{1,4}){1,7}|::(?:[0-9a-fA-F]{1,4}){1,6}:(?:[0-9a-fA-F]{1,4}){1,6}|(?:[0-9a-fA-F]{1,4}:){1,7}:|(?:[0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}$/;

            if (!ipv4Pattern.test(hostIp) && !ipv6Pattern.test(hostIp)) {
                isValid = false;
                document.getElementById('host-ip').classList.add('error');
                document.getElementById('host-ip-error').textContent = 'Invalid IP address format.';
            }
        }

        return isValid;
    }

    createAgentButton.addEventListener('click', function (event) {
        event.preventDefault(); // Previene el comportamiento por defecto del botón

        if (!validateHostData()) {
            return; // No enviar datos si la validación falla
        }

        // Cambiar el estado del botón para mostrar el loader
        toggleButtonState(true);

        // Obtener los valores del formulario
        const hostName = document.getElementById('host-name');
        const hostIp = document.getElementById('host-ip');
        const description = document.getElementById('host-description');
        const threshold_check = document.getElementById('threshold-checkbox').checked;
        const threshold = document.getElementById('threshold_value');

        // Seleccionar el contenedor de transportes
        const transportSection = document.querySelector('.alert-transports');

        // Seleccionar solo los checkboxes dentro de .alert-transports que estén marcados
        const selectedCheckboxes = transportSection.querySelectorAll('.checkbox-wrapper-13 input[type="checkbox"]:checked');

        // Obtener los IDs de los checkboxes seleccionados
        const TransportsSelected = Array.from(selectedCheckboxes).map(checkbox => checkbox.id);


        console.log(TransportsSelected)
        // Crear un objeto con los datos del nuevo host
        const newHost = {
            name: hostName.value,
            ip: hostIp.value,
            description: description.value,
            transports: TransportsSelected
        };

        // Agregar threshold_value si threshold_check está en true
        if (threshold_check) {
            newHost.threshold = Math.floor(threshold.value); // Toma siempre la parte entera
        }

        console.log(newHost);

        fetch('../php/API/create_ping_agent.php', {
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
                toggleButtonState(false);
            });
    });
});