function get_host_by_id() {

    fetch(`../php/API/get_host_data.php?id=${hostId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            load_host_data(data);
            create_log_table(data.log);
        })

        .catch(error => {
            ShowAlert('error', 'Error', `Failed to load host data: ${error}`, 'error');
        });
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
            <p>${log_time_formatter(time)}</p>
        </div>`;

    return log_row;
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
    host_name.textContent = host.name;
    host_description.textContent = host.description;

    // Construir la URL base con los parámetros id, ip, name y description
    let href = `host_edit.php?id=${hostId}`;
    edit_host.setAttribute('href', href);


    // Verificar y actualizar last_check
    if (host.state === null) {
        current_status.textContent = 'Waiting';
        current_status.className = `waiting`;
    }

    if (host.state === 1) {
        current_status.textContent = 'UP';
        current_status.className = `up`;
        up_since.textContent = 'Uptime: ' + uptime_formatter(uptime_calculator(host.last_up));

        place_holder_manager('up_since_placeholder');
        place_holder_manager('last_check_placeholder');

    }
    if (host.state === 0) {
        current_status.textContent = 'DOWN';
        current_status.className = `down`;
        up_since.textContent = '';
    }

    last_check.textContent = 'Last check ' + uptime_formatter(uptime_calculator(host.last_check)) + ' ago';

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
    let uptimeString = '';

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
        uptimeString = '0sec';
    }

    return uptimeString;
}

function place_holder_manager(place_holder_id) {
    const place_holder = document.getElementById(place_holder_id);
    if (place_holder) {
        place_holder.remove();
    }
}

function log_time_formatter(time) {
    const date = new Date(time + " UTC"); // Interpretar como UTC

    // Obtener los componentes individuales en la zona horaria del cliente
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Mes empieza en 0
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');

    // Formatear la fecha
    const formattedDate = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;

    return formattedDate;
}



let selectedRange = '24h';
get_host_by_id();
//setInterval(get_host_by_id, 60000); // Ejecuta cada 1 minuto
setInterval(get_host_by_id, 30000);