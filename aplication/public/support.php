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
    <title>Support</title>

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
    <link rel="stylesheet" href="../css/support.css" />

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
                class="bi bi-life-preserver" viewBox="0 0 16 16">
                <path
                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m6.43-5.228a7.03 7.03 0 0 1-3.658 3.658l-1.115-2.788a4 4 0 0 0 1.985-1.985zM5.228 14.43a7.03 7.03 0 0 1-3.658-3.658l2.788-1.115a4 4 0 0 0 1.985 1.985zm9.202-9.202-2.788 1.115a4 4 0 0 0-1.985-1.985l1.115-2.788a7.03 7.03 0 0 1 3.658 3.658m-8.087-.87a4 4 0 0 0-1.985 1.985L1.57 5.228A7.03 7.03 0 0 1 5.228 1.57zM8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
            </svg>
        </div>
        <h1 id="nav-tittle"> Support</h1>

    </nav>

    <?php include '../php/global/lateral_menu/lateral_menu.php'; ?>


    <main class="dashboard">
        <section class="form-container">
            <h1>Need Assistance?</h1>
            <h2>We’re here to help! </h2>
            <p> Email us at <a
                    href="mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue.">support@elipticnet.com</a>
                , and we’ll respond as quickly as possible to
                resolve your issue.</p>
        </section>
    </main>


    <script src="../js/modal.js"></script>
    <script src="../js/page_transition.js"></script>

</body>

</html>