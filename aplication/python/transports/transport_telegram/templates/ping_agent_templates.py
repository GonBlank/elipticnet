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

DOMAIN = config["app"]["DOMAIN"]

#TIME = datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M:%S")
TIME = datetime.now(timezone.utc)

# ╔════════════════════╗
# ║ Validate transport ║
# ╚════════════════════╝


def validate_transport(hash):
    validation_url = (
        DOMAIN + "/aplication/public/transport_validator.html?validation_hash=" + hash
    )

    # Generar el mensaje
    return f"""
🚀 *Transport Validation Required*

To activate and use this transport for receiving alerts, please confirm the validation process.

🔗 [Click here to validate your transport]({validation_url})

Once validated, you will be able to receive real-time notifications for any critical updates.

Thank you for ensuring the proper configuration!

---
For any questions, feel free to [contact us]({DOMAIN}).

"""


# ╔════╗
# ║ UP ║
# ╚════╝

def ping_agent_up_template(owner, id, name, ip, last_down):
    # Convertir last_down a datetime si es una cadena
    if isinstance(last_down, str):
        last_down = datetime.strptime(last_down, "%Y-%m-%d %H:%M:%S")

    # Asegurarse de que last_down tenga zona horaria UTC
    if last_down.tzinfo is None:
        last_down = pytz.utc.localize(last_down)  # Localizar a UTC si no tiene zona horaria

    # Calcular la diferencia entre last_down y TIME
    time_diff = TIME - last_down

    # Formatear la duración utilizando la función duration_format
    duration = duration_format(time_diff)

    # Ahora puedes agregar la duración al mensaje
    return f"""
✅ *PING AGENT {name} - UP*

🔹 *IP*: {ip}
🔹 *Incident started at*: {time_translate(last_down, owner)}
🔹 *Resolved at*: {time_translate(TIME, owner)}
🔹 *Duration*: {duration}

💡 The agent's connectivity has been restored. If you need further details, [go to agent]({DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id})
"""



# ╔══════╗
# ║ DOWN ║
# ╚══════╝


def ping_agent_down_template(owner,id, name, ip, cause):
    # Generar el mensaje
    return f"""
❌ *PING AGENT {name} - DOWN*

🔹 *IP*: {ip}
🔹 *Root cause*: {cause}
🔹 *Incident started at*: {time_translate(TIME, owner)}

💡 The agent is currently down. If you need further details, [go to agent]({DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id})
"""


# ╔════════════════════╗
# ║ Threshold exceeded ║
# ╚════════════════════╝


def ping_agent_latency_threshold_exceeded(owner, id, name, ip, latency, threshold):
    # Generar el mensaje
    return f"""
⚠️ *PING AGENT {name} - LATENCY THRESHOLD EXCEEDED*

🔹 *IP*: {ip}
🔹 *Latency*: {latency} ms
🔹 *Threshold*: {threshold} ms
🔹 *Incident started at*: {time_translate(TIME, owner)}

💡 The latency has exceeded the set threshold. If you need further details, [go to agent]({DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id})
"""


# ╔════════════════════╗
# ║ Threshold restored ║
# ╚════════════════════╝


def ping_agent_latency_threshold_restored(owner, id, name, ip, latency, threshold):
    # Generar el mensaje
    return f"""
✅ *PING AGENT {name} - LATENCY THRESHOLD RESTORED*

🔹 *IP*: {ip}
🔹 *Latency*: {latency} ms
🔹 *Threshold*: {threshold} ms
🔹 *Resolved at*: {time_translate(TIME, owner)}

💡 The latency has returned to normal and is now within the set threshold. If you need further details, [go to agent]({DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id})
"""
