function fetchHosts() {
    fetch('../php/API/get_hosts.php')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                ShowAlert(data.type, data.title, data.message, data.type);
                place_holder_manager('error');
            } else {
                place_holder_manager('delete');
                if (Array.isArray(data) && data.length === 0) {
                    const agents_menu_container = document.getElementById('agents_menu_container');
                    agents_menu_container.classList.add('full');
                }

                data.forEach(host => {
                    updateOrCreateHost(host);
                });

                updateOrCreateStatistics(data);
            }

        })
        .catch(error => {
            ShowAlert('error', 'Error', `Error: ${error.message}`, 'error')
            place_holder_manager('error');
        })//
        .finally(() => {
            //Quitar el icono de carga
        });
}


function updateOrCreateStatistics(data) {
    const hosts_monitored = document.getElementById('hosts_monitored');
    const hosts_up = document.getElementById('hosts_up');
    const hosts_down = document.getElementById('hosts_down');


    const hosts_monitored_h2Element = hosts_monitored.querySelector('h2');
    const hosts_up_h2Element = hosts_up.querySelector('h2');
    const hosts_down_h2Element = hosts_down.querySelector('h2');

    //contadores
    const hostCount = data.length;
    const upHosts = data.filter(host => host.state === 1).length;
    const downHosts = data.filter(host => host.state === 0).length;

    hosts_monitored_h2Element.textContent = hostCount;
    hosts_up_h2Element.textContent = upHosts;
    hosts_down_h2Element.textContent = downHosts;

}

function place_holder_manager(row_type) {
    const place_holder = document.getElementById('row_place_holder');

    switch (row_type) {
        case 'error':
            place_holder.innerHTML = `<img src="../img/icons/error.png" width="20" height="20">
            <p id="place_holder_message">ERROR</p>`;
            return;
        case 'delete':
            if (place_holder) {
                place_holder.remove();
            }
            return;
    }
}

function updateOrCreateHost(host) {
    const exist_element_host = document.querySelector(`a[host-id='${host.id}']`);
    const hostTable = document.getElementById('host_table');
    if (exist_element_host) {
        updateHost(exist_element_host, host);
    } else {
        const new_element_host = createHost(host);
        hostTable.appendChild(new_element_host);
    }
}

function createHost(host) {
    const agents_menu_container = document.getElementById('agents_menu_container');
    agents_menu_container.classList.remove('full'); //Acomoda el boton a un costado

    const host_row = document.createElement('a');
    host_row.classList.add('row');
    state = state_class(host.state);//waiting/up/down/warning
    host_row.classList.add(state.class);
    host_row.setAttribute('host-id', host.id);
    host_row.setAttribute('href', `ping_agent_view.php?id=${host.id}`);

    host_row.innerHTML = `
        <div class="host-satus">
            <div class="heartbeat-animation-container">
                <div class="heartbeat-animation-heartbeat"></div>
                    <div class="heartbeat-animation-core">
                        ${state.svg}
                    </div>
                </div>
            </div>
        <div class="host-data">
            <h1 id="host-name">${host.name}</h1>
            <p id="host-ip">${host.ip}</p>
        </div>
        <div class="host-extra-info">
            <p id="up-since" class="up-since">${up_since_formatter(host.state, host.last_up)}</p>

            </small>
        </div>
    `;

    return host_row;
}

function updateHost(element_host, host) {

    const hostName = element_host.querySelector('#host-name');
    const hostIp = element_host.querySelector('#host-ip');
    const upSince = element_host.querySelector('#up-since');
    const icon_state = element_host.querySelector('.heartbeat-animation-core');
    const state = state_class(host.state); // waiting/up/down/warning

    hostName.textContent = host.name;
    hostIp.textContent = host.ip;
    upSince.innerHTML = up_since_formatter(host.state, host.last_up);


    element_host.className = `row ${state.class}`;
    icon_state.innerHTML = state.svg;
}

function state_class(state) {
    switch (state) {
        case null:
            return {
                'class': 'waiting',
                'svg': `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                        </svg>`};
        case 1:
            return {
                'class': 'up',
                'svg': `<svg xmlns = "http://www.w3.org/2000/svg" width = "16" height = "16" fill = "currentColor" class="bi bi-check-circle-fill" viewBox = "0 0 16 16" >
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>`};
        case 0:
            return {
                'class': 'down',
                'svg': `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                        </svg>`};
        default:
            return {
                'class': 'waiting',
                'svg': `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                        </svg>`};
    }
}

function uptime_calculator(time) {
    // Convertir la cadena de texto 'time' en un objeto Date
    const inputDate = new Date(time + " UTC");  // Agregar " UTC" para asegurar que se interprete como UTC

    // Obtener la fecha y hora actual del cliente
    const currentDate = new Date();

    // Calcular la diferencia en milisegundos
    const uptimeMilliseconds = currentDate - inputDate;

    // Calcular la diferencia en días, horas, minutos y segundos
    const uptimeSeconds = Math.floor(uptimeMilliseconds / 1000);
    const uptimeMinutes = Math.floor(uptimeSeconds / 60);
    const uptimeHours = Math.floor(uptimeMinutes / 60);
    const uptimeDays = Math.floor(uptimeHours / 24);

    // Obtener el resto de horas, minutos y segundos
    const remainingHours = uptimeHours % 24;
    const remainingMinutes = uptimeMinutes % 60;
    const remainingSeconds = uptimeSeconds % 60;

    // Crear un objeto con el uptime calculado
    const uptime = {
        days: uptimeDays,
        hours: remainingHours,
        minutes: remainingMinutes,
        seconds: remainingSeconds
    };

    return uptime;
}

function uptime_formatter(uptime) {
    let uptimeString = 'UP';

    // Si hay días
    if (uptime.days > 0) {
        uptimeString += ` ${uptime.days}day${uptime.days > 1 ? 's' : ''}`;
    }

    // Si hay horas
    else if (uptime.hours > 0) {
        uptimeString += ` ${uptime.hours}hr${uptime.hours > 1 ? 's' : ''}`;
    }

    // Si hay minutos
    else if (uptime.minutes > 0) {
        uptimeString += ` ${uptime.minutes}min${uptime.minutes > 1 ? 's' : ''}`;
    }

    // Si hay segundos
    else if (uptime.seconds > 0) {
        uptimeString += ` ${uptime.seconds}sec${uptime.seconds > 1 ? 's' : ''}`;
    }

    // Si no hay tiempo transcurrido
    else {
        uptimeString = 'UP 0sec';
    }

    return uptimeString;
}

function up_since_formatter(state, last_up) {

    switch (state) {
        case null:
            return `
                    <svg class="spinner-circle" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                    <path d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z"></path>
                    </svg>
                    `;
        case 1:
            uptime = uptime_calculator(last_up);
            formatted_uptime = uptime_formatter(uptime);
            return uptime_formatter(uptime);
        case 0:
            return `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                    </svg> Host down
                    `;
        default:
            return `
                    <svg class="spinner-circle blue" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                    <path d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z"></path>
                    </svg>
                    `;
    }

}

// Llamada inicial a fetchHosts cuando la página carga
fetchHosts();

// Actualizar los hosts periódicamente
setInterval(fetchHosts, 5000);
