from transports.transport_telegram.templates.ping_agent_templates import (
    ping_agent_up_template,
    ping_agent_down_template,
    ping_agent_latency_threshold_exceeded,
    ping_agent_latency_threshold_restored,
)

from transports.transport_telegram.templates.web_agent_templates import (
    web_agent_up_template,
    web_agent_down_template,
    web_agent_threshold_exceeded_template,
    web_agent_threshold_restored,
)


# ╔═════════════╗
# ║ LOG CONFIG  ║
# ╚═════════════╝

import log.log_config

# Configurar el logger para este script
logger = log.log_config.setup_logger("telegram_message_manager")


def telegram_message_manager(data, message_type, details=None):
    """Genera el mensaje de Telegram según el tipo."""
    ##ALL
    id = str(data.get("id"))
    alias = data.get("alias")
    owner = data.get("owner")
    last_down = data.get("last_down")
    cause = details.get("cause") if details else None
    
    ##PING AGENT
    ip = data.get("ip", None)
    latency_threshold = data.get("threshold", None)
    latency = details.get("latency") if details else None
    
    ##WEB AGENT
    url = data.get("url", None)
    status_code = details.get("status_code", None) if details else None
    value = details.get("value", None) if details else None
    threshold_type = details.get("threshold_type", None) if details else None
    web_threshold = details.get("threshold_set", None)

    
    
    # Diccionario que mapea tipos de mensajes a plantillas y parámetros
    template_mapping = {
        ##PING AGENT
        "ping_agent_up": lambda: ping_agent_up_template(owner, id, alias, ip, last_down),
        "ping_agent_down": lambda: ping_agent_down_template(owner, id, alias, ip, cause),
        "ping_agent_latency_threshold_exceeded": lambda: ping_agent_latency_threshold_exceeded(owner, id, alias, ip, latency, latency_threshold),
        "ping_agent_latency_threshold_restored": lambda: ping_agent_latency_threshold_restored(owner, id, alias, ip, latency, latency_threshold),
        ##WEB AGENT
        "web_agent_up": lambda: web_agent_up_template(owner, id, url, alias, last_down, status_code),
        "web_agent_down": lambda: web_agent_down_template(owner, id, url, alias, status_code),
        "web_agent_threshold_exceeded_template": lambda: web_agent_threshold_exceeded_template(owner, id, url, alias , value, web_threshold, threshold_type),
        "web_agent_threshold_restored": lambda: web_agent_threshold_restored(owner, id, url, alias , value, web_threshold, threshold_type),
    }

    # Obtener la plantilla correspondiente al tipo de mensaje
    template_func = template_mapping.get(message_type)

    if template_func:
        return template_func()
    else:
        #print("Error: Unknown message type")
        logger.warning("Unknown message type")
        return None
