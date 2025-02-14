<?php
require __DIR__ . "/../../env.php";
require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/../../functions/create_session.php";

require_once __DIR__ . '/../../email/email.php';
require_once __DIR__ . '/../../email/templates/welcome_new_user.php';




$client = new Google\Client;

$client->setClientId(CLIENT_ID);
$client->setClientSecret(SECRET);
$client->setRedirectUri(APP_LINK . "/aplication/php/sesion/google/oauth2callback.php");

if (! isset($_GET["code"])) {
    header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
    exit("Login failed");
}

/* Prueba de implementacion de state para segurizar los login por google*/
if (! isset($_GET["state"])) {
    error_log("[ERROR] " . __FILE__ . ": oauth_state no presente en link");
} else {
    session_start();
    if ($_GET["state"] != $_SESSION['oauth_state']) {
        error_log("[ERROR] " . __FILE__ . ": oauth_state no coincide");
    }
}

$token = $client->fetchAccessTokenWithAuthCode($_GET["code"]);

$client->setAccessToken($token["access_token"]);

$oauth = new Google\Service\Oauth2($client);

$userinfo = $oauth->userinfo->get();

$email = $userinfo->email;
$name = $userinfo->name;
$verified_email = $userinfo->verified_email;


try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->connect_error);
        header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
        exit;
    }

    //check if the email already exists in the "users" table

    $check_query = "SELECT COUNT(*) FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 1) {
        /*
        * El email existe en la base de datos =>
        * Verificar que la cuenta esté habilitada
        * Loguear al usuario
        */

        $sql = "SELECT id, password, username, email, enable, type FROM users WHERE email = ?";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
            header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
            exit;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_db = $result->fetch_assoc();

        if (!$user_db['enable']) {
            header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=user_not_enable");
            exit;
        }

        create_session($user_db['id'], $user_db['username'], $user_db['email'], $user_db['type']);

        header("Location: " . APP_LINK . "/aplication/public/home.php");
        exit;
    }
    if ($count > 1) {
        //error, hay varios registros con el mismo correo
        error_log("[ERROR] " . __FILE__ . ": La consulta devolvió más de una cuenta asociada");
        header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
        exit;
    }


    /*El correo no existe
    * Insertar registro de users
    * Insertar registro de user_config
    * Insertar registro de correo en transports
    * Loguear al usuario
    */

    // Generar password aleatoria
    $hashed_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

    $user_type = 0; //default free user

    // Insert new user
    $sql = "INSERT INTO users  (username, email, password, type) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
        exit;
    }

    $stmt->bind_param("ssss", $name, $email, $hashed_password, $user_type);

    if (!$stmt->execute()) {
        error_log("[ERROR] " . __FILE__ . ": " . $e->getMessage());
        header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
        exit;
    }

    // Obtener el ID del registro insertado
    $user_id = $conn->insert_id;

    //Insertar el transporte email con el ID del usuario como owner
    $sql = "INSERT INTO transports (owner, type, alias, transport_id, valid) 
                                   VALUES ( ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
        exit;
    }

    $type = "email";
    $valid = true;

    $stmt->bind_param("isssi", $user_id, $type, $name, $email, $valid);

    if (!$stmt->execute()) {
        error_log("[ERROR]: Insert transport email user signup.php:" . $e->getMessage());
        header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
        exit;
    }

    create_session($user_id, $name, $email, $type);

    /*
    //Configuracion de usuario
    // Obtener el idioma del navegador
    $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en';
    $primaryLanguage = explode(',', $acceptLanguage)[0];
    $languageCode = explode('-', $primaryLanguage)[0];
    */

    $sql = "INSERT INTO user_config (owner, time_zone, language) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
        exit;
    }

    $time_zone = NULL; //Por el momento no tengo forma de saberlo
    $languageCode = NULL;
    $stmt->bind_param("iss", $user_id, $time_zone, $languageCode);


    if (!$stmt->execute()) {
        error_log("[ERROR] " . __FILE__ . ": " . $e->getMessage());
        header("Location:" . __DIR__ . "/../../public/aplication/public/login.php?error=true&message=Connection_Error");
        exit;
    }


    /*
    ╔════════════════════════════╗
    ║ Enviar email de bienvenida ║
    ╚════════════════════════════╝
    */
    try {
        $body = welcome_new_user_template($name);
        send_email($body, "Welcome to Elipticnet!", $email);
    } catch (Exception $e) {
        // Registrar el error en el log de PHP
        error_log("Error al enviar el email de bienvenida: " . $e->getMessage());
    }

    header("Location: " . APP_LINK . "/aplication/public/home.php");

    /* Loguearse */
} catch (Exception $e) {
    // Manejo de errores
    error_log("[ERROR] " . __FILE__ . ": " . $e->getMessage());
    header("Location: " . APP_LINK . "/aplication/public/login.php?error=" . urlencode($e->getMessage()));
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
