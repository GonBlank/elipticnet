<?php
require_once '../env.php';

function close_sesion()
{
    // Clear the remember_me cookie if it exists
    if (isset($_COOKIE['remember_me'])) {
        setcookie('remember_me', '', time() - 3600, '/');
        try {
            // Delete the cookie_hash in the "users" table
            $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql_update_hash = "UPDATE users SET cookie_hash = NULL WHERE email = :email";
            $stmt_update_hash = $conn->prepare($sql_update_hash);
            $stmt_update_hash->bindParam(':email', $_SESSION['user']['email']);
            $stmt_update_hash->execute();
            $conn = null;
        } catch (PDOException $e) {
            $conn = null;
            error_log("[ERROR]: Update the hash in the users table logout.php:" . $e->getMessage());
            echo json_encode(["error" => true, "type" => "error", "title" => "Database connection failed", "message" => "Cookie hash not deleted." . $e->getMessage()]);
            exit;
        }
    }

    // Clear session cookies if necessary
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    // Delete all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();
}
