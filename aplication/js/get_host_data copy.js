
function get_host_by_id() {

    fetch(`../php/get_host_by_id.php?id=${hostId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log(data)
            document.querySelectorAll('.placeholder-load-container').forEach(element => {
                element.classList.add('hide');
            });

            document.querySelectorAll('.row-loader').forEach(element => {
                element.classList.add('hide');
            });

            if (selectedRange == '24h') {
                createChartDataLast24Hs(data.latency);
            } if (selectedRange == '1m') {
                createChartDataLastMonth(data.latency);
            }
            if (selectedRange == '1y') {
                createChartDataLastYear(data.latency);
            }

            load_data(data);
        })

        .catch(error => {
            //ShowAlert('error', 'Error', 'Failed to load host data', 'error');
            ShowAlert('error', 'Error', `Failed to load host data: ${error.message || error}`, 'error');

            document.querySelectorAll('.placeholder-load-container').forEach(element => {
                element.classList.remove('hide');
            });

            document.querySelectorAll('.row-loader').forEach(element => {
                element.classList.remove('hide');
            });

        });
}



function load_data(host) {
    const host_name = document.getElementById('host_name');
    const host_ip = document.getElementById('host_ip');
    const host_description = document.getElementById('host_description');
    const edit_host = document.getElementById('edit_host');
    const up_since = document.getElementById('up_since');
    const current_status = document.getElementById('current_status');
    const last_check = document.getElementById('last_check');
    const latency_average = document.getElementById('latency_average');
    const latency_minimum = document.getElementById('latency_minimum');
    const latency_maximum = document.getElementById('latency_maximum');

    host_ip.textContent = 'Ping agent for ' + host.ip;
    host_name.textContent = host.name;
    host_description.textContent = host.description;

    // Construir la URL base con los parámetros id, ip, name y description
    let href = `host_edit.php?id=${hostId}&ip=${host.ip}&name=${host.name}&description=${host.description}`;

    // Añadir cada transporte automáticamente
    for (const [key, value] of Object.entries(host.transports)) {
        href += `&${key}=${value}`;
    }

    // Asignar el enlace completo al atributo href
    edit_host.setAttribute('href', href);

    //edit_host.setAttribute('href', `host_edit.php?id=${hostId}&ip=${host.ip}&name=${host.name}&description=${host.description}&email_transport=${host.transports['email']}&telegram_transport=${host.transports['telegram']}`);

    statistics = calculateStatistics(host.latency, selectedRange);

    // Verificar y actualizar promedio (avg)
    if (statistics.avg === null || statistics.avg === 0) {
        //latency_average.innerHTML = document.getElementById('load_latency_average').outerHTML;
        latency_average.textContent = 'not calculated';
        latency_average.classList.add('waiting');
    } else {
        latency_average.classList.remove('waiting');
        latency_average.textContent = statistics.avg + ' ms';
    }

    // Verificar y actualizar mínimo (min)
    if (statistics.min === null) {
        latency_minimum.textContent = 'not calculated';
        latency_minimum.classList.add('waiting');
        //latency_minimum.innerHTML = document.getElementById('load_latency_minimum').outerHTML;
    } else {
        latency_minimum.classList.remove('waiting');
        latency_minimum.textContent = statistics.min + ' ms';
    }

    // Verificar y actualizar máximo (max)
    if (statistics.max === null) {
        latency_maximum.textContent = 'not calculated';
        latency_maximum.classList.add('waiting');
        //latency_maximum.innerHTML = document.getElementById('load_latency_maximum').outerHTML;
    } else {
        latency_maximum.classList.remove('waiting');
        latency_maximum.textContent = statistics.max + ' ms';
    }


    // Verificar y actualizar last_check
    if (host.last_check === null) {
        current_status.textContent = 'Waiting for first check';
        current_status.classList.add('waiting');
        current_status.classList.remove('up');
        current_status.classList.remove('down');
    } else {


        if (host.state) {
            current_status.textContent = 'UP';
            current_status.classList.add('up');
            current_status.classList.remove('down');
            current_status.classList.remove('waiting');

            up_since.textContent = 'Active for ' + getTimeDifference(host.last_up);

        } else {
            current_status.textContent = 'DOWN';
            current_status.classList.add('down');
            current_status.classList.remove('up');
            current_status.classList.remove('waiting');

            up_since.textContent = '';

        }

        last_check.textContent = 'Last check ' + getTimeDifference(host.last_check) + ' ago';


    }

    //log
    addRow(host.log);

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

function calculateStatistics(data, range) {
    const now = Date.now() / 1000; // Obtener el tiempo actual en segundos
    const oneDay = 24 * 60 * 60;   // Segundos en un día
    const oneMonth = 30 * oneDay;  // Aproximación de segundos en un mes (30 días)
    const oneYear = 365 * oneDay;  // Aproximación de segundos en un año
    let statistics = 0;
    // Función para calcular estadísticas (promedio, máximo, mínimo) dentro de un intervalo de tiempo
    function statsInInterval(interval) {
        // Filtrar valores que no sean null y estén dentro del intervalo de tiempo
        const filteredValues = data
            .filter(d => now - d.time <= interval && d.value !== null)
            .map(d => d.value);

        if (filteredValues.length === 0) return { avg: 0, max: null, min: null };

        const sum = filteredValues.reduce((acc, val) => acc + val, 0);
        const avg = (sum / filteredValues.length).toFixed(2);
        const max = Math.max(...filteredValues);
        const min = Math.min(...filteredValues);

        return { avg, max, min };
    }

    if (range === '24h') {

        statistics = statsInInterval(oneDay);

    } else if (range === '1m') {

        statistics = statsInInterval(oneMonth);

    } else if (range === '1y') {

        statistics = statsInInterval(oneYear);

    }

    return statistics;
}

function addRow(logData) {
    const logTable = document.getElementById('table_body');

    // Verificar si ya existe la fila con clase 'special'
    let specialRow = document.querySelector(".row.special");

    if (logData.length === 0 && !specialRow) {
        // Si logData está vacío y no existe la fila especial, crearla
        logTable.innerHTML += `
            <div class="row special">
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#e8eaed">
                        <path d="M360-120v-200q-62-5-121.5-14T120-360l20-80q83 23 168 31.5t172 8.5q86 0 171-8.5T820-440l20 80q-60 17-119.5 26T600-320v200H360Zm120-320q-34 0-57-23t-23-57q0-33 23-56.5t57-23.5q33 0 56.5 23.5T560-520q0 34-23.5 57T480-440ZM180-560q-26 0-43-17t-17-43q0-25 17-42.5t43-17.5q25 0 42.5 17.5T240-620q0 26-17.5 43T180-560Zm600 0q-26 0-43-17t-17-43q0-25 17-42.5t43-17.5q25 0 42.5 17.5T840-620q0 26-17.5 43T780-560ZM290-710q-26 0-43-17t-17-43q0-25 17-42.5t43-17.5q25 0 42.5 17.5T350-770q0 26-17.5 43T290-710Zm380 0q-26 0-43-17t-17-43q0-25 17-42.5t43-17.5q25 0 42.5 17.5T730-770q0 26-17.5 43T670-710Zm-190-50q-26 0-43-17t-17-43q0-25 17-42.5t43-17.5q25 0 42.5 17.5T540-820q0 26-17.5 43T480-760Z"/>
                    </svg>
                    No incidents
                </p>
            </div>`;
    } else if (logData.length > 0 && specialRow) {
        // Si logData tiene elementos y la fila especial existe, eliminarla
        specialRow.remove();
    }





    logData.forEach(log => {
        const rowId = `row-${log.start_time}`; // Usa start_time como identificador único

        let existingRow = document.getElementById(rowId);

        // Calcular la duración
        let durationString = '?';
        if (log.end_time != null) {
            const duration = log.end_time - log.start_time;
            durationString = formatDuration(duration);
        }

        // Formatear estado y clases
        let statusClass = log.state ? 'resolved' : 'unsolved';
        let statusText = log.state ? 'Solved' : 'Unsolved';

        // SVG según el estado
        let statusIcon = log.state ? `
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>` : `
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
            </svg>`;

        if (!existingRow) {
            // Si no existe, crear la nueva fila
            const row = document.createElement('div');
            row.classList.add('row');
            row.id = rowId;

            row.innerHTML = `
                <p class="${statusClass}">
                    ${statusIcon} ${statusText}
                </p>
                <p>${log.cause}</p>
                <p>${formatDate(log.start_time)}</p>
                <p>${durationString}</p>
            `;

            logTable.appendChild(row);
        } else {
            // Si ya existe, actualizar el contenido
            existingRow.querySelector('p').className = statusClass;
            existingRow.querySelector('p').innerHTML = `${statusIcon} ${statusText}`;
            existingRow.querySelectorAll('p')[1].textContent = log.cause;
            existingRow.querySelectorAll('p')[2].textContent = formatDate(log.start_time);
            existingRow.querySelectorAll('p')[3].textContent = durationString;
        }
    });
}

function formatDate(timestamp) {
    const date = new Date(timestamp * 1000); // Convertir de segundos a milisegundos

    // Formato: Sep 26, 2024 09:26AM con la zona horaria del navegador
    const options = {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true, // Para mostrar AM/PM
    };

    // Obtener la fecha formateada sin forzar una zona horaria
    return date.toLocaleString('en-US', options);
}

// Función para calcular la duración entre start_time y end_time
function calculateDuration(start, end) {
    const duration = end - start;
    const hours = Math.floor(duration / 3600);
    const minutes = Math.floor((duration % 3600) / 60);
    const seconds = duration % 60;

    return `${hours}h ${minutes}m ${seconds}s`;
}

function formatDuration(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const remainingSeconds = seconds % 60;

    let durationParts = [];
    if (hours > 0) durationParts.push(`${hours}h`);
    if (minutes > 0) durationParts.push(`${minutes}m`);
    if (remainingSeconds > 0 || durationParts.length === 0) durationParts.push(`${remainingSeconds}s`);

    return durationParts.join(' ');
}


// Función para actualizar el valor de selectedRange cuando se selecciona un radio
function updateSelectedRange(range) {
    selectedRange = range;
    //createChartData(data.latency);
    get_host_by_id();
}

let selectedRange = '24h';
get_host_by_id();
setInterval(get_host_by_id, 60000); // Ejecuta cada 1 minuto