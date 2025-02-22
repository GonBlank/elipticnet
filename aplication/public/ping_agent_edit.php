<?php
require_once '../php/sesion/checkAuth.php';
$user = checkAuth();
define('MENU_ALLOWED', true);
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit agent</title>

    <!--Favicon-->
    <link rel="apple-touch-icon" sizes="180x180" href="../img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon/favicon-16x16.png">
    <link rel="manifest" href="../img/favicon/site.webmanifest">
    <link rel="mask-icon" href="../img/favicon/safari-pinned-tab.svg" color="#2b2d42">
    <link rel="shortcut icon" href="../img/favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#2b2d42">
    <meta name="msapplication-config" content="../img/favicon/browserconfig.xml">
    <meta name="theme-color" content="#2b2d42">


    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/palette.css" />
    <link rel="stylesheet" href="../css/components/alert.css" />
    <link rel="stylesheet" href="../css/components/tooltip.css" />
    <link rel="stylesheet" href="../css/ping_agent.css" />

</head>

<body>

    <nav>
        <button id="menu_button" onclick="open_menu()">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5f6368">
                <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z" />
            </svg>
        </button>
        <div class="nav-icon" id="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
            </svg>
        </div>
        <h1 id="nav-tittle"> Edit agent</h1>

    </nav>

    <?php include '../php/global/lateral_menu/lateral_menu.php'; ?>


    <main class="dashboard">

        <div id="load-curtain">
            <svg class="spinner-circle" class="up-since" xmlns="http://www.w3.org/2000/svg"
                height="24px" viewBox="0 -960 960 960" width="24px">
                <path
                    d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
            </svg>
        </div>

        <section class="form-container card">
            <div class="tittle">
                <h1>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001" />
                    </svg>
                    Edit agent
                </h1>

            </div>
            <div class="input-form">
                <div class="input-row-group">

                    <span style="width: 100%;" class="tooltip">
                        <label class="label">
                            <input tabindex="1" type="text" placeholder=" " class="input" id="alias" name="alias"
                                autocomplete="name" maxlength="25" />
                            <span class="label__name">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-quote-fill" viewBox="0 0 16 16">
                                    <path d="M16 8c0 3.866-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.584.296-1.925.864-4.181 1.234-.2.032-.352-.176-.273-.362.354-.836.674-1.95.77-2.966C.744 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7M7.194 6.766a1.7 1.7 0 0 0-.227-.272 1.5 1.5 0 0 0-.469-.324l-.008-.004A1.8 1.8 0 0 0 5.734 6C4.776 6 4 6.746 4 7.667c0 .92.776 1.666 1.734 1.666.343 0 .662-.095.931-.26-.137.389-.39.804-.81 1.22a.405.405 0 0 0 .011.59c.173.16.447.155.614-.01 1.334-1.329 1.37-2.758.941-3.706a2.5 2.5 0 0 0-.227-.4zM11 9.073c-.136.389-.39.804-.81 1.22a.405.405 0 0 0 .012.59c.172.16.446.155.613-.01 1.334-1.329 1.37-2.758.942-3.706a2.5 2.5 0 0 0-.228-.4 1.7 1.7 0 0 0-.227-.273 1.5 1.5 0 0 0-.469-.324l-.008-.004A1.8 1.8 0 0 0 10.07 6c-.957 0-1.734.746-1.734 1.667 0 .92.777 1.666 1.734 1.666.343 0 .662-.095.931-.26z" />
                                </svg>
                                Alias</span>
                            <div class="error-message" id="alias-error"></div>
                        </label>
                        <span class="tooltip-text"> <span style="font-weight: 600;">(Optional)</span> The alias is an alternative way to identify this agent.</span>
                    </span>

                    <label class="label">
                        <input tabindex="2" type="text" placeholder=" " class="input" id="host-ip" name="host-ip"
                            autocomplete="ip" disabled />
                        <span class="label__name">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                                fill="#e8eaed">
                                <path
                                    d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm-40-82v-78q-33 0-56.5-23.5T360-320v-40L168-552q-3 18-5.5 36t-2.5 36q0 121 79.5 212T440-162Zm276-102q20-22 36-47.5t26.5-53q10.5-27.5 16-56.5t5.5-59q0-98-54.5-179T600-776v16q0 33-23.5 56.5T520-680h-80v80q0 17-11.5 28.5T400-560h-80v80h240q17 0 28.5 11.5T600-440v120h40q26 0 47 15.5t29 40.5Z" />
                            </svg>
                            IP</span>
                        <div class="error-message" id="host-ip-error"></div>
                    </label>
                </div>

                <label class="label">
                    <textarea maxlength="200" tabindex="3" type="text" placeholder=" " class="input textarea"
                        id="host-description" name="host-description" autocomplete="description" required></textarea>
                    <span class="label__name textarea">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                            fill="#e8eaed">
                            <path
                                d="M280-400q17 0 28.5-11.5T320-440q0-17-11.5-28.5T280-480q-17 0-28.5 11.5T240-440q0 17 11.5 28.5T280-400Zm0-120q17 0 28.5-11.5T320-560q0-17-11.5-28.5T280-600q-17 0-28.5 11.5T240-560q0 17 11.5 28.5T280-520Zm0-120q17 0 28.5-11.5T320-680q0-17-11.5-28.5T280-720q-17 0-28.5 11.5T240-680q0 17 11.5 28.5T280-640Zm120 240h200v-80H400v80Zm0-120h320v-80H400v80Zm0-120h320v-80H400v80ZM80-80v-720q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H240L80-80Zm126-240h594v-480H160v525l46-45Zm-46 0v-480 480Z" />
                        </svg>
                        Description</span>
                    <div class="error-message" id="host-description-error"></div>
                </label>

            </div>

            <div class="divider">
                <hr>
                <p>Additional checks</p>
            </div>

            <div class="additional_checks">

                <span class="tooltip">
                    <div id="threshold-box" class="threshold init">
                        <div class="checkbox-wrapper-13">
                            <input id="threshold-checkbox" name="threshold" type="checkbox" onchange="toggleInput(this)">
                            <label for="threshold-checkbox">Threshold</label>
                        </div>

                        <label class="label" id="threshold-input-wrapper" style="display: none;">
                            <input tabindex="1" type="number" placeholder=" " class="input" id="threshold_value" name="threshold"
                                autocomplete="name" max="999" />
                            <span class="label__name">100ms</span>
                            <div class="error-message" id="threshold_value-error"></div>
                        </label>
                    </div>
                    <span class="tooltip-text">If the latency is higher than the threshold we will send a warning.<br>
                        - Value [ms]</span>
                </span>

                <script>
                    function toggleInput(checkbox) {
                        const inputWrapper = document.getElementById('threshold-input-wrapper');
                        const thresholdBox = document.getElementById('threshold-box');

                        if (checkbox.checked) {
                            thresholdBox.classList.remove('init');
                            thresholdBox.classList.add('expand');
                        } else {
                            thresholdBox.classList.remove('expand');
                            thresholdBox.classList.add('init');
                        }

                        inputWrapper.style.display = checkbox.checked ? '' : 'none';
                    }
                </script>
            </div>

            <div class="divider">
                <hr>
                <p>Alert transport</p>
            </div>

            <section id="alert_transports_table" class="alert-transports">

                <!-- <article class="transport">
                    <div class="checkbox-wrapper-13">
                        <input id="id_del_transporte" type="checkbox">
                        <label for="id_del_transporte">
                            <div class="checkbox-text">
                                <img src="../img/svg/email.svg">
                                <p>Alias</p>
                            </div>
                        </label>
                    </div>
                    <a href="transports.html" class="transition-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-wrench" viewBox="0 0 16 16">
                            <path
                                d="M.102 2.223A3.004 3.004 0 0 0 3.78 5.897l6.341 6.252A3.003 3.003 0 0 0 13 16a3 3 0 1 0-.851-5.878L5.897 3.781A3.004 3.004 0 0 0 2.223.1l2.141 2.142L4 4l-1.757.364zm13.37 9.019.528.026.287.445.445.287.026.529L15 13l-.242.471-.026.529-.445.287-.287.445-.529.026L13 15l-.471-.242-.529-.026-.287-.445-.445-.287-.026-.529L11 13l.242-.471.026-.529.445-.287.287-.445.529-.026L13 11z" />
                        </svg>
                        test.testing@gmail.com</a>
                </article> -->

                <div class="add-transport">
                    <a href="transports.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-send-plus-fill" viewBox="0 0 16 16">
                            <path
                                d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z" />
                            <path
                                d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-3.5-2a.5.5 0 0 0-.5.5v1h-1a.5.5 0 0 0 0 1h1v1a.5.5 0 0 0 1 0v-1h1a.5.5 0 0 0 0-1h-1v-1a.5.5 0 0 0-.5-.5" />
                        </svg>
                        ADD TRANSPORT</a>
                </div>
            </section>
            <button id="updateAgent" class="create-agent primary">

                <div class="text show">
                    UPDATE
                </div>

                <div class="loader-hourglass hide"> <!-- Cambiado a hide para que esté oculto por defecto -->
                    <svg class="spinner-hourglass" xmlns="http://www.w3.org/2000/svg" height="24px"
                        viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                        <path
                            d="M320-160h320v-120q0-66-47-113t-113-47q-66 0-113 47t-47 113v120Zm160-360q66 0 113-47t47-113v-120H320v120q0 66 47 113t113 47ZM160-80v-80h80v-120q0-61 28.5-114.5T348-480q-51-32-79.5-85.5T240-680v-120h-80v-80h640v80h-80v120q0 61-28.5 114.5T612-480q51 32 79.5 85.5T720-280v120h80v80H160Z" />
                    </svg>
                </div>


            </button>
        </section>
    </main>

    <script src="../js/modal.js"></script>
    <script src="../js/API/get_transports.js"></script>
    <script src="../js/functions/get_url_param_id.js"></script>
    <script src="../js/functions/remove_load_curtain.js"></script>
    <script src="../js/API/ping_agent_edit_get_data.js"></script>
    <script type="module" src="../js/API/ping_agent_edit_update_data.js"></script>
    <script src="../js/page_transition.js"></script>
    <script src="../js/components/alert.js"></script>
</body>

</html>