<?php
// Cargar las variables de entorno
require_once 'env.php';
include '../php/sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];

try {
    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error:", "message" => $conn->connect_error]);
        exit;
    }

    // Obtener el hostId del parámetro GET
    $hostId = isset($_GET['id']) ? $_GET['id'] : null;

    // Validar el hostId
    if ($hostId === null) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Data Error:", "message" => "Host ID is required."]);
        exit;
    }

    // Obtener los datos JSON enviados a través del POST
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['name']) && isset($data['description']) && isset($data['transports'])) {
        // Sanear los datos
        $name = $data['name'];
        $description = $data['description'];
        $transports = json_encode($data['transports']); // Codificar el array transports como JSON

        // SQL para actualizar los campos
        $sql = "UPDATE host_data 
                SET name = ?, 
                    description = ?, 
                    transports = ? 
                WHERE id = ? AND owner = ?";

        $stmt = $conn->prepare($sql);

        // Pasar los valores al bind_param
        $stmt->bind_param(
            "ssssi", 
            $name, 
            $description, 
            $transports, 
            $hostId, 
            $owner
        );

        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Devolver el host actualizado
            $newHost = [
                'id' => $hostId,
                'name' => $name,
                'description' => $description,
                'transports' => $data['transports']
            ];
            echo json_encode($newHost);
        } else {
            echo json_encode(["error" => true, "type" => "error", "title" => "Update Error", "message" => "Failed to update host."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => true, "type" => "error", "title" => "Data Error", "message" => "Incomplete data."]);
    }

    // Cerrar la conexión
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
}
