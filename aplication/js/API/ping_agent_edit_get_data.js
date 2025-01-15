import { getUrlParameter } from '../functions/getUrlParameter.js';
import { removeLoadCurtain } from '../functions/removeLoadCurtain.js';

function pingAgentEditGetData(id) {
    fetch(`../php/API/ping_agent_edit_get_data.php?id=${id}`)
        .then(response => {
            if (!response.ok) {
                ShowAlert('error', 'Error', `Failed to fetch data:  ${response.status}`, 'error');
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data.error) {
                removeLoadCurtain();
                load_ping_agent_data(data);
            }
            else {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                setTimeout(() => {
                    window.location.href = 'home.php';
                }, 2000);
            }
            removeLoadCurtain();
            loadPingAgentData(data);
        })
        .catch(error => {
            ShowAlert('error', 'Error', `Failed to fetch data:  ${error}`, 'error');
        });
}

function loadPingAgentData(pingAgent) {
    const hostName = document.getElementById('hostName');
    const hostIp = document.getElementById('hostIp');
    const hostDescription = document.getElementById('hostDescription');

    hostName.value = pingAgent.name;
    hostIp.value = pingAgent.ip;
    hostDescription.value = pingAgent.description;

    if (pingAgent.threshold != null) {
        const thresholdCheckbox = document.getElementById('thresholdCheckbox');
        const thresholdValue = document.getElementById('thresholdValue');
        const thresholdBox = document.getElementById('thresholdBox');
        const inputWrapper = document.getElementById('thresholdInputWrapper');
        thresholdCheckbox.checked = true;
        thresholdValue.value = pingAgent.threshold;
        thresholdBox.classList.add('expand');
        inputWrapper.style.display = '';
    }

    // Selecciona el contenedor donde se generan los transportes
    const container = document.getElementById('alertTransportsTable');
    const transports = JSON.parse(pingAgent.transports);

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

const id = getUrlParameter('id');

if (!id || id === null || !Number.isInteger(Number(id))) {
    window.location.replace('../public/home.php');
}

pingAgentEditGetData(id);

