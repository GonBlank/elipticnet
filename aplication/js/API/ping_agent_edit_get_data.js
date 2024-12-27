function ping_agent_edit_get_data() {

    fetch(`../php/API/ping_agent_edit_get_data.php?id=${hostId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            load_ping_agent_data(data);
        })
        .catch(error => {
            ShowAlert('error', 'Error', `Failed to load host data: ${error}`, 'error');
        });
}

ping_agent_edit_get_data();

function load_ping_agent_data(ping_agent) {
    const host_name = document.getElementById('host-name');
    const host_ip = document.getElementById('host-ip');
    const host_description = document.getElementById('host-description');

    host_name.value = ping_agent.name;
    host_ip.value = ping_agent.ip;
    host_description.value = ping_agent.description;

    if (ping_agent.threshold != null) {
        const threshold_checkbox = document.getElementById('threshold-checkbox');
        const threshold_value = document.getElementById('threshold_value');
        const threshold_box = document.getElementById('threshold-box');
        const inputWrapper = document.getElementById('threshold-input-wrapper');
        threshold_checkbox.checked = true;
        threshold_value.value = ping_agent.threshold;
        threshold_box.classList.add('expand');
        inputWrapper.style.display = '';
    }

    // Selecciona el contenedor donde se generan los transportes
    const container = document.getElementById('alert_transports_table');
    const transports = JSON.parse(ping_agent.transports);

    // Configura el MutationObserver
    const observer = new MutationObserver(() => {
        transports.forEach(transportId => {
            const checkbox = document.getElementById(transportId); // Busca el checkbox por ID
            if (checkbox) {
                checkbox.checked = true; // Marca el checkbox si existe
            }
        });
    });

    // Inicia la observación de cambios en el contenedor
    observer.observe(container, { childList: true, subtree: true });


}