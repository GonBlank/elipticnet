import mysql.connector
import json

CONFIG_PATH = "/var/www/elipticnet/aplication/config/config.json"
with open(CONFIG_PATH, "r") as file:
    config = json.load(file)

DB_CONFIG = {
    "host": config["database"]["DB_HOST"],
    "user": config["database"]["DB_USER"],
    "password": config["database"]["DB_PASS"],
    "database": config["database"]["DB_NAME"],
    "ssl_disabled": True,
}

def get_user_timezone(user_id):
    """Obtiene la zona horaria del usuario."""
    connection = None
    cursor = None
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor(dictionary=True)

        query = """
        SELECT time_zone FROM user_config WHERE owner = %s;
        """
        cursor.execute(query, (user_id,))
        result = cursor.fetchone()
        return result["time_zone"]

    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return None
    finally:
        if cursor is not None:
            cursor.close()
        if connection is not None and connection.is_connected():
            connection.close()
