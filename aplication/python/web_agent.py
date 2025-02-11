import concurrent.futures
import requests
import http
import time
from bs4 import BeautifulSoup
import mysql.connector
from datetime import datetime, timezone
import json
import traceback  ##Para mejorar el debug de errores

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

# notifier.notifier(data, "web_agent_up", details={"latency": ping_response})

# ╔══════════════════╗
# ║ NOTIFIER CONFIG  ║
# ╚══════════════════╝
from transports.notifier import NotificationManager

notifier = NotificationManager(CONFIG_PATH, DB_CONFIG)


def fetch_web_agent_data():
    connection = None
    cursor = None
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor(dictionary=True)

        query = """
        SELECT * FROM web_agent_data;
        """
        cursor.execute(query)
        results = cursor.fetchall()
        return results

    except mysql.connector.Error as err:
        # print(f"Error: {err}")
        # logger.error(f"Error: {err}")
        return None
    finally:
        if cursor is not None:
            cursor.close()
        if connection is not None and connection.is_connected():
            connection.close()


def monitor_web(url, timeout=10):
    result = {
        "id": None,
        "url": url,
        "status_code": None,
        "response_time": None,
        "ttfb": None,
        "state": None,
        "redirects": [],
        "content_length": None,
        "content_type": None,
        "content_encoding": None,
        "server": None,
        "page_title": None,
    }

    headers = {"User-Agent": "Mozilla/5.0 (compatible; WebMonitorBot/1.0)"}

    try:
        start_time = time.time()
        response = requests.get(url, headers=headers, timeout=timeout)
        # total_time = round(time.time() - start_time, 3) [segundos]
        total_time = round((time.time() - start_time) * 1000, 2)  # [milisegundos]

        # Asignar valores obtenidos
        result["status_code"] = response.status_code
        result["response_time"] = total_time
        # result["ttfb"] = round(response.elapsed.total_seconds(), 3)[segundos]
        result["ttfb"] = round(
            response.elapsed.total_seconds() * 1000, 2
        )  # [milisegundos]
        result["state"] = response.status_code < 400
        result["redirects"] = [resp.url for resp in response.history]
        result["content_length"] = len(response.content) if response.content else 0
        result["content_type"] = response.headers.get("Content-Type", "N/A")
        result["content_encoding"] = response.headers.get("Content-Encoding", "N/A")
        result["server"] = response.headers.get("Server", "N/A")

        # Extraer título si es HTML
        if "text/html" in result["content_type"]:
            soup = BeautifulSoup(response.text, "html.parser")
            title_tag = soup.find("title")
            result["page_title"] = title_tag.text.strip() if title_tag else "N/A"

    except requests.exceptions.RequestException as e:
        result["error"] = str(e)
        result["state"] = False  # Si hay error, el sitio está caído

    return result


# Ejemplo de uso
# url = "https://elipticnet.com"
# timeout = 10
# data = monitor_web(url, timeout)
# print(data)


def get_http_status_description(code):
    """
    Returns the description of an HTTP status code.
    """
    return (
        f"{code} {http.HTTPStatus(code).phrase}"
        if code in http.HTTPStatus._value2member_map_
        else f"{code} Unknown code"
    )



def update_web_agent_data(data):
    connection = None
    cursor = None
    status_code_description = get_http_status_description(data["status_code"])
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()

        query = """
        UPDATE web_agent_data
        SET status_code = %s,
            response_time = %s,
            ttfb = %s,
            state = %s,
            redirects = %s,
            content_length = %s,
            content_type = %s,
            content_encoding = %s,
            server = %s,
            page_title = %s,
            last_check = %s
        WHERE id = %s;
        """
        cursor.execute(
            query,
            (
                status_code_description,
                data["response_time"],
                data["ttfb"],
                data["state"],
                json.dumps(data["redirects"]),
                data["content_length"],
                data["content_type"],
                data["content_encoding"],
                data["server"],
                data["page_title"],
                TIME,
                data["id"],
            ),
        )
        connection.commit()

    except mysql.connector.Error as err:
        # print(f"Error: {err}")
        # logger.error(f"Error: {err}")
        return None
    finally:
        if cursor is not None:
            cursor.close()
        if connection is not None and connection.is_connected():
            connection.close()


def insert_web_agent_record(data):
    connection = None
    cursor = None
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()

        query = """
        INSERT INTO web_agent_record (web_id, status_code, response_time, ttfb) 
        VALUES (%s, %s, %s, %s);
        """
        cursor.execute(
            query,
            (
                data["id"],
                data["status_code"],
                data["response_time"],
                data["ttfb"],
            ),
        )
        connection.commit()

    except mysql.connector.Error as err:
        # print(f"Error: {err}")
        # logger.error(f"Error: {err}")
        return None
    finally:
        if cursor:
            cursor.close()
        if connection and connection.is_connected():
            connection.close()


def detect_web_state(web, data):
    """
    Detecta cambios en el estado de la web.
    """
    if data["state"] == False and web["state"] == True:
        notifier.notifier(web, "web_agent_down", details={"status_code": get_http_status_description(data["status_code"])})
        update_last_up_down(False, web["id"])
        print("[INFO] web agent DOWN")
    elif data["state"] == True and web["state"] == False:
        notifier.notifier(web, "web_agent_up", details={"status_code": get_http_status_description(data["status_code"])})
        update_last_up_down(True, web["id"])
        print("[INFO] web agent RESTORED")
    elif web["state"] == None:
        print("[INFO] FIRST CHECK")
        #logger.info("[INFO] FIRST CHECK")
        if data["state"] == True:
            update_last_up_down(True, web["id"])
        else:
            update_last_up_down(False, web["id"])
    return


def threshold_exceeded(threshold_set, threshold_real, threshold_exceeded):
    """
    Determina si el umbral ha sido excedido o restaurado.
    Retorna True si se excedió, False si se restauró, y None si no hubo cambios.
    """
    if threshold_real > threshold_set and not threshold_exceeded:
        return True
    elif threshold_real < threshold_set and threshold_exceeded:
        return False
    return None


def update_check_threshold(id, threshold_state, threshold_type):
    """
    Actualiza el estado del umbral en la base de datos.
    """
    try:
        with mysql.connector.connect(**DB_CONFIG) as connection:
            with connection.cursor() as cursor:
                update_query = (
                    f"UPDATE web_agent_data SET {threshold_type} = %s WHERE id = %s"
                )
                cursor.execute(update_query, (threshold_state, id))
                connection.commit()
    except mysql.connector.Error as e:
        print(f"ERROR: {e}")


def handle_threshold(web, data):
    if web["response_time_threshold"]:
        if threshold_exceeded(web["response_time_threshold"],data["response_time"], web["response_time_threshold_exceeded"]):
            notifier.notifier(web, "web_agent_threshold_exceeded_template", details={"value": data["response_time"], "threshold_type": "Response time", "threshold_set": web["response_time_threshold"]})
            update_check_threshold(web["id"], True, "response_time_threshold_exceeded")
            print("Humbral de tiempo de respuesta excedido")
        elif (threshold_exceeded(web["response_time_threshold"], data["response_time"], web["response_time_threshold_exceeded"]) == False):
            notifier.notifier(web, "web_agent_threshold_restored", details={"value": data["response_time"], "threshold_type": "Response time", "threshold_set": web["response_time_threshold"]})
            update_check_threshold(web["id"], False, "response_time_threshold_exceeded")
            print("Umbral de tiempo de respuesta restaurado")
    if web["ttfb_threshold"]:
        if threshold_exceeded(web["ttfb_threshold"], data["ttfb"], web["ttfb_threshold_exceeded"]):
            update_check_threshold(web["id"], True, "ttfb_threshold_exceeded")
            notifier.notifier(web, "web_agent_threshold_exceeded_template", details={"value": data["ttfb"], "threshold_type": "TTFB", "threshold_set": web["ttfb_threshold"]})
            print("Humbral de tiempo de TTFB excedido")
        elif (threshold_exceeded(web["ttfb_threshold"], data["ttfb"], web["ttfb_threshold_exceeded"]) == False):
            notifier.notifier(web, "web_agent_threshold_restored", details={"value": data["ttfb"], "threshold_type": "TTFB", "threshold_set": web["ttfb_threshold"]})
            update_check_threshold(web["id"], False, "ttfb_threshold_exceeded")
            print("Umbral de TTFB restaurado")

def update_last_up_down(state, id):
    # print("[INFO] UPDATING LAST UP/DOWN")

    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        cursor = connection.cursor()

        if state:
            update_query = """UPDATE web_agent_data SET last_up = %s WHERE id = %s"""
        else:
            update_query = """UPDATE web_agent_data SET last_down = %s WHERE id = %s"""

        cursor.execute(update_query, (TIME, id))
        connection.commit()

    except mysql.connector.Error as e:
        print(f"ERROR: {e}")
        # logger.warning(f"Error: {e}")
    finally:
        cursor.close()
        connection.close()


if __name__ == "__main__":
    web_list = fetch_web_agent_data()
    print("STARTING")
    with concurrent.futures.ThreadPoolExecutor(max_workers=10) as executor:
        future_to_web = {
            executor.submit(monitor_web, web["url"], web["request_timeout"]): web
            for web in web_list
        }

        for future in concurrent.futures.as_completed(future_to_web):
            web = future_to_web[future]
            try:
                data = future.result()
                data["id"] = web["id"]
                update_web_agent_data(data)
                insert_web_agent_record(data)
                detect_web_state(web, data)
                # notifier.notifier(web, "web_agent_up")
                if data["state"]:
                    handle_threshold(web, data)
            except Exception as e:
                # Agregar más información sobre el error
                print(f"Error monitoring {web['url']}: {e}")
                print("Exception type:", type(e))
                print("Traceback:")
                traceback.print_exc()  # Muestra el traceback completo
