import mysql.connector
import json
from ping3 import ping
from datetime import datetime, timezone


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

TIME = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S")

# ╔══════════════════╗
# ║ NOTIFIER CONFIG  ║
# ╚══════════════════╝
from transports.notifier import NotificationManager

notifier = NotificationManager(CONFIG_PATH, DB_CONFIG)

# ╔════════════╗
# ║ FUNCTIONS  ║
# ╚════════════╝


def fetch_ping_agent_data():
    connection = None
    cursor = None
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor(dictionary=True)

        query = """
        SELECT * FROM ping_agent_data;
        """
        cursor.execute(query)
        results = cursor.fetchall()
        return results

    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return None
    finally:
        if cursor is not None:
            cursor.close()
        if connection is not None and connection.is_connected():
            connection.close()


def ping_host(host):
    ping_response = ping(host["ip"], timeout=2, unit="ms")

    if ping_response != None and ping_response != False or type(ping_response) == float:
        # print("[INFO] HOST UP")
        ping_response = round(float(ping_response), 2)
        update_host_latency(host["id"], ping_response)  # round(ping_response, 2)
        return {"state": True, "response": ping_response}
    else:
        # print("[INFO] HOST DOWN")
        update_host_latency(host["id"], None)
        return {"state": False, "response": False}


def update_host_latency(host_id, latency):
    # print("[INFO] UPDATING HOST LATENCY")

    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()

        # Usar %s para todos los tipos de datos en la consulta
        update_query = """INSERT INTO latency (host_id, latency) VALUES (%s, %s)"""

        cursor.execute(update_query, (host_id, latency))
        connection.commit()

        # print("[INFO] Host latency updated successfully.")

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")

    finally:
        cursor.close()
        connection.close()


def check_state(host, new_state):
    if host["state"] == True and new_state["state"] == False:
        print("[DOWN] DETECT HOST DOWN")
        cause = log_message(new_state["response"])
        update_state(host, new_state["state"])
        update_host_log(host, "host_down", "Host down", cause)

        update_last_up_down(False, host["id"])
        notifier.notifier(host, "ping_agent_down", details={"cause": cause})
    elif host["state"] == False and new_state["state"] == True:
        print("[INFO] DETECT HOST UP")
        update_state(host, new_state["state"])
        update_host_log(host, "host_up", "Recovered host", "The host is back online")
        update_last_up_down(True, host["id"])
        notifier.notifier(host, "ping_agent_up")

    elif host["state"] == None:
        print("[UP] FIRST CHECK")
        update_host_log(
            host, "host_start", "First check", "The host begins to be monitored"
        )
        if new_state["state"] == True:
            # notifier.notifier(host, "ping_agent_up")
            update_state(host, new_state["state"])
            update_last_up_down(True, host["id"])
        else:
            # notifier.notifier(host, "ping_agent_down", details={"cause": "First check: host down"})
            update_state(host, new_state["state"])
            update_last_up_down(False, host["id"])
            update_host_log(
                host, "host_down", "Host down", log_message(new_state["response"])
            )


def update_state(host, new_state):
    # print("[INFO] UPDATING STATE")
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()

        # Consulta SQL para actualizar el campo 'state'
        update_query = """
        UPDATE ping_agent_data SET state = %s WHERE id = %s """
        cursor.execute(update_query, (new_state, host["id"]))
        connection.commit()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")

    finally:
        cursor.close()
        connection.close()


def update_host_log(host, icon, cause, message=None):
    # print("[INFO] UPDATING HOST LOG")

    # Convertir el log del host (si es una cadena JSON) a un objeto Python
    if isinstance(host["log"], str):  # Si el log es una cadena JSON
        try:
            host["log"] = json.loads(host["log"])  # Convertir a lista de diccionarios
        except json.JSONDecodeError as e:
            print(f"ERROR: No se pudo decodificar el log JSON: {e}")
            return

    # Agregar el nuevo registro al log
    host["log"].append(
        {
            "time": TIME,
            "icon": icon,
            "cause": cause,
            "message": message,
        }
    )

    try:
        # Conectar a la base de datos
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()

        # Actualizar el campo log en la base de datos
        update_query = """
        UPDATE ping_agent_data 
        SET log = %s
        WHERE id = %s
        """
        # Convertir log actualizado a JSON string antes de guardarlo en la base de datos
        cursor.execute(update_query, (json.dumps(host["log"]), host["id"]))
        connection.commit()
        print("[INFO] HOST LOG UPDATED SUCCESSFULLY")

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")
    finally:
        cursor.close()
        connection.close()


def log_message(ping_response):
    if ping_response == False and type(ping_response) != float:
        return "Host unknown (cannot resolve)"
    elif ping_response == None:
        return "Timed out (no reply)"
    elif ping_response or type(ping_response) == float:
        return "Recovery"
    else:
        return "Unknow"


def update_last_up_down(state, id):
    # print("[INFO] UPDATING LAST UP/DOWN")

    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()

        if state:
            update_query = """UPDATE ping_agent_data SET last_up = %s WHERE id = %s"""
        else:
            update_query = """UPDATE ping_agent_data SET last_down = %s WHERE id = %s"""

        cursor.execute(update_query, (TIME, id))
        connection.commit()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")
    finally:
        cursor.close()
        connection.close()


def check_threshold(host, ping_response):
    threshold = host.get("threshold")
    threshold_exceeded = host.get("threshold_exceeded", False)

    if ping_response and threshold is not None:
        if ping_response > threshold and not threshold_exceeded:
            print("[WARN] THRESHOLD EXCEEDED")
            notifier.notifier(
                host,
                "ping_agent_latency_threshold_exceeded",
                details={"latency": ping_response},
            )
            update_check_threshold(host["id"], True)
            update_host_log(
                host,
                "host_alert",
                "Threshold exceeded",
                f"The latency is above {threshold} ms.",
            )
            # send_alert(host.get('transports'))
        elif ping_response < threshold and threshold_exceeded:
            print("[OK] THRESHOLD RESTORED")
            notifier.notifier(
                host,
                "ping_agent_latency_threshold_restored",
                details={"latency": ping_response},
            )
            update_check_threshold(host["id"], False)
            update_host_log(
                host,
                "host_check",
                "Threshold recovered",
                f"The latency is now below {threshold} ms.",
            )
            # send_alert(host.get('transports'))


def update_check_threshold(id, threshold_state):
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()
        update_query = (
            """UPDATE ping_agent_data SET threshold_exceeded = %s WHERE id = %s"""
        )
        cursor.execute(update_query, (threshold_state, id))
        connection.commit()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")
    finally:
        cursor.close()
        connection.close()


def update_last_check(id):
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()
        update_query = """UPDATE ping_agent_data SET last_check = %s WHERE id = %s"""
        cursor.execute(update_query, (TIME, id))
        connection.commit()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")
    finally:
        cursor.close()
        connection.close()


# ╔═══════════════╗
# ║ MAIN PROGRAM  ║
# ╚═══════════════╝

if __name__ == "__main__":
    host_list = fetch_ping_agent_data()

    for host in host_list:
        print(f"[INFO] PINGING {host["ip"]}")
        ##TESTING##
        # notifier.notifier(host, "ping_agent_down", details={"cause": "Test"})
        ##TESTING##
        new_state = ping_host(host)
        check_state(host, new_state)  # Actualizador
        update_last_check(host["id"])
        check_threshold(host, new_state["response"])
