<?php
// Cargar las variables de entorno
require_once '../env.php';
include '../sesion/checkAuth.php';
$user = checkAuth();
$owner = $user['id'];


// Validar entrada
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && isset($_GET['time_range'])) {
    $hostId = $_GET['id'];
    $time_range = $_GET['time_range'];
} else {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid Request", "message" => "Invalid request method or missing id"]);
    exit;
}

if (empty($hostId) || !is_numeric($hostId)) {
    echo json_encode(["error" => true, "type" => "error", "title" => "Invalid Parameter", "message" => "Host ID must be a valid numeric value"]);
    exit;
}

$hostId = (int)$hostId;

try {

    // Conectar a la base de datos
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conn->connect_error) {
        echo json_encode(["error" => true, "type" => "error", "title" => "Connection Error", "message" => $conn->connect_error]);
        exit;
    }

    $sql = sql_data_range($time_range);

    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $hostId, $owner);
    $stmt->execute();
    $result = $stmt->get_result();
    $statistics = $result->fetch_assoc();
    echo json_encode($statistics, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    // Manejo de errores
    echo json_encode(["error" => true, "type" => "error", "title" => "Database Error", "message" => $e->getMessage()]);
} finally {

    if (isset($stmt) && $stmt !== false) {
        $stmt->close(); // Aquí se lanza el error si $stmt ya estaba cerrado.
    }
    if (isset($conn) && $conn !== false) {
        $conn->close();
    }
}



function sql_data_range($time_range)
{
    switch ($time_range) {
        case 1:
            //'1 DAY'
            return "
            SELECT 
            JSON_ARRAYAGG(
            JSON_OBJECT(
            'latency', ROUND(l.latency, 2),
            'time', l.time
            )
            ) AS latency_time_json,
            ROUND(AVG(l.latency), 2) AS average_latency,
            ROUND(MIN(l.latency), 2) AS minimum_latency,
            ROUND(MAX(l.latency), 2) AS maximum_latency,
            ROUND(
            100 * SUM(CASE WHEN l.latency IS NOT NULL THEN 1 ELSE 0 END) / COUNT(*),
            2
            ) AS uptime_percentage
            FROM 
            latency l
            JOIN 
            host_data h 
            ON 
            l.host_id = h.id
            WHERE 
            l.host_id = ?
            AND h.owner = ?
            AND l.time >= NOW() - INTERVAL 1 DAY;
            ";
            break;

        case 2:
            //'1 MONTH'
            return "
            SELECT 
            JSON_ARRAYAGG(
            JSON_OBJECT(
            'time', time,
            'latency', ROUND(latency, 2)
            )
            ) AS latency_time_json,
            AVG(latency) AS average_latency,
            ROUND(MIN(latency), 2) AS minimum_latency,
            ROUND(MAX(latency), 2) AS maximum_latency,
            ROUND(
            100 * SUM(CASE WHEN latency IS NOT NULL THEN 1 ELSE 0 END) / COUNT(*),
            2
            ) AS uptime_percentage
            FROM (
            SELECT 
            DATE(l.time) AS time,
            AVG(l.latency) AS latency,
            SUM(CASE WHEN l.latency IS NOT NULL THEN 1 ELSE 0 END) AS up_count,
            COUNT(*) AS total_count
            FROM 
            latency l
            JOIN 
            host_data h 
            ON 
            l.host_id = h.id
            WHERE 
            l.host_id = ?
            AND h.owner = ?
            AND l.time >= NOW() - INTERVAL 1 MONTH
            GROUP BY 
            DATE(l.time)
            ) AS subquery;

            ";
            break;

        case 3:
            //'1 YEAR'
            return "
            SELECT 
            JSON_ARRAYAGG(
            JSON_OBJECT(
                'time', time,
                'latency', ROUND(latency, 2)
            )
            ) AS latency_time_json,
            AVG(latency) AS average_latency,
            ROUND(MIN(latency),2) AS minimum_latency,
            ROUND(MAX(latency),2) AS maximum_latency
            FROM (
            SELECT 
            DATE_FORMAT(l.time, '%Y-%m') AS time,
            AVG(l.latency) AS latency
            FROM 
            latency l
            JOIN 
            host_data h 
            ON 
            l.host_id = h.id
            WHERE 
            l.host_id = ?
            AND h.owner = ?
            AND l.time >= NOW() - INTERVAL 1 YEAR
            GROUP BY 
            DATE_FORMAT(l.time, '%Y-%m')
            ) AS subquery;
            ";
            break;

        default:
            // Default '1 DAY'
            return "
            SELECT 
            JSON_ARRAYAGG(
            JSON_OBJECT(
                'latency', ROUND(l.latency, 2),
                'time', l.time
            )
            ) AS latency_time_json,
            ROUND(AVG(l.latency), 2) AS average_latency,
            ROUND(MIN(l.latency), 2) AS minimum_latency,
            ROUND(MAX(l.latency), 2) AS maximum_latency,
            ROUND(
            100 * SUM(CASE WHEN l.latency IS NOT NULL THEN 1 ELSE 0 END) / COUNT(*),
            2
            ) AS uptime_percentage
            FROM 
            latency l
            JOIN 
            host_data h 
            ON 
            l.host_id = h.id
            WHERE 
            l.host_id = ?
            AND h.owner = ?
            AND l.time >= NOW() - INTERVAL 1 DAY;
            ";
            break;
    }
}
