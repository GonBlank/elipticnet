function get_host_statistics() {
    if (!hostId || isNaN(hostId)) {
        ShowAlert('error', 'Error', 'Invalid host ID', 'error');
        return;
    }

    fetch(`../php/API/get_host_statistics.php?id=${hostId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            if (data.error) {
                ShowAlert(data.type, data.title, data.message, data.type);
            }else{
                create_get_host_statistics(data)
            }
        })
        .catch(error => {
            console.error('Error fetching host statistics:', error);
            ShowAlert('error', 'Error', `Failed to load host data: ${error.message || error}`, 'error');
        });
}

function create_get_host_statistics(data){
    const latency_average = document.getElementById('latency_average');
    const latency_minimum = document.getElementById('latency_minimum');
    const latency_maximum = document.getElementById('latency_maximum');

    if (data.average_latency != null && data.minimum_latency != null && data.maximum_latency != null) {
        latency_average.textContent = parseFloat(data.average_latency.toFixed(2)) + 'ms';
        latency_minimum.textContent = data.minimum_latency + 'ms';
        latency_maximum.textContent = data.maximum_latency + 'ms';
    }
    


}

get_host_statistics()
