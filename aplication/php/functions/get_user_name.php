<?php
include "../env.php";

function get_user_name($id)
{
    try {
        // Conectar a la base de datos
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Verificar conexión
        if ($conn->connect_error) {
            error_log("[ERROR] " . __FILE__ . ": " . $conn->connect_error);
            return null;
        }

        $sql = "SELECT username FROM users WHERE id = ?";

        // Preparar la consulta
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
            return null;
        }

        // Vincular parámetros y ejecutar consulta
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result && $user = $result->fetch_assoc()) {
            return $user['username'];
        } else {
            error_log("[INFO] get_user_name: No user found with ID $id");
            return null;
        }
    } catch (\Throwable $th) {
        error_log("[ERROR] " . __FILE__ . ": " . $th->getMessage());
        return null;
    } finally {
        // Asegurarse de cerrar conexiones y liberar recursos
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
}
