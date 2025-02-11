import json
from datetime import datetime, timezone
import pytz

from global_functions.time_translate import time_translate
from global_functions.duration_format import duration_format


# ╔═══════════════╗
# ║ GLOBAL CONFIG ║
# ╚═══════════════╝

with open("/var/www/elipticnet/aplication/config/config.json", "r") as file:
    config = json.load(file)

APP_LINK = config["app"]["LINK"]

# TIME = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S")
TIME = datetime.now(timezone.utc)


# ╔════╗
# ║ UP ║
# ╚════╝


def web_agent_up_template(owner, id, url, alias, last_down, status_code):
    alias = alias or "-"
    # Convertir last_down a datetime si es una cadena
    if isinstance(last_down, str):
        last_down = datetime.strptime(last_down, "%Y-%m-%d %H:%M:%S")

    # Asegurarse de que last_down tenga zona horaria UTC
    if last_down.tzinfo is None:
        last_down = pytz.utc.localize(
            last_down
        )  # Localizar a UTC si no tiene zona horaria

    # Calcular la diferencia entre last_down y TIME
    time_diff = TIME - last_down

    # Formatear la duración utilizando la función duration_format
    duration = duration_format(time_diff)

    # Ahora puedes agregar la duración al mensaje
    return f"""
✅ *WEB AGENT - UP*

🔹 *URL*: {url}
🔹 *ALIAS*: {alias}
🔹 *Status code*: {status_code}
🔹 *Incident started at*: {time_translate(last_down, owner)}
🔹 *Resolved at*: {time_translate(TIME, owner)}
🔹 *Duration*: {duration}

💡 The agent's connectivity has been restored. If you need further details, [go to agent]({APP_LINK + "/aplication/public/web_agent_view.php?id=" + id})
"""


# ╔══════╗
# ║ DOWN ║
# ╚══════╝


def web_agent_down_template(owner, id, url, alias, status_code):
    alias = alias or "-"
    return f"""
❌ *WEB AGENT - DOWN*

🔹 *URL*: {url}
🔹 *Alias*: {alias}
🔹 *Status code*: {status_code}
🔹 *Incident started at*: {time_translate(TIME, owner)}

💡 The agent is currently down. If you need further details, [go to agent]({APP_LINK + "/aplication/public/web_agent_view.php?id=" + id})
"""


def web_agent_threshold_exceeded_template(owner, id, url, alias , value, threshold, threshold_type):
    
    alias = alias or "-"
    # Generar el mensaje
    return f"""
⚠️ *WEB AGENT  - {threshold_type} THRESHOLD EXCEEDED*

🔹 *URL*: {url}
🔹 *Alias*: {alias}
🔹 *{threshold_type} value*: {value} ms
🔹 *Threshold*: {threshold} ms
🔹 *Incident started at*: {time_translate(TIME, owner)}

💡 The {threshold_type} has exceeded the set threshold. If you need further details, [go to agent]({APP_LINK + "/aplication/public/web_agent_view.php?id=" + id})
"""



def web_agent_threshold_restored(owner, id, url, alias , value, threshold, threshold_type):
    alias = alias or "-"
    # Generar el mensaje
    return f"""
✅ *WEB AGENT  - {threshold_type} THRESHOLD RESTORED*

🔹 *URL*: {url}
🔹 *Alias*: {alias}
🔹 *{threshold_type} value*: {value} ms
🔹 *Threshold*: {threshold} ms
🔹 *Resolved at*: {time_translate(TIME, owner)}

💡 The {threshold_type} has returned to normal and is now within the set threshold. If you need further details, [go to agent]({APP_LINK + "/aplication/public/ping_agent_view.php?id=" + id})
"""