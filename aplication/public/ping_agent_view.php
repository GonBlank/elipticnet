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
    <title>Elipticnet || Ping agent view</title>

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
    <link rel="stylesheet" href="../css/components/link_drop_down.css" />
    <link rel="stylesheet" href="../css/ping_agent_view.css" />

</head>

<body>

    <nav>
        <button id="menu_button" onclick="open_menu()">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5f6368">
                <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z" />
            </svg>
        </button>
        <div class="nav-icon" id="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
            </svg>
        </div>
        <h1 id="nav-tittle">Ping agent view</h1>

    </nav>

    <?php include '../php/global/lateral_menu/lateral_menu.php'; ?>


    <main class="dashboard">

        <div id="waiting-curtain">
            <h1>Waiting for the first check.</h1>
            <p>( it won't be long )</p>
        </div>

        <div id="load-curtain">
            <svg class="spinner-circle" class="up-since" xmlns="http://www.w3.org/2000/svg"
                height="24px" viewBox="0 -960 960 960" width="24px">
                <path
                    d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
            </svg>
        </div>

        <section class="container">
            <article class="presentation">
                <div class="title">
                    <h1 id="host_name">
                        <div class="placeholder-load-container" style="width: 150px; height: 1.5rem;">
                        </div>
                    </h1>
                    <h2 id="host_ip">
                        <div class="placeholder-load-container" style="width: 110px; height: .9rem;">
                        </div>
                    </h2>
                    <h2 id="host_description">
                        <div class="placeholder-load-container" style="width: 110px; height: .9rem;">
                        </div>
                    </h2>
                </div>
                <div class="dropdown_menu_container">
                    <button id="dropdown_button" class="host-options">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-wide" viewBox="0 0 16 16">
                            <path d="M8.932.727c-.243-.97-1.62-.97-1.864 0l-.071.286a.96.96 0 0 1-1.622.434l-.205-.211c-.695-.719-1.888-.03-1.613.931l.08.284a.96.96 0 0 1-1.186 1.187l-.284-.081c-.96-.275-1.65.918-.931 1.613l.211.205a.96.96 0 0 1-.434 1.622l-.286.071c-.97.243-.97 1.62 0 1.864l.286.071a.96.96 0 0 1 .434 1.622l-.211.205c-.719.695-.03 1.888.931 1.613l.284-.08a.96.96 0 0 1 1.187 1.187l-.081.283c-.275.96.918 1.65 1.613.931l.205-.211a.96.96 0 0 1 1.622.434l.071.286c.243.97 1.62.97 1.864 0l.071-.286a.96.96 0 0 1 1.622-.434l.205.211c.695.719 1.888.03 1.613-.931l-.08-.284a.96.96 0 0 1 1.187-1.187l.283.081c.96.275 1.65-.918.931-1.613l-.211-.205a.96.96 0 0 1 .434-1.622l.286-.071c.97-.243.97-1.62 0-1.864l-.286-.071a.96.96 0 0 1-.434-1.622l.211-.205c.719-.695.03-1.888-.931-1.613l-.284.08a.96.96 0 0 1-1.187-1.186l.081-.284c.275-.96-.918-1.65-1.613-.931l-.205.211a.96.96 0 0 1-1.622-.434zM8 12.997a4.998 4.998 0 1 1 0-9.995 4.998 4.998 0 0 1 0 9.996z" />
                        </svg>
                    </button>
                    <div id="dropdown_menu" class="dropdown_menu collapsed">
                        <a id="edit_host" class="transition_link" href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5853ff"
                                class="bi bi-pencil" viewBox="0 0 16 16">
                                <path
                                    d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                            </svg>
                            Edit
                        </a>
                        <button data-modal="pingAgentDeleteDialog" class="openModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f83b3b"
                                class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                <path
                                    d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
                <div class="time-interval">
                    <label>
                        <input checked type="radio" name="timeRange" value="1">
                        <span>Last day</span>
                    </label>
                    <label>
                        <input type="radio" name="timeRange" value="2">
                        <span>Last month</span>
                    </label>
                    <label>
                        <input type="radio" name="timeRange" value="3">
                        <span>Last year</span>
                    </label>
                </div>

            </article>

            <article id="ping_agent_status" class="status">
                <div class="current-status card">
                    <h3>Current status</h3>
                    <h4 id="current_status" class="up">
                        <svg class="spinner-circle blue" class="up-since" xmlns="http://www.w3.org/2000/svg"
                            height="24px" viewBox="0 -960 960 960" width="24px">
                            <path
                                d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
                        </svg>
                    </h4>
                    <div class="timers">
                        <p id="up_since">
                        <div id="up_since_placeholder" class="placeholder-load-container" style="width: 60%; height: .7rem;">
                        </div>
                        </p>
                        <p id="last_check">
                        <div id="last_check_placeholder" class="placeholder-load-container" style="width: 60%; height: .7rem;">
                        </div>
                        </p>
                    </div>
                </div>

                <div class="availability card">

                    <h3>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-activity" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M6 2a.5.5 0 0 1 .47.33L10 12.036l1.53-4.208A.5.5 0 0 1 12 7.5h3.5a.5.5 0 0 1 0 1h-3.15l-1.88 5.17a.5.5 0 0 1-.94 0L6 3.964 4.47 8.171A.5.5 0 0 1 4 8.5H.5a.5.5 0 0 1 0-1h3.15l1.88-5.17A.5.5 0 0 1 6 2" />
                        </svg>
                        Availability
                    </h3>

                    <div id="availability_percentage">
                        <!--
                        <div class="circle-percent box" style="background: conic-gradient(var(--accent-green) 0% 90%, transparent 90% 100%);">
                            <div class="circle-percent core">
                                <p id="uptime_percentage">90%</p>
                            </div>
                        </div>
                        -->
                    </div>

                </div>
                <div class="statistics card">
                    <div>
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                class="bi bi-plus-slash-minus" viewBox="0 0 16 16">
                                <path
                                    d="m1.854 14.854 13-13a.5.5 0 0 0-.708-.708l-13 13a.5.5 0 0 0 .708.708M4 1a.5.5 0 0 1 .5.5v2h2a.5.5 0 0 1 0 1h-2v2a.5.5 0 0 1-1 0v-2h-2a.5.5 0 0 1 0-1h2v-2A.5.5 0 0 1 4 1m5 11a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5A.5.5 0 0 1 9 12" />
                            </svg>
                            Average
                        </h3>
                        <p id="latency_average">
                            <svg class="spinner-circle" id="load_latency_average" class="up-since"
                                xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                                <path
                                    d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
                            </svg>
                        </p>
                    </div>
                    <div>
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-caret-down-fill" viewBox="0 0 16 16">
                                <path
                                    d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z" />
                            </svg>
                            Minimum
                        </h3>
                        <p id="latency_minimum">
                            <svg class="spinner-circle" id="load_latency_minimum" class="up-since"
                                xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                                <path
                                    d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
                            </svg>
                        </p>
                    </div>
                    <div>
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-caret-up-fill" viewBox="0 0 16 16">
                                <path
                                    d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z" />
                            </svg>
                            Maximum
                        </h3>
                        <p id="latency_maximum">
                            <svg class="spinner-circle" id="load_latency_maximum" class="up-since"
                                xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px">
                                <path
                                    d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
                            </svg>
                        </p>
                    </div>
                </div>
                <!--
                <div id="warning_trheshold" class="warning-trheshold card">
                    <div class="heartbeat-animation-container">
                        <div class="heartbeat-animation-heartbeat"></div>
                        <div class="heartbeat-animation-core">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                            </svg>
                        </div>
                    </div>
                    <p>Threshold exceeded</p>
                </div>
                -->
            </article>

            <article id="latency_graph_container" class="card">
                <div id="latency_graph" style="width: 100%; height: 100%;"></div>
            </article>


            <article class="log card">
                <h1>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-text" viewBox="0 0 16 16">
                        <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
                        <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2" />
                        <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z" />
                    </svg>
                    Latest events
                </h1>

                <div id="log_table">

                    <div id="table_body" class="table-body">
                        <!--
                    <article class="log_row">
                            <div class="log_event">
                                <img src="../img/svg/host/host_down.svg">
                                <p>Host down</p>
                            </div>

                            <div class="cause">
                                <p>Host down because is unreacheble</p>
                            </div>

                            <div class="started">
                                <p>12/02/2024 15:02:23</p>
                            </div>

                        </article>

                        <article class="log_row">
                            <div class="log_event">
                                <img src="../img/svg/host/host_up.svg">
                                <p>Host UP</p>
                            </div>
                            <div class="cause">
                                <p>Host up againd</p>
                            </div>
                            <div class="started">
                                <p>12/02/2024 16:02:23</p>
                            </div>
                        </article>

                        <article class="log_row">
                            <div class="log_event">
                                <img src="../img/svg/host/host_alert.svg">
                                <p>Threshhold sobrepass</p>
                            </div>
                            <div class="cause">
                                <p>the ping is 123ms</p>
                            </div>
                            <div class="started">
                                <p>12/02/2024 17:02:23</p>
                            </div>
                        </article>
                        -->
                    </div>
                </div>
            </article>


        </section>
    </main>


    <dialog id="pingAgentDeleteDialog">
        <div class="dialog-header">
            <h1>Delete Host?</h1>
            <button class="close-modal close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x"
                    viewBox="0 0 16 16">
                    <path
                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                </svg>

            </button>
        </div>
        <div class="dialog-body">
            <p>This can't be undone</p>
        </div>
        <div class="dialog-options">
            <button id="cancel-btn" class="close-btn">Cancel</button>
            <button id="pingAgentDeleteBtn">

                <div class="text show">
                    Delete
                </div>

                <div class="loader-hourglass hide">
                    <svg class="spinner-hourglass" xmlns="http://www.w3.org/2000/svg" height="20px"
                        viewBox="0 -960 960 960" width="20px" fill="#e8eaed">
                        <path
                            d="M320-160h320v-120q0-66-47-113t-113-47q-66 0-113 47t-47 113v120Zm160-360q66 0 113-47t47-113v-120H320v120q0 66 47 113t113 47ZM160-80v-80h80v-120q0-61 28.5-114.5T348-480q-51-32-79.5-85.5T240-680v-120h-80v-80h640v80h-80v120q0 61-28.5 114.5T612-480q51 32 79.5 85.5T720-280v120h80v80H160Z" />
                    </svg>
                </div>

            </button>
        </div>
    </dialog>



    <script src="../js/page_transition.js"></script>
    <script src="../js/components/alert.js"></script>
    <script src="../js/functions/get_url_param_id.js"></script> <!-- pronto deprecado -->
    <script src="../js/functions/remove_load_curtain.js"></script>

    <script src="../js/modal.js"></script>
    <script src="../js/components/linkDropDown.js"></script>



    <script src="../modules/echart/node_modules/echarts/dist/echarts.min.js"></script>

    <script type="module" src="../js/API/ping_agent_get_data.js"></script>
    <script type="module" src="../js/API/ping_agent_get_statistics.js"></script>
    <script type="module" src="../js/API/agent_get_log.js"></script>
    <script type="module" src="../js/API/ping_agent_delete.js"></script>

    <script type="module">
    import {agent_get_log } from "../js/API/agent_get_log.js";
    agent_get_log('ping_agent_log');
    setInterval(() => agent_get_log('ping_agent_log'), 60000);
    </script>

</body>

</html>