import requests
import time
from bs4 import BeautifulSoup
import mysql.connector
from datetime import datetime, timezone
import json

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
        total_time = round(time.time() - start_time, 3)

        # Asignar valores obtenidos
        result["status_code"] = response.status_code
        result["response_time"] = total_time
        result["ttfb"] = round(response.elapsed.total_seconds(), 3)
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


def update_web_agent_data(data):
    connection = None
    cursor = None
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
                data["status_code"],
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


if __name__ == "__main__":
    web_list = fetch_web_agent_data()

    for web in web_list:
        data = monitor_web(web["url"], web["request_timeout"])
        data["id"] = web["id"]
        update_web_agent_data(data)
        insert_web_agent_record(data)
        print(data)
