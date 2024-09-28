function fetchHosts() {
    fetch('../php/get_hosts.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                ShowAlert(data.type, data.title, data.message, data.type);
            } else {

                updateOrCreateStatistics(data);

                data.forEach(host => {
                    updateOrCreateHost(host);
                });
            }
        })
        .catch(error => ShowAlert('error', 'Error', 'Error getting hosts', 'error'))
        .finally(() => {
            // quita el icono de carga
            const loaderDiv = document.getElementById('table-loader');
            loaderDiv.classList.add('hide');
            loaderDiv.classList.remove('show');
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
    const upHosts = data.filter(host => host.state === true).length;
    const downHosts = data.filter(host => host.state === false).length;

    hosts_monitored_h2Element.textContent = hostCount;
    hosts_up_h2Element.textContent = upHosts;
    hosts_down_h2Element.textContent = downHosts;

}

function updateOrCreateHost(host) {
    const existingRow = document.querySelector(`a[data-id='${host.id}']`);

    if (existingRow) {
        updateHost(existingRow, host);
    } else {
        createHost(host);
    }
}

function updateHost(row, host) {
    const hostName = row.querySelector('#host-name');
    const hostIp = row.querySelector('#host-ip');
    const upSince = row.querySelector('#up-since');
    const lastCheck = row.querySelector('#last-check');

    hostName.textContent = host.name;
    hostIp.textContent = host.ip;
    //upSince.textContent = `Up since ${host.last_up}`;

    // Verificar si host.last_check tiene valor
    if (host.last_check && host.state) {
        const timeDiffText = getTimeDifference(host.last_check);
        lastCheck.textContent = `Last check: ${timeDiffText}`;
    } else if (!host.last_check && host.state) {
        lastCheck.textContent = '';
    }

    if (host.last_up) {
        upSince.textContent = 'Up ' + getTimeDifference(host.last_up);

    } else if (!host.last_up && host.state) {
        upSince.innerHTML = `
        <svg class="spinner-circle yellow" class="up-since" xmlns="http://www.w3.org/2000/svg" height="24px"
            viewBox="0 -960 960 960" width="24px" >
            <path
                d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
        </svg>
        Waiting...
    `;
    }

    const heartbeat = row.querySelector('.heartbeat-animation-heartbeat');
    const heartbeatCore = row.querySelector('.heartbeat-animation-core');

    if (!host.state) {
        heartbeat.classList.add('down');
        heartbeatCore.classList.add('down');
        upSince.innerHTML = `<svg class="red" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#f83b3b"><path d="M480-280q17 0 28.5-11.5T520-320q0-17-11.5-28.5T480-360q-17 0-28.5 11.5T440-320q0 17 11.5 28.5T480-280Zm-40-160h80v-240h-80v240Zm40 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
        Host down`;
        lastCheck.textContent = '';
    } else {
        heartbeat.classList.remove('down');
        heartbeatCore.classList.remove('down');
    }
}

function createHost(host) {
    const row = document.createElement('a');
    row.setAttribute('href', `host.html?id=${host.id}`);
    row.setAttribute('class', 'row');
    row.setAttribute('data-id', host.id);

    const hostStatus = document.createElement('div');
    hostStatus.setAttribute('class', 'host-satus');

    const heartbeatContainer = document.createElement('div');
    heartbeatContainer.setAttribute('class', 'heartbeat-animation-container');

    const heartbeat = document.createElement('div');
    heartbeat.setAttribute('class', 'heartbeat-animation-heartbeat');

    const heartbeatCore = document.createElement('div');
    heartbeatCore.setAttribute('class', 'heartbeat-animation-core');
    heartbeatCore.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
             class="bi bi-router-fill" viewBox="0 0 16 16">
            <path d="M5.525 3.025a3.5 3.5 0 0 1 4.95 0 .5.5 0 1 0 .707-.707 4.5 4.5 0 0 0-6.364 0 .5.5 0 0 0 .707.707" />
            <path d="M6.94 4.44a1.5 1.5 0 0 1 2.12 0 .5.5 0 0 0 .708-.708 2.5 2.5 0 0 0-3.536 0 .5.5 0 0 0 .707.707Z" />
            <path d="M2.974 2.342a.5.5 0 1 0-.948.316L3.806 8H1.5A1.5 1.5 0 0 0 0 9.5v2A1.5 1.5 0 0 0 1.5 13H2a.5.5 0 0 0 .5.5h2A.5.5 0 0 0 5 13h6a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5h.5a1.5 1.5 0 0 0 1.5-1.5v-2A1.5 1.5 0 0 0 14.5 8h-2.306l1.78-5.342a.5.5 0 1 0-.948-.316L11.14 8H4.86zM2.5 11a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m4.5-.5a.5.5 0 1 1 1 0 .5.5 0 0 1-1 0m2.5.5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m1.5-.5a.5.5 0 1 1 1 0 .5.5 0 0 1-1 0m2 0a.5.5 0 1 1 1 0 .5.5 0 0 1-1 0" />
            <path d="M8.5 5.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
        </svg>
    `;

    if (!host.state) {
        heartbeat.classList.add('down');
        heartbeatCore.classList.add('down');
    } else {
        heartbeat.classList.remove('down');
        heartbeatCore.classList.remove('down');
    }

    heartbeatContainer.appendChild(heartbeat);
    heartbeatContainer.appendChild(heartbeatCore);
    hostStatus.appendChild(heartbeatContainer);

    const hostData = document.createElement('div');
    hostData.setAttribute('class', 'host-data');
    const hostName = document.createElement('h1');
    hostName.setAttribute('id', 'host-name');
    hostName.textContent = host.name;
    const hostIp = document.createElement('p');
    hostIp.setAttribute('id', 'host-ip');
    hostIp.textContent = host.ip;
    hostData.appendChild(hostName);
    hostData.appendChild(hostIp);

    const hostExtraInfo = document.createElement('div');
    hostExtraInfo.setAttribute('class', 'host-extra-info');
    const upSince = document.createElement('p');
    upSince.setAttribute('id', 'up-since');
    upSince.classList.add('up-since');

    const lastCheck = document.createElement('small');
    lastCheck.setAttribute('id', 'last-check');

    if (!host.state) {
        upSince.innerHTML = `<svg class="red" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#f83b3b"><path d="M480-280q17 0 28.5-11.5T520-320q0-17-11.5-28.5T480-360q-17 0-28.5 11.5T440-320q0 17 11.5 28.5T480-280Zm-40-160h80v-240h-80v240Zm40 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                            Host down`;
        lastCheck.textContent = '';
    }


    if (host.last_check && host.state) {
        const timeDiffText = getTimeDifference(host.last_check);
        lastCheck.textContent = `Last check: ${timeDiffText}`;
    } else if (!host.last_check && host.state) {
        lastCheck.textContent = '';
    }

    if (host.last_up && host.state) {
        upSince.textContent = 'Up ' + getTimeDifference(host.last_up);
    } else if (!host.last_up && host.state) {

        upSince.innerHTML = `
        <svg class="spinner-circle yellow" xmlns="http://www.w3.org/2000/svg" height="24px"
            viewBox="0 -960 960 960" width="24px">
            <path
                d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
        </svg>
        Waiting...
    `;
    }

    hostExtraInfo.appendChild(upSince);
    hostExtraInfo.appendChild(lastCheck);

    row.appendChild(hostStatus);
    row.appendChild(hostData);
    row.appendChild(hostExtraInfo);

    const hostTable = document.getElementById('host-table');
    hostTable.appendChild(row);
}

function getTimeDifference(lastCheck) {
    const now = Date.now() / 1000;
    let diff = Math.floor(now - lastCheck);

    const days = Math.floor(diff / (24 * 3600));
    diff -= days * 24 * 3600;
    const hours = Math.floor(diff / 3600);
    diff -= hours * 3600;
    const minutes = Math.floor(diff / 60);
    const seconds = diff % 60;

    const parts = [];
    if (days > 0) parts.push(`${days}d`);
    if (hours > 0) parts.push(`${hours}h`);
    if (minutes > 0) parts.push(`${minutes}m`);
    if (seconds > 0 || parts.length === 0) parts.push(`${seconds}s`);

    return parts.join(' ');
}

// Llamada inicial a fetchHosts cuando la página carga
fetchHosts();

// Actualizar los hosts periódicamente
setInterval(fetchHosts, 5000);
