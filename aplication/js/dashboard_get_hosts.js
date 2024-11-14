function fetchHosts() {
    fetch('../php/get_hosts.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                ShowAlert(data.type, data.title, data.message, data.type);

                document.getElementById('host-table').innerHTML = `
                <a id="special-row-error" class="row special-row ">
                <p>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ff6a6a" class="bi bi-exclamation-diamond-fill" viewBox="0 0 16 16">
                <path d="M9.05.435c-.58-.58-1.52-.58-2.1 0L.436 6.95c-.58.58-.58 1.519 0 2.098l6.516 6.516c.58.58 1.519.58 2.098 0l6.516-6.516c.58-.58.58-1.519 0-2.098zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                </svg>
                We are experiencing problems</p>
                </a>
                `;
            } else {

                if (Array.isArray(data) && data.length === 0) {
                    // No hay hosts creados
                    document.getElementById('host-table').innerHTML = `
                        <a id="special-row-no-host" class="row special-row">
                        <p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-arms-up" viewBox="0 0 16 16">
                            <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                            <path d="m5.93 6.704-.846 8.451a.768.768 0 0 0 1.523.203l.81-4.865a.59.59 0 0 1 1.165 0l.81 4.865a.768.768 0 0 0 1.523-.203l-.845-8.451A1.5 1.5 0 0 1 10.5 5.5L13 2.284a.796.796 0 0 0-1.239-.998L9.634 3.84a.7.7 0 0 1-.33.235c-.23.074-.665.176-1.304.176-.64 0-1.074-.102-1.305-.176a.7.7 0 0 1-.329-.235L4.239 1.286a.796.796 0 0 0-1.24.998l2.5 3.216c.317.316.475.758.43 1.204Z"/>
                            </svg>
                            No agents created
                        </p>
                        </a>
                    `;
                } else {
                    deleteSpecialRow('no-host');
                }

                deleteSpecialRow('error');
                updateOrCreateStatistics(data);

                data.forEach(host => {
                    updateOrCreateHost(host);
                });
            }
        })
        .catch(() => {

            document.getElementById('host-table').innerHTML = `
            <a id="special-row" class="row special-row ">
            <p>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ff6a6a" class="bi bi-exclamation-diamond-fill" viewBox="0 0 16 16">
            <path d="M9.05.435c-.58-.58-1.52-.58-2.1 0L.436 6.95c-.58.58-.58 1.519 0 2.098l6.516 6.516c.58.58 1.519.58 2.098 0l6.516-6.516c.58-.58.58-1.519 0-2.098zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
            </svg>
            We are experiencing problems</p>
            </a>
            `;
        }
        )
        .finally(() => {
            // quita el icono de carga
            const loaderDiv = document.getElementById('table-loader');
            loaderDiv.classList.add('hide');
            loaderDiv.classList.remove('show');
        });
}

function deleteSpecialRow(type) {
    const specialRow = document.getElementById('special-row-' + type);
    if (specialRow) {
        specialRow.remove();
    }
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

    hostName.textContent = host.name;
    hostIp.textContent = host.ip;

    if (host.last_up) {
        upSince.textContent = 'Up ' + getTimeDifference(host.last_up);

    } else if (!host.last_up && host.state) {
        upSince.textContent = `Awaiting first check`;
    }

    const heartbeat = row.querySelector('.heartbeat-animation-heartbeat');
    const heartbeatCore = row.querySelector('.heartbeat-animation-core');

    if (!host.last_up && host.state) {
        heartbeat.classList.add('waiting');
        heartbeatCore.classList.add('waiting');

    } else if (!host.state) {
        heartbeat.classList.add('down');
        heartbeatCore.classList.add('down');

        heartbeat.classList.remove('waiting');
        heartbeatCore.classList.remove('waiting');

        upSince.innerHTML = `<svg class="red" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#f83b3b"><path d="M480-280q17 0 28.5-11.5T520-320q0-17-11.5-28.5T480-360q-17 0-28.5 11.5T440-320q0 17 11.5 28.5T480-280Zm-40-160h80v-240h-80v240Zm40 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
        Host down`;
    } else {
        heartbeat.classList.remove('down');
        heartbeatCore.classList.remove('down');

        heartbeat.classList.remove('waiting');
        heartbeatCore.classList.remove('waiting');
    }
}

function createHost(host) {
    const row = document.createElement('a');
    row.setAttribute('href', `host.php?id=${host.id}`);
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
    if (!host.last_up && host.state) {
        heartbeat.classList.add('waiting');
        heartbeatCore.classList.add('waiting');
    } else if (!host.state) {
        heartbeat.classList.add('down');
        heartbeatCore.classList.add('down');
    } else {
        heartbeat.classList.remove('down');
        heartbeatCore.classList.remove('down');

        heartbeat.classList.remove('waiting');
        heartbeatCore.classList.remove('waiting');
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

    if (!host.state) {
        upSince.innerHTML = `<svg class="red" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#f83b3b"><path d="M480-280q17 0 28.5-11.5T520-320q0-17-11.5-28.5T480-360q-17 0-28.5 11.5T440-320q0 17 11.5 28.5T480-280Zm-40-160h80v-240h-80v240Zm40 360q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg>
                            Host down`;
    }


    if (host.last_up && host.state) {
        upSince.textContent = 'Up ' + getTimeDifference(host.last_up);
    } else if (!host.last_up && host.state) {
        upSince.textContent = `Awaiting first check`;
    }

    hostExtraInfo.appendChild(upSince);
    row.appendChild(hostStatus);
    row.appendChild(hostData);
    row.appendChild(hostExtraInfo);

    const hostTable = document.getElementById('host-table');
    hostTable.appendChild(row);
}

function getTimeDifference(time) {
    const now = Date.now() / 1000;
    let diff = Math.floor(now - time);

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
