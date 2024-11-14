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
        SET data = %s
        WHERE id = %s
        """
        # Directly update the whole data field
        cursor.execute(update_query, (json.dumps(host), host["id"]))
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
        SET data = %s
        WHERE id = %s
        """
        # Directly update the whole data field
        cursor.execute(update_query, (json.dumps(host), host["id"]))
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

        host["last_check"] = int(time.time())
        # Convert host_data back to JSON and update the database
        update_query = """
        UPDATE host_data 
        SET data = %s
        WHERE id = %s
        """
        # Directly update the whole data field
        cursor.execute(update_query, (json.dumps(host), host["id"]))
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

        host["last_up"] = int(time.time())
        # Convert host_data back to JSON and update the database
        update_query = """
        UPDATE host_data 
        SET data = %s
        WHERE id = %s
        """
        # Directly update the whole data field
        cursor.execute(update_query, (json.dumps(host), host["id"]))
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

        host["last_down"] = int(time.time())
        # Convert host_data back to JSON and update the database
        update_query = """
        UPDATE host_data 
        SET data = %s
        WHERE id = %s
        """
        # Directly update the whole data field
        cursor.execute(update_query, (json.dumps(host), host["id"]))
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

        host["state"] = new_state

        # Convert host_data back to JSON and update the database
        update_query = """
        UPDATE host_data 
        SET data = %s
        WHERE id = %s
        """
        # Directly update the whole data field
        cursor.execute(update_query, (json.dumps(host), host["id"]))
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

if __name__ == "__main__":
    while True:
        print("[INFO] START RUNNING")
        try:
            # Conectar a la base de datos
            connection = mysql.connector.connect(**db_config)
            cursor = connection.cursor()

            # Consultar los hosts
            select_query = "SELECT data FROM host_data"
            cursor.execute(select_query)

            # Obtener los resultados
            hosts = cursor.fetchall()
            host_list = [json.loads(host[0]) for host in hosts]  # Decodificar JSON

            # Cerrar la conexión
            cursor.close()
            connection.close()
        except Error as e:
            print(f"ERROR: {e}")

        for host in host_list:
            print("[INFO] Check host ", host["ip"])
            new_state = ping_host(host)  # Actualiza la latencia del host
            check_state(host, new_state)  # Actualiza la de disponibilidad del host
            update_last_check(host)
            # first check
            if host["last_up"] is None and new_state["state"]:
                update_last_up(host)
                print("first check")

        time.sleep(60)
