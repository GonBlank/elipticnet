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

        let isValid = true;

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

        
        // Seleccionar todos los checkboxes de tipo 'transport' en el formulario
        let transportCheckboxes = document.querySelectorAll("input[name$='_transport']"); // Selecciona todos los input con nombre que termine en '_transport'

        // Crear un objeto para almacenar los transportes seleccionados
        const transports = {};

        // Recorrer todos los checkboxes y almacenar si están seleccionados
        transportCheckboxes.forEach(checkbox => {
            transports[checkbox.name] = checkbox.checked; // Usamos el 'name' del checkbox como clave, el valor es si está marcado
        });

        // Crear un objeto con los datos del nuevo host
        const newHost = {
            name: hostName.value,
            ip: hostIp.value,
            description: description.value,
            transports: transports // El objeto con todos los transportes seleccionados
        };

        // Enviar los datos al backend usando fetch
        fetch('../php/create_ping_agent.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(newHost),
        })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    // Capturar el ID y el mensaje de respuesta
                    ShowAlert('success', 'Success', 'Host created successfully', 'success');

                    hostName.value = '';
                    hostIp.value = '';
                    description.value = '';

                    // Redireccionar a home.html
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 2000); //delay
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