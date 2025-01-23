
import { getUrlParameter } from '../functions/getUrlParameter.js';
import { logTimeFormatter } from '../functions/logTimeFormatter.js';
ping_agent_get_log();
setInterval(ping_agent_get_log, 60000);

function ping_agent_get_log() {
    const pingAgentId = getUrlParameter('id');
    fetch(`../php/API/ping_agent_get_log.php?id=${pingAgentId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then(response => {
            if (!response.ok) {
                ShowAlert('error', 'Error', `Response error: ${response.status}`, 'error');
                throw new Error(`[ERROR]: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {

            if (data.error) {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
            } else {
                console.log(data);
                create_log_table(data);
            }
        })
        .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'))
}


function create_log_table(log_array) {
    const table_body = document.getElementById('table_body');

    log_array.forEach((log, index) => {
        // Crear un ID único para cada log_row basado en la posición en el array
        const logId = `log_row_${index}`;

        // Verificar si el log_row con ese ID ya existe
        const existingLogRow = document.getElementById(logId);

        if (!existingLogRow) {
            // Si el log_row no existe, crear y agregar uno nuevo
            const log_row_element = create_log_row(log.icon, log.cause, log.message, log.time, logId);
            table_body.appendChild(log_row_element);
        }
    });
}

function create_log_row(icon, cause, message, time, logId) {
    const log_row = document.createElement('article');
    log_row.classList.add('log_row');
    log_row.id = logId; // Asignar un ID único al log_row

    log_row.innerHTML = `
        <div class="log_event">
            <img src="../img/svg/host/${icon}.svg">
            <p>${cause}</p>
        </div>
        <div class="cause">
            <p>${message}</p>
        </div>
        <div class="started">
            <p>${logTimeFormatter(time)}</p>
        </div>`;

    return log_row;
}
