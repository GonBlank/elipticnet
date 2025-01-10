function get_url_param_id() {
    const urlParams = new URLSearchParams(window.location.search);
    const hostId = urlParams.get('id');
    if (!hostId || hostId === null || !Number.isInteger(Number(hostId))) {
        window.location.replace('../public/home.html');
        return;
    }
    return (hostId);
}

hostId = get_url_param_id();