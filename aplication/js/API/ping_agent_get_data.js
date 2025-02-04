import { elapsedTime } from '../functions/elapsedTime.js';



function get_host_by_id() {

    fetch(`../php/API/ping_agent_get_data.php?id=${hostId}`)
        .then(response => {
            if (!response.ok) {
                ShowAlert('error', 'Error', `Response error: ${response.status}`, 'error');
                throw new Error(`[Response error]: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.error) {
                removeLoadCurtain();
                load_host_data(data);
                threshold_notification(data.threshold, data.threshold_exceeded);
                setThreshold(data.threshold); //Se usa para marcar el valor del threshold en el grafico de latencia
            } else {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                setTimeout(() => {
                    window.location.href = 'home.php';
                }, 2000);
            }
        })
        .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'));
}

function threshold_notification(threshold, threshold_exceeded) {
    const ping_agent_status = document.getElementById('ping_agent_status');
    //Failed to load host data: ReferenceError: warning_trheshold is not defined

    if (threshold_exceeded && !document.getElementById('warning_threshold')) {
        ping_agent_status.innerHTML += `<div id="warning_threshold" class="warning-trheshold card">
                    <div class="heartbeat-animation-container">
                        <div class="heartbeat-animation-heartbeat"></div>
                        <div class="heartbeat-animation-core">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                            </svg>
                        </div>
                    </div>
                    <p>Threshold exceeded - The latency is above ${threshold} ms</p>

                </div>`;
        return;
    }

    if (!threshold_exceeded && document.getElementById('warning_threshold')) {
        document.getElementById('warning_threshold').remove();
    }
}


function load_host_data(host) {
    const host_name = document.getElementById('host_name');
    const host_ip = document.getElementById('host_ip');
    const host_description = document.getElementById('host_description');
    const edit_host = document.getElementById('edit_host');

    const up_since = document.getElementById('up_since');
    const current_status = document.getElementById('current_status');
    const last_check = document.getElementById('last_check');

    host_ip.textContent = 'Ping agent for ' + host.ip;
    host_name.textContent = host.alias;
    host_description.textContent = host.description;

    // Construir la URL base con los parámetros id, ip, name y description
    let href = `ping_agent_edit.php?id=${hostId}`;
    edit_host.setAttribute('href', href);


    // Verificar y actualizar last_check
    if (host.state === null) {
        current_status.textContent = 'Waiting...';
        current_status.className = `waiting`;

        // Cambiar el display de #waiting-curtain a 'flex'
        const waitingCurtain = document.getElementById('waiting-curtain');
        waitingCurtain.style.display = 'flex';
    } else {
        // Verificar si el elemento #waiting-curtain existe antes de intentar ocultarlo y eliminarlo
        const waitingCurtain = document.getElementById('waiting-curtain');
        if (waitingCurtain) {
            //waitingCurtain.style.display = 'none';
            waitingCurtain.remove();  // Eliminarlo del DOM por completo
        }
    }

    if (host.state === 1) {
        current_status.textContent = 'UP';
        current_status.className = `up`;
        up_since.textContent = 'Uptime: ' + elapsedTime(host.last_up);

    }
    if (host.state === 0) {
        current_status.textContent = 'DOWN';
        current_status.className = `down`;
        up_since.textContent = '';
        up_since.textContent = 'Uptime: - ';
    }

    place_holder_manager('up_since_placeholder');
    place_holder_manager('last_check_placeholder');

    last_check.textContent = 'Last check: ' + elapsedTime(host.last_check) + ' ago';
}



function place_holder_manager(place_holder_id) {
    const place_holder = document.getElementById(place_holder_id);
    if (place_holder) {
        place_holder.remove();
    }
}

//globalVariable_threshold = null;

export let globalVariable_threshold = null;

export function setThreshold(newThreshold) {
    globalVariable_threshold = newThreshold;
}

get_host_by_id();
setInterval(get_host_by_id, 60000);