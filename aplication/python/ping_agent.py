import mysql.connector
import json
from ping3 import ping
from datetime import datetime, timezone


#╔═════════════════╗
#║ CONFIGURATIONS  ║
#╚═════════════════╝


with open("../config/config.json", "r") as file:
    config = json.load(file)

db_config = {
    "host": config["database"]["DB_HOST"],
    "user": config["database"]["DB_USER"],
    "password": config["database"]["DB_PASS"],
    "database": config["database"]["DB_NAME"],
    "ssl_disabled": True,
}

time = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S")


#╔════════════╗
#║ FUNCTIONS  ║
#╚════════════╝


def fetch_host_data():
    connection = None
    cursor = None
    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor(dictionary=True)

        query = """
        SELECT id, ip, state, threshold, last_check, log, transports
        FROM host_data;
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
    update_host_latency(host["id"], ping_response)  # round(ping_response, 2)

    if ping_response != None and ping_response != False or type(ping_response) == float:
        print("[INFO] HOST UP")
        return {"state": True, "response": ping_response}
    else:
        print("[INFO] HOST DOWN")
        return {"state": False, "response": ping_response}

def update_host_latency(host_id, latency):
    print("[INFO] UPDATING HOST LATENCY")

    # Verificar si latency no es None
    if latency is not None:
        latency = round(float(latency), 2)  # Convertir a float si no es None

    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()

        # Usar %s para todos los tipos de datos en la consulta
        update_query = """INSERT INTO latency (host_id, latency) VALUES (%s, %s)"""

        cursor.execute(update_query, (host_id, latency))
        connection.commit()

        print("[INFO] Host latency updated successfully.")

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")

    finally:
        cursor.close()
        connection.close()

def check_state(host, new_state):
    if host["state"] == True and new_state["state"] == False:
        print("[INFO] DETECT HOST DOWN")
        update_state(host, new_state["state"])
        update_host_log(host, "down", "Host down", log_message(new_state["response"]))
        update_last_up_down(False, host['id'])
        #send_alert(host['transports'])
    elif host["state"] == False and new_state["state"] == True:
        print("[INFO] DETECT HOST UP")
        update_state(host, new_state["state"])
        update_host_log(host, "up", "Recovered host")
        update_last_up_down(True, host['id'])
        #send_alert(host['transports'])
        
    elif host["state"] == None:
        print("[INFO] FIRST CHECK")
        if new_state["state"] == True:
            update_state(host, new_state["state"])
            update_last_up_down(True, host['id'])
        else:
            update_state(host, new_state["state"])
            update_last_up_down(False, host['id'])
        

def update_state(host, new_state):
    print("[INFO] UPDATING STATE")
    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()

        # Consulta SQL para actualizar el campo 'state'
        update_query = """
        UPDATE host_data SET state = %s WHERE id = %s """
        cursor.execute(update_query, (new_state, host["id"]))
        connection.commit()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")

    finally:
        cursor.close()
        connection.close()

def update_host_log(host, icon, cause, message=None):
    print("[INFO] UPDATING HOST LOG")

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
            "time": time,
            "icon": icon,
            "cause": cause,
            "message": message,
        }
    )

    try:
        # Conectar a la base de datos
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()

        # Actualizar el campo log en la base de datos
        update_query = """
        UPDATE host_data 
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
    print("[INFO] UPDATING LAST UP/DOWN")

    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()

        if state:
            update_query = """UPDATE host_data SET last_up = %s WHERE id = %s"""
        else:
            update_query = """UPDATE host_data SET last_down = %s WHERE id = %s"""

        cursor.execute(update_query, (time, id))
        connection.commit()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")
    finally:
        cursor.close()
        connection.close()

def update_last_check(id):
    print("[INFO] UPDATING LAST CHECK")

    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()
        update_query = """UPDATE host_data SET last_check = %s WHERE id = %s"""
        cursor.execute(update_query, (time, id))
        connection.commit()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")
    finally:
        cursor.close()
        connection.close()



#╔═══════════════╗
#║ MAIN PROGRAM  ║
#╚═══════════════╝

if __name__ == "__main__":
    host_list = fetch_host_data()

    for host in host_list:
        new_state = ping_host(host)
        check_state(host, new_state)  # Actualizador
        update_last_check(host['id'])

