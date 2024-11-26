<?php
// Cargar las variables de entorno
require_once 'env.php';
include '../php/sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];

$conn = null; // Inicializar la conexión
try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error:", "message" => $conn->connect_error]);
        exit;
    }

    // Obtener los datos JSON enviados a través del POST
    $data = json_decode(file_get_contents('php://input'), true);

    // Función para validar la dirección IP
    function validate_ip($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    if (isset($data['name']) && isset($data['ip'])) {
        // Validar la IP
        if (!validate_ip($data['ip'])) {
            echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "The IP address is not valid."]);
            exit;
        }

        // Verificar si ya existe un host con la misma IP y owner
        $check_query = "SELECT COUNT(*) FROM host_data WHERE ip = ? AND owner = ?";

        $stmt = $conn->prepare($check_query);
        if (!$stmt) {
            echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $conn->error]);
            exit;
        }

        $stmt->bind_param("ss", $data['ip'], $owner);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo json_encode(["error" => true, "type" => "warning", "title" => "Duplicate Error", "message" => "The host already exists."]);
            exit;
        }

        // Insertar el host en la base de datos
        $sql = "INSERT INTO host_data (owner, ip, name, description, state, latency, log, transports, extra, last_check, last_down, last_up) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $conn->error]);
            exit;
        }

        // Convertir los valores JSON
        $latency_json = json_encode([]);
        $log_json = json_encode([]);
        $transports_json = json_encode($data['transports']);
        $extra_json = json_encode([]);

        $state = true;
        $last_check = NULL;
        $last_down = NULL;
        $last_up = NULL;

        // Bind parameters
        $stmt->bind_param("isssssssssss", $owner, $data['ip'], $data['name'], $data['description'], $state, $latency_json, $log_json, $transports_json, $extra_json, $last_check, $last_down, $last_up);

        if (!$stmt->execute()) {
            echo json_encode(["error" => true, "type" => "error", "title" => "Insertion Error", "message" => $stmt->error]);
            exit;
        }

        // Obtener el ID generado
        $newHost['id'] = $conn->insert_id;;

        // Devolver el host completo en formato JSON
        echo json_encode($newHost);

    } else {
        echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "Incomplete data."]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
} finally {
    // Cerrar la conexión
    if ($conn) {
        $conn->close();
    }
}
