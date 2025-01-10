<?php


function generate_random_hash($conn, $table, $parameter)
{
    $hash = bin2hex(random_bytes(12));

    //check if the hash exists in the "users" table
    try {
        $sql_check_hash = "SELECT COUNT(*) FROM $table WHERE $parameter = :hash";
        $stmt_check_hash = $conn->prepare($sql_check_hash);
        $stmt_check_hash->bindParam(':hash', $hash);
        $stmt_check_hash->execute();
        $hash_exists = $stmt_check_hash->fetchColumn();
    } catch (PDOException $e) {
        $conn = null;
        error_log("[ERROR]:Check if the hash already exists in the users table login.php:" . $e->getMessage());
        echo json_encode(["error" => true, "type" => "error", "title" => "Fatal error", "message" => "Database connection failed.". $e->getMessage()]);
        exit;
    }

    if ($hash_exists) {
        generate_random_hash($conn, $table, $parameter);
    } else {
        return $hash;
    }
}