from transports.transport_email.templates.ping_agent_templates import (
    ping_agent_up_template,
    ping_agent_down_template,
    ping_agent_latency_threshold_exceeded,
    ping_agent_latency_threshold_restored,
)


def email_message_manager(data, message_type, details=None):
    """Genera el body del correo según el tipo."""
    id = str(data.get("id"))
    name = data.get("name")
    ip = data.get("ip")
    last_down = data.get("last_down")
    threshold = data.get("threshold")
    owner = data.get("owner")
    cause = details.get("cause") if details else None
    latency = details.get("latency") if details else None

    # Diccionario que mapea tipos de mensajes a plantillas y parámetros
    body_generator = {
        "ping_agent_up": lambda: ping_agent_up_template(owner, id, name, ip, last_down),
        "ping_agent_down": lambda: ping_agent_down_template(owner, id, name, ip, cause),
        "ping_agent_latency_threshold_exceeded": lambda: ping_agent_latency_threshold_exceeded(
            owner, id, name, ip, latency, threshold
        ),
        "ping_agent_latency_threshold_restored": lambda: ping_agent_latency_threshold_restored(
            owner, id, name, ip, latency, threshold
        ),
    }

    subject_generator = {
        "ping_agent_up": "✅ Ping agent " + name + " UP",
        "ping_agent_down": "❌ Ping agent " + name + " DOWN",
        "ping_agent_latency_threshold_exceeded": "⚠️ "+ name + " Threshold Exceeded",
        "ping_agent_latency_threshold_restored": "✅ "+ name + " Threshold Restored",
    }

    # Obtener la plantilla correspondiente al tipo de mensaje
    body_func = body_generator.get(message_type)
    subject = subject_generator.get(message_type)

    # Verificar si se ha encontrado una plantilla y devolver el diccionario
    if body_func and subject:
        return {
            "body": body_func(),  # Llamar la función para generar el cuerpo
            "subject": subject,
        }
    else:
        print("Error: Unknown message type")
        return None
