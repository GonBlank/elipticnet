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
    <title>Home</title>

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

    <!-- Styles -->

    <!--Generics-->
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/palette.css" />

    <!--Components-->
    <link rel="stylesheet" href="../css/components/alert.css" />
    <link rel="stylesheet" href="../css/components/loader.css" />
    <link rel="stylesheet" href="../css/components/dialog.css" />
    <link rel="stylesheet" href="../css/components/button.css" />
    <link rel="stylesheet" href="../css/components/drop_down_menu.css" />

    <!--Specific-->
    <link rel="stylesheet" href="../css/style_home.css" />



</head>

<body>
    <nav>
        <button id="menu_button" onclick="open_menu()">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5f6368">
                <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z" />
            </svg>
        </button>
        <div class="nav-icon" id="nav-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-house-door-fill" viewBox="0 0 16 16">
                <path
                    d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5" />
            </svg>
        </div>
        <h1 id="nav-tittle"> Home</h1>
    </nav>

    <?php include '../php/global/lateral_menu/lateral_menu.php'; ?>

    <main class="dashboard">

        <div id="load-curtain">
            <svg class="spinner-circle" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                width="24px">
                <path
                    d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
            </svg>
        </div>

        <section class="area-statistics">
            <div id="hosts_up" class="statistics-card card up">
                <h1>UP</h1>
                <h2>
                    <svg class="spinner-circle" xmlns="http://www.w3.org/2000/svg" height="24px"
                        viewBox="0 -960 960 960" width="24px">
                        <path
                            d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
                    </svg>
                </h2>
            </div>
            <div id="hosts_down" class="statistics-card card down">
                <h1>DOWN</h1>
                <h2>
                    <svg class="spinner-circle" xmlns="http://www.w3.org/2000/svg" height="24px"
                        viewBox="0 -960 960 960" width="24px">
                        <path
                            d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
                    </svg>
                </h2>
            </div>
            <div id="hosts_monitored" class="statistics-card card monitored">
                <h1>Monitored</h1>
                <h2>
                    <svg class="spinner-circle" xmlns="http://www.w3.org/2000/svg" height="24px"
                        viewBox="0 -960 960 960" width="24px">
                        <path
                            d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z" />
                    </svg>
                </h2>
            </div>
        </section>

        <section class="monitored-hosts card">
            <div id="agents_menu_container" class="dropdown_menu_container">
                <button id="dropdown_button" class="host-options dropdown-button">
                    NEW AGENT
                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"
                        fill="#e8eaed">
                        <path d="M480-384 288-576h384L480-384Z" />
                    </svg>
                </button>
                <div id="dropdown_menu" class="dropdown_menu collapsed">
                    <a id="ping_agent" href="ping_agent_create.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-vinyl" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="M8 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4M4 8a4 4 0 1 1 8 0 4 4 0 0 1-8 0" />
                            <path d="M9 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                        </svg>
                        <p> Ping agent </p>
                    </a>
                </div>
            </div>

            <div id="host_table" class="table">
                <div id="row_place_holder" class="row">
                    <svg class="spinner-circle" xmlns="http://www.w3.org/2000/svg" height="24px"
                        viewBox="0 -960 960 960" width="24px">
                        <path
                            d="M480-80q-82 0-155-31.5t-127.5-86Q143-252 111.5-325T80-480q0-83 31.5-155.5t86-127Q252-817 325-848.5T480-880q17 0 28.5 11.5T520-840q0 17-11.5 28.5T480-800q-133 0-226.5 93.5T160-480q0 133 93.5 226.5T480-160q133 0 226.5-93.5T800-480q0-17 11.5-28.5T840-520q17 0 28.5 11.5T880-480q0 82-31.5 155t-86 127.5q-54.5 54.5-127 86T480-80Z">
                        </path>
                    </svg>
                </div>

                <!-- 
                <a href="#" class="row">
                    <div class="host-satus">
                        <div class="heartbeat-animation-container">
                            <div class="heartbeat-animation-heartbeat"></div>
                            <div class="heartbeat-animation-core">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="host-data">
                        <h1 id="host-name">TEST</h1>
                        <p id="host-ip">8.8.8.8</p>
                    </div>
                    <div class="host-extra-info">
                        <p id="up-since" class="up-since">Up 1 day, 1hr</p>

                        </small>
                    </div>
                </a>               
                -->
            </div>
        </section>
    </main>

    <script src="../js/components/alert.js"></script>
    <script src="../js/components/dialog.js"></script>
    <script src="../js/page_transition.js"></script>
    <script src="../js/functions/remove_load_curtain.js"></script>
    <script src="../js/components/drop_down_menu.js"></script>
    <script src="../js/API/load_home_view.js"></script>


</body>

</html>