<?php
// Cargar las variables de entorno
require_once __DIR__ . '/../env.php';
include __DIR__ . '/../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];

try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->connect_error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
        exit;
    }

    // Consulta SQL para obtener los hosts del usuario
    $sql = "SELECT id, ip, alias, state, last_check, last_up, threshold_exceeded FROM ping_agent_data WHERE owner = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("[ERROR] " . __FILE__ . ": " . $conn->error);
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);
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
            "name" => $row['alias'],
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
    error_log("[ERROR] " . __FILE__ . ": " . $e->getMessage());
    echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => "We are experiencing problems, please try again later or", "link_text" => "contact support", "link" => "mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."]);

} finally {
    if (isset($stmt) && $stmt !== false) {
        $stmt->close();
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}
