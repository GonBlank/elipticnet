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
            throw new Exception("Database error: " . $conn->error);
        }

        $stmt->bind_param("s", $hash);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0); // Reintentar si el hash ya existe

    return $hash; // Retornar el hash único
}