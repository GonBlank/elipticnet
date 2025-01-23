import { getUrlParameter } from '../functions/getUrlParameter.js';
import { toggleButtonState } from '../functions/toggleButtonState.js';

document.addEventListener("DOMContentLoaded", function () {
    const deleteDialog = document.getElementById("pingAgentDeleteDialog");
    const deleteBtn = document.getElementById("pingAgentDeleteBtn");
    const pingAgentId = getUrlParameter('id');

    deleteBtn.addEventListener('click', function (event) {

        toggleButtonState('pingAgentDeleteBtn' ,true);
        const pingAgentData = {
            id: pingAgentId
        };
        fetch('../php/API/ping_agent_delete.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(pingAgentData),
        })
            .then(response => {
                if (!response.ok) {
                    ShowAlert('error', 'Error', `Response error: ${response.status}`, 'error');
                    throw new Error(`[ERROR]: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                ShowAlert(data.type, data.title, data.message, data.type, data.link_text, data.link);
                if (!data.error) {
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 2000);
                }
            })
            .catch(error => ShowAlert('error', 'Error', `Fetch error: ${error.message || error}`, 'error'))
            .finally(() => {
                toggleButtonState('pingAgentDeleteBtn' ,false);
                deleteDialog.close();
            });

    });
});
