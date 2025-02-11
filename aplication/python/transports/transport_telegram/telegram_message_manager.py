from transports.transport_telegram.templates.ping_agent_templates import (
    ping_agent_up_template,
    ping_agent_down_template,
    ping_agent_latency_threshold_exceeded,
    ping_agent_latency_threshold_restored,
)


# ╔═════════════╗
# ║ LOG CONFIG  ║
# ╚═════════════╝

import log.log_config

# Configurar el logger para este script
logger = log.log_config.setup_logger("telegram_message_manager")


def telegram_message_manager(data, message_type, details=None):
    """Genera el mensaje de Telegram según el tipo."""
    id = str(data.get("id"))
    name = data.get("alias")
    ip = data.get("ip")
    last_down = data.get("last_down")
    threshold = data.get("threshold")
    owner = data.get("owner")
    cause = details.get("cause") if details else None
    latency = details.get("latency") if details else None

    # Diccionario que mapea tipos de mensajes a plantillas y parámetros
    template_mapping = {
        "ping_agent_up": lambda: ping_agent_up_template(owner, id, name, ip, last_down),
        "ping_agent_down": lambda: ping_agent_down_template(owner, id, name, ip, cause),
        "ping_agent_latency_threshold_exceeded": lambda: ping_agent_latency_threshold_exceeded(owner, id, name, ip, latency, threshold),
        "ping_agent_latency_threshold_restored": lambda: ping_agent_latency_threshold_restored(owner, id, name, ip, latency, threshold),
    }

    # Obtener la plantilla correspondiente al tipo de mensaje
    template_func = template_mapping.get(message_type)

    if template_func:
        return template_func()
    else:
        #print("Error: Unknown message type")
        logger.warning("Unknown message type")
        return None
