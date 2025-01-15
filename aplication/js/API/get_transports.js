function fetchTransports() {
    fetch('../php/API/get_transport_by_owner.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                ShowAlert('error', 'Error', `Error: ${response.status}`, 'error');
            }
            return response.json();
        })
        .then(data => {
            if (data.error){
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                return null;
            }
            // Asegurarse de que los datos contienen transportes
            if (data.transports && data.transports.length > 0) {
                const transportContainer = document.querySelector('.alert-transports');

                data.transports.forEach(transport => {
                    const transportElement = createTransport(transport);
                    transportContainer.appendChild(transportElement);
                });
            } else {
                ShowAlert('warning', 'Not found', 'No transports found', 'warning');
            }
        })
        .catch(error => ShowAlert('error', 'Error', `Error: ${error.message}`, 'error'))//
}

// Llamar la función al cargar la página
window.onload = fetchTransports;

function createTransport(data) {
    const transport = document.createElement('article');
    transport.classList.add('transport');
    transport.innerHTML = `
        <div class="checkbox-wrapper-13">
            <input id="${data.id}" type="checkbox">
            <label for="${data.id}">
                <div class="checkbox-text">
                    <img src="../img/svg/${data.type}.svg" alt="${data.type}">
                    <p>${data.alias}</p>
                </div>
            </label>
        </div>
        <a href="transports.html" class="transition-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-wrench" viewBox="0 0 16 16">
                <path d="M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364zm13.37 9.019.528.026.287.445.445.287.026.529L15 13l-.242.471-.026.529-.445.287-.287.445-.529.026L13 15l-.471-.242-.529-.026-.287-.445-.445-.287-.026-.529L11 13l.242-.471.026-.529.445-.287.287-.445.529-.026L13 11z" />
            </svg>
            ${data.transport_id}
        </a>
    `;
    return transport; // Devuelve el elemento creado
}
