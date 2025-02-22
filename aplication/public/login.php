<?php
session_start();
// Verificar si el usuario está autenticado
if (isset($_SESSION['user'])) {
    // Redirige al login si no está autenticado
    header("Location: /aplication/public/home.php");
    exit();
}

require __DIR__ . "/../php/env.php";
require __DIR__ . "/../php/sesion/google/vendor/autoload.php";

$client = new Google\Client;

$client->setClientId(CLIENT_ID);
$client->setClientSecret(SECRET);
$client->setRedirectUri(APP_LINK . "/aplication/php/sesion/google/oauth2callback.php");

$client->addScope("email");
$client->addScope("profile");

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;
$client->setState($state);

$authUrl = $client->createAuthUrl();

?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

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

    <link rel="stylesheet" href="../css/palette.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/components/alert.css" />
    <link rel="stylesheet" href="../css/components/googeBtn.css" />
    <link rel="stylesheet" href="../css/login.css" />

    <!-- Re Captcha v3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=6LdbfsIqAAAAAJC2-7BKJzl180BpsN1_Bqy1t_Yv"></script>

</head>

<body>
    <main class="dashboard">

        <section class="login-container card ">

            <section class="login-form">

                <div class="tittle">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="#e8eaed">
                        <path
                            d="M120-120v-80h80v-560q0-33 23.5-56.5T280-840h400q33 0 56.5 23.5T760-760v560h80v80H120Zm560-80v-560H280v560h400ZM560-440q17 0 28.5-11.5T600-480q0-17-11.5-28.5T560-520q-17 0-28.5 11.5T520-480q0 17 11.5 28.5T560-440ZM280-760v560-560Z" />
                    </svg>
                    <h1>Sign in</h1>
                </div>

                <form>
                    <div class="input-container">
                        <label class="label" for="user-email">
                            <input tabindex="1" type="email" placeholder=" " class="input" id="user-email"
                                name="user-email" autocomplete="email" required />
                            <span class="label__name">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                                    width="24px" fill="#e8eaed">
                                    <path
                                        d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480v58q0 59-40.5 100.5T740-280q-35 0-66-15t-52-43q-29 29-65.5 43.5T480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480v58q0 26 17 44t43 18q26 0 43-18t17-44v-58q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93h200v80H480Zm0-280q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35Z" />
                                </svg> Email</span>
                            <div class="error-message" id="user-email-error"></div>
                        </label>

                        <label class="label" for="user-password">
                            <input tabindex="2" type="password" placeholder=" " class="input" id="user-password"
                                name="user-password" autocomplete="current-password" required minlength="8"
                                maxlength="20" />
                            <span class="label__name">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                                    width="24px" fill="#e8eaed">
                                    <path
                                        d="M280-240q-100 0-170-70T40-480q0-100 70-170t170-70q66 0 121 33t87 87h432v240h-80v120H600v-120H488q-32 54-87 87t-121 33Zm0-80q66 0 106-40.5t48-79.5h246v120h80v-120h80v-80H434q-8-39-48-79.5T280-640q-66 0-113 47t-47 113q0 66 47 113t113 47Zm0-80q33 0 56.5-23.5T360-480q0-33-23.5-56.5T280-560q-33 0-56.5 23.5T200-480q0 33 23.5 56.5T280-400Zm0-80Z" />
                                </svg>
                                Password</span>
                            <div class="error-message" id="user-password-error"></div>
                        </label>

                    </div>

                    <div class="options">
                        <div class="checkbox-wrapper-13">
                            <input tabindex="3" id="remember_me" name="remember_me" type="checkbox">
                            <label for="remember_me">Remember me</label>
                        </div>
                    </div>

                    <button id="login_btn" class="action_btn primary">
                        <div class="text show">
                            SIGN IN
                        </div>

                        <div class="loader-hourglass hide"> <!-- Cambiado a hide para que esté oculto por defecto -->
                            <svg class="spinner-hourglass" xmlns="http://www.w3.org/2000/svg" height="24px"
                                viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path
                                    d="M320-160h320v-120q0-66-47-113t-113-47q-66 0-113 47t-47 113v120Zm160-360q66 0 113-47t47-113v-120H320v120q0 66 47 113t113 47ZM160-80v-80h80v-120q0-61 28.5-114.5T348-480q-51-32-79.5-85.5T240-680v-120h-80v-80h640v80h-80v120q0 61-28.5 114.5T612-480q51 32 79.5 85.5T720-280v120h80v80H160Z" />
                            </svg>
                        </div>
                    </button>
                </form>

                <div class="link-options">
                    <button data-modal="forgot-password" class="openModal">
                        Forgot Password?
                    </button>
                    <button data-modal="sign-up" class="openModal">
                        Sign up!
                    </button>
                </div>


                <div class="divisor">
                    <hr>
                    <p>OR</p>
                </div>
                <!-- GOOGLE BTN -->
                <a href="<?= $authUrl; ?>" class="gsi-material-button">
                    <div class="gsi-material-button-state"></div>
                    <div class="gsi-material-button-content-wrapper">
                        <div class="gsi-material-button-icon">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: block;">
                                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                                <path fill="none" d="M0 0h48v48H0z"></path>
                            </svg>
                        </div>
                        <span class="gsi-material-button-contents">Sign in with Google</span>
                        <span style="display: none;">Sign in with Google</span>
                    </div>
                </a>

            </section>


        </section>

    </main>




    <dialog id="sign-up" class="card">
        <div class="dialog-header">
            <h1>
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor"
                    class="bi bi-person-add" viewBox="0 0 16 16">
                    <path
                        d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                    <path
                        d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z" />
                </svg>
                Sign up!
            </h1>
        </div>
        <div class="dialog-body">
            <form class="form-container">
                <div class="input-container">
                    <label class="label" for="register-username">
                        <input type="text" placeholder=" " class="input" id="register-username" name="register-username"
                            autocomplete="username" required minlength="3" maxlength="15" />
                        <span class="label__name">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-person-badge" viewBox="0 0 16 16">
                                <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                <path
                                    d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492z" />
                            </svg> Name</span>
                        <div class="error-message" id="register-username-error"></div>
                    </label>
                    <label class="label" for="register-user-email">
                        <input type="email" placeholder=" " class="input" id="register-user-email"
                            name="register-user-email" autocomplete="email" required />
                        <span class="label__name">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                                fill="#e8eaed">
                                <path
                                    d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480v58q0 59-40.5 100.5T740-280q-35 0-66-15t-52-43q-29 29-65.5 43.5T480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480v58q0 26 17 44t43 18q26 0 43-18t17-44v-58q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93h200v80H480Zm0-280q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35Z" />
                            </svg> Email</span>
                        <div class="error-message" id="register-user-email-error"></div>
                    </label>

                    <label class="label" for="register-user-password">
                        <input type="password" placeholder=" " class="input" id="register-user-password"
                            name="register-user-password" autocomplete="password" required minlength="8"
                            maxlength="20" />
                        <span class="label__name">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                                fill="#e8eaed">
                                <path
                                    d="M280-240q-100 0-170-70T40-480q0-100 70-170t170-70q66 0 121 33t87 87h432v240h-80v120H600v-120H488q-32 54-87 87t-121 33Zm0-80q66 0 106-40.5t48-79.5h246v120h80v-120h80v-80H434q-8-39-48-79.5T280-640q-66 0-113 47t-47 113q0 66 47 113t113 47Zm0-80q33 0 56.5-23.5T360-480q0-33-23.5-56.5T280-560q-33 0-56.5 23.5T200-480q0 33 23.5 56.5T280-400Zm0-80Z" />
                            </svg>
                            Password</span>
                        <div class="error-message" id="register-user-password-error"></div>
                    </label>
                </div>

                <button id="sign_up_btn" type="submit" class="action_btn primary">
                    <div class="text show">
                        SIGN UP
                    </div>
                    <div class="loader-hourglass hide"> <!-- Cambiado a hide para que esté oculto por defecto -->
                        <svg class="spinner-hourglass" xmlns="http://www.w3.org/2000/svg" height="24px"
                            viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                            <path
                                d="M320-160h320v-120q0-66-47-113t-113-47q-66 0-113 47t-47 113v120Zm160-360q66 0 113-47t47-113v-120H320v120q0 66 47 113t113 47ZM160-80v-80h80v-120q0-61 28.5-114.5T348-480q-51-32-79.5-85.5T240-680v-120h-80v-80h640v80h-80v120q0 61-28.5 114.5T612-480q51 32 79.5 85.5T720-280v120h80v80H160Z" />
                        </svg>
                    </div>
                </button>
<!--
                <button class="g-recaptcha"
                    data-sitekey="6LdbfsIqAAAAAJC2-7BKJzl180BpsN1_Bqy1t_Yv"
                    data-callback='onSubmit'
                    data-action='submit'>Submit</button>
-->

            </form>
        </div>
    </dialog>



    <dialog id="forgot-password" class="card">
        <div class="dialog-header">
            <h1>
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                    class="bi bi-person-lock" viewBox="0 0 16 16">
                    <path
                        d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m0 5.996V14H3s-1 0-1-1 1-4 6-4q.845.002 1.544.107a4.5 4.5 0 0 0-.803.918A11 11 0 0 0 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664zM9 13a1 1 0 0 1 1-1v-1a2 2 0 1 1 4 0v1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1zm3-3a1 1 0 0 0-1 1v1h2v-1a1 1 0 0 0-1-1" />
                </svg>
                Forgot Password?
            </h1>
        </div>
        <div class="dialog-body">
            <form class="form-container">
                <div class="input-container">
                    <label class="label" for="recovery-email">
                        <input type="email" placeholder=" " class="input" id="recovery-email"
                            name="recovery-email" autocomplete="email" required />
                        <span class="label__name">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                                fill="#e8eaed">
                                <path
                                    d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480v58q0 59-40.5 100.5T740-280q-35 0-66-15t-52-43q-29 29-65.5 43.5T480-280q-83 0-141.5-58.5T280-480q0-83 58.5-141.5T480-680q83 0 141.5 58.5T680-480v58q0 26 17 44t43 18q26 0 43-18t17-44v-58q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93h200v80H480Zm0-280q50 0 85-35t35-85q0-50-35-85t-85-35q-50 0-85 35t-35 85q0 50 35 85t85 35Z" />
                            </svg> Email</span>
                        <div class="error-message" id="recovery-email-error"></div>
                    </label>
                </div>

                <button id="recovery_btn" type="submit" class="action_btn primary">
                    <div class="text show">
                        RECOVERY
                    </div>
                    <div class="loader-hourglass hide"> <!-- Cambiado a hide para que esté oculto por defecto -->
                        <svg class="spinner-hourglass" xmlns="http://www.w3.org/2000/svg" height="24px"
                            viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                            <path
                                d="M320-160h320v-120q0-66-47-113t-113-47q-66 0-113 47t-47 113v120Zm160-360q66 0 113-47t47-113v-120H320v120q0 66 47 113t113 47ZM160-80v-80h80v-120q0-61 28.5-114.5T348-480q-51-32-79.5-85.5T240-680v-120h-80v-80h640v80h-80v120q0 61-28.5 114.5T612-480q51 32 79.5 85.5T720-280v120h80v80H160Z" />
                        </svg>
                    </div>
                </button>
            </form>
        </div>
    </dialog>

    <script src="../js/modal.js"></script>
    <script src="../js/components/alert.js"></script>
    <script src="../js/sesion/login.js"></script>
    <script src="../js/sesion/signup.js"></script>
    <script src="../js/sesion/validate_email.js"></script>
    <script src="../js/sesion/send_validation_code_restore_password.js"></script>
</body>

</html>