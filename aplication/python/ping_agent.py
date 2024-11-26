import ipaddress
from ping3 import ping
import time
import json
import mysql.connector
from mysql.connector import Error

##Configurations
with open("../config/config.json", "r") as file:
    config = json.load(file)

db_config = {
    "host": config["database"]["DB_HOST"],
    "user": config["database"]["DB_USER"],
    "password": config["database"]["DB_PASS"],
    "database": config["database"]["DB_NAME"],
}


##FUNCTIONS
def first_check():
    actual_time = int(time.time())


def ping_host(host):
    actual_time = int(time.time())
    ping_response = ping(host["ip"], timeout=2, unit="ms")
    if ping_response != None and ping_response != False or type(ping_response) == float:
        # valido type porque `0.0` returned means the delay is lower than the precision of `time.time()`.
        update_host_latency(
            host, {"time": actual_time, "value": round(ping_response, 2)}
        )
        # update_host_log(host, True, ping_response)
        print("[INFO] HOST UP")

        return {"state": True, "response": ping_response}
    else:
        update_host_latency(
            host, {"time": actual_time, "value": None}
        )  # pudo implementar un -1 y despues dibujar los puntos en rojo. O un null
        # update_host_log(host, False, ping_response)
        print("[INFO] HOST DOWN")
        return {"state": False, "response": ping_response}


def update_host_latency(host, latency_result):
    print("[INFO] UPDATING HOST LATENCY")
    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()

        host["latency"].append(latency_result)

        # Convert host_data back to JSON and update the database
        update_query = """
        UPDATE host_data 
        SET latency = %s
        WHERE id = %s
        """
        # Directly update the whole data field
        cursor.execute(update_query, (json.dumps(host["latency"]), host["id"]))
        connection.commit()

        cursor.close()
        connection.close()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")


def update_host_log(host, state, ping_response):
    print("[INFO] UPDATING HOST LOG")

    if ping_response == False and type(ping_response) != float:
        cause = "Host unknown (cannot resolve)"
    elif ping_response == None:
        cause = "Timed out (no reply)"
    elif ping_response or type(ping_response) == float:
        cause = "Recovery"
    else:
        cause = "Unknow"

    if not state:
        host["log"].append(
            {
                "start_time": int(time.time()),
                "end_time": None,
                "state": state,
                "cause": cause,
            }
        )
    else:
        ultimo_registro = host["log"][-1]
        ultimo_registro["end_time"] = int(time.time())
        ultimo_registro["state"] = True

    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()
        # Convert host_data back to JSON and update the database
        update_query = """
        UPDATE host_data 
        SET log = %s
        WHERE id = %s
        """
        # Directly update the whole data field
        cursor.execute(update_query, (json.dumps(host["log"]), host["id"]))
        connection.commit()

        cursor.close()
        connection.close()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")


def update_last_check(host):
    print("[INFO] UPDATING LAST CHECK")
    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()

        host["last_check"] = int(time.time())  # Actualizar el campo last_check

        # Consulta SQL para actualizar el campo 'last_check'
        update_query = """
        UPDATE host_data
        SET last_check = %s
        WHERE id = %s
        """
        cursor.execute(update_query, (host["last_check"], host["id"]))
        connection.commit()

        cursor.close()
        connection.close()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")


def update_last_up(host):
    print("[INFO] UPDATING LAST UP")
    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()

        host["last_up"] = int(time.time())  # Actualizar el campo last_up

        # Consulta SQL para actualizar el campo 'last_up'
        update_query = """
        UPDATE host_data
        SET last_up = %s
        WHERE id = %s
        """
        cursor.execute(update_query, (host["last_up"], host["id"]))
        connection.commit()

        cursor.close()
        connection.close()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")


def update_last_down(host):
    print("[INFO] UPDATING LAST DOWN")
    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()

        host["last_down"] = int(time.time())  # Actualizar el campo last_down

        # Consulta SQL para actualizar el campo 'last_down'
        update_query = """
        UPDATE host_data
        SET last_down = %s
        WHERE id = %s
        """
        cursor.execute(update_query, (host["last_down"], host["id"]))
        connection.commit()

        cursor.close()
        connection.close()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")


def update_state(host, new_state):
    print("[INFO] UPDATING STATE")
    try:
        connection = mysql.connector.connect(**db_config)
        cursor = connection.cursor()

        host["state"] = new_state  # Actualizamos el campo 'state'

        # Consulta SQL para actualizar el campo 'state'
        update_query = """
        UPDATE host_data
        SET state = %s
        WHERE id = %s
        """
        cursor.execute(update_query, (host["state"], host["id"]))
        connection.commit()

        cursor.close()
        connection.close()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")


def check_state(host, new_state):
    if host["state"] == True and new_state["state"] == False:
        print("[INFO] DETECT HOST DOWN")
        update_last_down(host)
        update_host_log(host, new_state["state"], new_state["response"])
        update_state(host, new_state["state"])
    elif host["state"] == False and new_state["state"] == True:
        print("[INFO] DETECT HOST UP")
        update_last_up(host)
        update_host_log(host, new_state["state"], new_state["response"])
        update_state(host, new_state["state"])


##MAIN PROGRAM

# PROGRAMA PRINCIPAL
if __name__ == "__main__":
    while True:
        print("[INFO] START RUNNING")
        try:
            # Conectar a la base de datos
            connection = mysql.connector.connect(**db_config)
            cursor = connection.cursor()

            # Consultar todos los hosts con los campos id, ip, name, state, transports, latency
            select_query = (
                "SELECT id, ip, name, state,last_up, transports, log, latency FROM host_data"
            )
            cursor.execute(select_query)

            # Obtener los resultados y convertir cada fila en un diccionario con claves correspondientes
            column_names = [
                desc[0] for desc in cursor.description
            ]  # Obtener los nombres de las columnas
            hosts = cursor.fetchall()

            host_list = []
            for host in hosts:
                # Crear un diccionario para cada host
                host_dict = dict(zip(column_names, host))

                # Decodificar el valor de transports si existe
                host_dict["transports"] = (
                    json.loads(host_dict["transports"])
                    if host_dict["transports"]
                    else {}
                )

                # Decodificar el valor de latency como array si existe
                host_dict["latency"] = (
                    json.loads(host_dict["latency"]) if host_dict["latency"] else []
                )
                
                                # Decodificar el valor de latency como array si existe
                host_dict["log"] = (
                    json.loads(host_dict["log"]) if host_dict["log"] else []
                )

                host_list.append(host_dict)

            # Cerrar la conexión
            cursor.close()
            connection.close()
        except Error as e:
            print(f"ERROR: {e}")

        # Procesar cada host
        for host in host_list:
            print(f"[INFO] Checking host {host['ip']}")
            new_state = ping_host(host)  # Actualiza la latencia del host
            check_state(host, new_state)  # Actualiza el estado de disponibilidad
            update_last_check(host)

            # Primer chequeo: si no ha sido marcado como "last_up", lo actualizamos
            if host["last_up"] is None and new_state["state"]:
               update_last_up(host)
               print("First check, updating last_up")

        # Pausa de 60 segundos entre ciclos
        time.sleep(60)
