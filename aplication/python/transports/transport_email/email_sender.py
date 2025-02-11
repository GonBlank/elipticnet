import smtplib
import json
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

CONFIG_PATH = "/var/www/elipticnet/aplication/config/config.json"
with open(CONFIG_PATH, "r") as file:
    config = json.load(file)

# Configuración SMTP
smtp_host = config["email"]["alerts"]["SMTP_HOST"]
smtp_port = config["email"]["alerts"]["SMTP_PORT"]
smtp_user = config["email"]["alerts"]["SMTP_USER"]
smtp_password = config["email"]["alerts"]["SMTP_PASS"]

# ╔═════════════╗
# ║ LOG CONFIG  ║
# ╚═════════════╝

import log.log_config

# Configurar el logger para este script
logger = log.log_config.setup_logger("email_sender")


# Función para enviar correos
def send_email(to, subject, body):
    try:
        # Crear el mensaje
        msg = MIMEMultipart()

        # Aquí configuramos el nombre del remitente y la dirección de correo
        msg["From"] = (
            "Elipticnet Alert <" + smtp_user + ">"
        )  # Modificado para incluir el nombre

        msg["To"] = to
        msg["Subject"] = subject

        # Agregar el cuerpo del mensaje en formato HTML
        msg.attach(MIMEText(body, "html"))

        # Conectar al servidor SMTP y enviar el correo
        with smtplib.SMTP_SSL(smtp_host, smtp_port) as server:
            server.login(smtp_user, smtp_password)
            server.sendmail(smtp_user, to, msg.as_string())

        # print(f"Correo enviado a {to} con asunto: {subject}")
        logger.info(f"Correo enviado a {to} con asunto: {subject}")
    except Exception as e:
        # print(f"Error enviando el correo a {to}: {e}")
        logger.error(f"Error enviando el correo a {to}: {e}")
