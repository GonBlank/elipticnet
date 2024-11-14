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
    function validate_ip($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    if (isset($data['name']) && isset($data['ip'])) {
        // Validar la IP
        if (!validate_ip($data['ip'])) {
            echo json_encode(["error" => true, "type" => "error", "title" => "Validation Error", "message" => "The IP address is not valid."]);
            exit;
        }

        // Verificar si ya existe un host con la misma IP y owner
        $check_query = "SELECT COUNT(*) FROM host_data WHERE JSON_EXTRACT(data, '$.ip') = ? AND JSON_EXTRACT(data, '$.owner') = ?";
        
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

        // Crear la estructura del nuevo host
        $newHost = array(
            "ip" => $data['ip'],
            "name" => $data['name'],
            "description" => $data['description'],
            "owner" => $owner,
            "latency" => array(),
            "log" => array(),
            "transports" => $data['transports'],
            "state" => true,
            "last_check" => Null,
            "last_down" => Null,
            "last_up" => Null,
        );

        // Insertar el host en la base de datos
        //$sql = "INSERT INTO host_data (data) VALUES ('" . $conn->real_escape_string(json_encode($newHost)) . "')";
        $sql = "INSERT INTO host_data (data, owner) VALUES ('" . $conn->real_escape_string(json_encode($newHost)) . "', '" . $conn->real_escape_string($owner) . "')";

        if (!$conn->query($sql)) {
            echo json_encode(["error" => true, "type" => "error", "title" => "Insertion Error", "message" => $conn->error]);
            exit;
        }

        // Obtener el ID generado
        $hostId = $conn->insert_id;

        // Actualizar el JSON en la base de datos con el ID
        $update_query = "UPDATE host_data SET data = JSON_SET(data, '$.id', ?) WHERE id = ?";
        $stmt = $conn->prepare($update_query);

        if (!$stmt) {
            echo json_encode(["error" => true, "type" => "error", "title" => "Update Query Error:", "message" => $conn->error]);
            exit;
        }

        $stmt->bind_param("ii", $hostId, $hostId);

        if (!$stmt->execute()) {
            echo json_encode(["error" => true, "type" => "error", "title" => "ID Update Error:", "message" => $stmt->error]);
            exit;
        }

        // Asignar el id de MySQL al campo id del nuevo host
        $newHost['id'] = $hostId;

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
?>
