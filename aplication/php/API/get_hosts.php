<?php
// Cargar las variables de entorno
require_once '../env.php';
include '../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];

try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
        exit;
    }

    // Consulta SQL para obtener los hosts del usuario
    $sql = "SELECT id, ip, name, state, last_check, last_up, threshold_exceeded FROM host_data WHERE owner = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("[ERROR] login" . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "DB error", "message" => $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $owner);
    $stmt->execute();
    $result = $stmt->get_result();

    // Comprobar si hay resultados
    if ($result->num_rows == 0) {
        // Si no hay resultados, devolver un mensaje vacío
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }

    // Crear un array para almacenar los datos
    $hosts = array();

    // Recorrer cada fila y agregar al array
    while ($row = $result->fetch_assoc()) {
        // Agregar los datos directamente, no es necesario JSON decode
        $hosts[] = array(
            "id" => $row['id'],
            "ip" => $row['ip'],
            "name" => $row['name'],
            'state' => $row['state'], // ($row['state'] == 1) ? true : false Para que java interprete bien los estados
            "last_up" => $row['last_up'],
            "last_check" => $row['last_check'],
            "threshold_exceeded" => $row['threshold_exceeded']
        );
    }

    // Devolver los resultados en formato JSON
    header('Content-Type: application/json');
    echo json_encode($hosts, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    // Manejo de errores
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
