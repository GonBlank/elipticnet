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
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
    }

    // Consulta SQL para obtener los hosts del usuario
    $sql = "SELECT data FROM host_data WHERE owner = $owner";

    // Ejecutar la consulta
    $result = $conn->query($sql);

    // Comprobar si hay resultados
    if ($result === false) {
        throw new Exception("Query Error: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        // Crear un array para almacenar los datos
        $hosts = array();

        // Recorrer cada fila y agregar al array
        while ($row = $result->fetch_assoc()) {
            $hosts[] = json_decode($row['data'], true);
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
    // Cerrar la conexión
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
