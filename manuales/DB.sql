--BASE DE DATOS
CREATE DATABASE elipticnet;
USE elipticnet;

-- ╔══════════╗
-- ║ Usuarios ║
-- ╚══════════╝

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,            -- Hasta 100 caracteres
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,            -- Guardará el hash de la contraseña
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, --No probado
    validation_hash CHAR(24) DEFAULT NULL,         -- Hash de validación de 24 caracteres en hexadecimal
    hash_date DATETIME DEFAULT NULL,               -- Fecha y hora de creación del hash
    enable BOOLEAN NOT NULL DEFAULT True,           -- Estado de validación, 0 = no validado, 1 = validado
    type INT NOT NULL DEFAULT 0,                    -- Tipo de usuario
    cookie_hash CHAR(24) DEFAULT NULL            -- Hash para la cookie de sesión
);


CREATE TABLE user_config (
    owner INT NOT NULL PRIMARY KEY,
    time_zone VARCHAR(50) DEFAULT NULL,
    language VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (owner) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE pre_release (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    language VARCHAR(255) DEFAULT NULL,
    hash_id CHAR(24) DEFAULT NULL,         -- Hash de identificación
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ╔════════════╗
-- ║ Transports ║
-- ╚════════════╝



CREATE TABLE transports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner INT NOT NULL,
    type VARCHAR(255) NOT NULL,               -- Almacena el tipo de transporte
    alias VARCHAR(15) DEFAULT NULL,               -- Almacena el alias de transporte
    transport_id VARCHAR(255) NOT NULL,         -- Almacena el contacto del usuario
    valid BOOLEAN NOT NULL DEFAULT False,          -- Estado de validación, 0 = no validado, 1 = validado
    retries INT DEFAULT NULL,           -- Reenvio de mensajes de validación
    validation_sent BOOLEAN DEFAULT NULL, -- True si se envio notificacion para validar el transporte
    validation_hash CHAR(24),         -- Hash de validación de 24 caracteres en hexadecimal
    hash_date DATETIME,               -- Fecha y hora de creación del hash
    FOREIGN KEY (owner) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO transports (owner, type,alias, transport_id, valid, validation_hash, hash_date)
VALUES (1, 'telegram', 'mi telegram', '124456', True, 'a1b2c3d4e5f6g7h8i9j0k1l2', NOW());

-- ╔══════════════════════╗
-- ║ Agentes de monitoreo ║
-- ╚══════════════════════╝

CREATE TABLE ping_agent_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner INT NOT NULL,
    ip VARCHAR(39),
    name CHAR(25) NOT NULL,
    description VARCHAR(200),
    state BOOLEAN DEFAULT NULL,
    threshold INT DEFAULT NULL,
    threshold_exceeded BOOLEAN DEFAULT NULL,
    threshold_time DATETIME DEFAULT NULL,
    last_check DATETIME DEFAULT NULL,
    last_down DATETIME DEFAULT NULL,
    last_up DATETIME DEFAULT NULL,
    transports JSON,
    extra JSON DEFAULT NULL,
    FOREIGN KEY (owner) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE ping_agent_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ping_agent_id INT NOT NULL,
    icon VARCHAR (255) DEFAULT NULL,
    cause VARCHAR (255) DEFAULT NULL,
    message VARCHAR (255) DEFAULT NULL,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ping_agent_id) REFERENCES ping_agent_data(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE latency (
    id INT AUTO_INCREMENT PRIMARY KEY,
    host_id INT NOT NULL,
    latency FLOAT,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (host_id) REFERENCES ping_agent_data(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- ╔═════════╗
-- ║ Usuario ║
-- ╚═════════╝

CREATE USER 'USER'@'localhost' IDENTIFIED BY 'PASSWORD';
GRANT ALL PRIVILEGES ON *.* TO 'USER'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;

-- ╔════════╗
-- ║ Extras ║
-- ╚════════╝

--Peso de la tabla latency
SELECT table_name AS "Table", ROUND(data_length / 1024 / 1024, 2) AS "Data Size (MB)", ROUND(index_length / 1024 / 1024, 2) AS "Index Size (MB)", ROUND((data_length + index_length) / 1024 / 1024, 2) AS "Total Size (MB)" FROM information_schema.tables WHERE table_schema = 'elipticnet' AND table_name = 'latency';

--Alter table
ALTER TABLE users
ADD registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
THRESHOLD EXCEEDED

/*
 * Ultimo mes promediando por día
 * Tiene problemas para calcular el porcentaje de uptime
 */
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

/*
 * Ultimo mes con datos exaustivos por día
 * calcula bien el porcentaje de uptime
 * Es poco eficiente por la cantidad de datos que manda al front
 */

 SELECT Json_arrayagg(Json_object('time', time, 'latency', Round(latency, 2))) AS latency_time_json,
       Avg(latency)                                                           AS average_latency,
       Round(Min(latency), 2)                                                 AS minimum_latency,
       Round(Max(latency), 2)                                                 AS maximum_latency,
       Round(100 * Count(CASE
                          WHEN latency IS NOT NULL THEN 1
                        END) / Count(*), 2)                                   AS uptime_percentage
FROM   (SELECT l.time AS time,
               l.latency AS latency
        FROM   latency l
               JOIN host_data h
                 ON l.host_id = h.id
        WHERE  l.host_id = ?
               AND h.owner = ?
               AND l.time >= Now() - INTERVAL 1 month) AS subquery;
