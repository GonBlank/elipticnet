import sys
import os

# Obtener la ruta absoluta de la raíz del proyecto
project_root = os.path.abspath(os.path.join(os.path.dirname(__file__), "../../"))

# Agregar esa ruta al sys.path para que Python pueda buscar ahí
sys.path.append(project_root)

import mysql.connector
import json

from transports.transport_telegram.telegram_bot import TelegramBot

from templates.ping_agent_templates import (
    validate_transport,
)


# ╔═════════════════╗
# ║ CONFIGURATIONS  ║
# ╚═════════════════╝

CONFIG_PATH = "/var/www/elipticnet/aplication/config/config.json"
with open(CONFIG_PATH, "r") as file:
    config = json.load(file)

DB_CONFIG = {
    "host": config["database"]["DB_HOST"],
    "user": config["database"]["DB_USER"],
    "password": config["database"]["DB_PASS"],
    "database": config["database"]["DB_NAME"],
    "ssl_disabled": config["database"]["SSL_DISABLED"],
}

# ╔═════════════╗
# ║ LOG CONFIG  ║
# ╚═════════════╝

import log.log_config

# Configurar el logger para este script
logger = log.log_config.setup_logger("telegram_validator")

# ╔════════════╗
# ║ FUNCTIONS  ║
# ╚════════════╝


def fetch_telegrams_to_validate():
    connection = None
    cursor = None
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor(dictionary=True)

        query = """
        SELECT owner, transport_id, retries, validation_hash FROM transports WHERE type = "telegram" AND valid = 0 AND validation_sent IS NOT TRUE;
        """
        cursor.execute(query)
        results = cursor.fetchall()
        return results

    except mysql.connector.Error as e:
        #print(f"Error: {e}")
        logger.warning(f"Error: {e}")
        return None
    finally:
        if cursor is not None:
            cursor.close()
        if connection is not None and connection.is_connected():
            connection.close()


def update_transport_telegram_state(telegram_id, owner):
    validation_sent = True
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()
        update_query = """UPDATE transports SET validation_sent = %s WHERE transport_id = %s AND owner = %s"""
        cursor.execute(update_query, (validation_sent, telegram_id, owner))
        connection.commit()

    except mysql.connector.Error as e:
        #print(f"ERROR: {e}")
        logger.warning(f"Error: {e}")
    finally:
        cursor.close()
        connection.close()


# ╔═══════════════╗
# ║ MAIN PROGRAM  ║
# ╚═══════════════╝

if __name__ == "__main__":
    telegram_bot = TelegramBot(CONFIG_PATH)
    telegram_list = fetch_telegrams_to_validate()

    # Verificar si la lista está vacía
    if not telegram_list:
        #print("[INFO] No transports to validate")
        logger.info("No transports to validate")
    else:
        for telegram in telegram_list:
            #print("[TRYING] Send validation to: " + telegram["transport_id"])
            logger.info("[TRYING] Send validation to: " + telegram["transport_id"])
            try:
                message = validate_transport(telegram["validation_hash"])
                telegram_bot.send_message(telegram["transport_id"], message)
                update_transport_telegram_state(
                    telegram["transport_id"], telegram["owner"]
                )
                #print("[SUCCESS]")
                logger.info("SUCCESS")
            except:
                logger.error("SUCCESS")
                #print("[FAIL]")
