export function toggleButtonState(buttonId, isLoading) {
    const button = document.getElementById(buttonId);
    if (!button) {
        ShowAlert('warning', 'Warning', `Button not found: ${buttonId}`, 'warning');
        return;
    }
    const textDiv = button.querySelector('.text');
    const loaderDiv = button.querySelector('.loader-hourglass');

    if (isLoading) {
        textDiv.classList.remove('show');
        textDiv.classList.add('hide');
        loaderDiv.classList.remove('hide');
        loaderDiv.classList.add('show');
        button.disabled = true;
    } else {
        textDiv.classList.remove('hide');
        textDiv.classList.add('show');
        loaderDiv.classList.remove('show');
        loaderDiv.classList.add('hide');
        button.disabled = false;
    }
}
