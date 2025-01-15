<?php
function generate_random_hash($conn, $table, $parameter)
{
    // Validar nombres de tabla y columna
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    $parameter = preg_replace('/[^a-zA-Z0-9_]/', '', $parameter);
    $count = 0;

    do {
        // Generar un hash aleatorio
        $hash = bin2hex(random_bytes(12));

        // Consulta para verificar si el hash ya existe
        $check_query = "SELECT COUNT(*) FROM $table WHERE $parameter = ?";

        $stmt = $conn->prepare($check_query);
        if (!$stmt) {
            error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
            echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
            return null;
        }

        $stmt->bind_param("s", $hash);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0); // Reintentar si el hash ya existe

    return $hash; // Retornar el hash único
}
