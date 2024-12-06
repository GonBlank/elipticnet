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
    $sql = "SELECT id, ip, name, state, last_check, last_up FROM host_data WHERE owner = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("i", $owner);
    $stmt->execute();
    $result = $stmt->get_result();

    // Comprobar si hay resultados
    if ($result->num_rows > 0) {
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
                "last_check" => $row['last_check']
            );
        }

        // Devolver los resultados en formato JSON
        header('Content-Type: application/json');
        echo json_encode($hosts, JSON_PRETTY_PRINT);
    } else {
        // Si no hay resultados, devolver un mensaje vacío
        header('Content-Type: application/json');
        echo json_encode([]);
    }

} catch (Exception $e) {
    // Manejo de errores
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
} finally {
    $conn->close();
    $stmt->close();
}
