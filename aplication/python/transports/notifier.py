import mysql.connector
import json
from transports.transport_telegram.telegram_bot import TelegramBot
from transports.transport_email.email_sender import send_email
from transports.transport_telegram.telegram_message_manager import (
    telegram_message_manager,
)
from transports.transport_email.email_message_manager import (
    email_message_manager,
)


class NotificationManager:
    def __init__(self, config_path, db_config):
        self.config_path = config_path
        self.db_config = db_config
        self.telegram_bot = TelegramBot(config_path)

    def fetch_transport(self, id, owner):
        """Obtiene el transporte desde la base de datos."""
        try:
            connection = mysql.connector.connect(**self.db_config)
            with connection.cursor(dictionary=True) as cursor:
                query = """
                SELECT type, transport_id FROM transports WHERE valid = 1 AND owner = %s AND id = %s;
                """
                cursor.execute(query, (owner, id))
                result = cursor.fetchone()
                if result is None:
                    print(f"Error: No se encontró el registro para id {id}.")
                    return None
                return result
        except mysql.connector.Error as err:
            print(f"Error de base de datos: {err}")
            return None
        finally:
            if connection.is_connected():
                connection.close()

    def send_notification(
        self, transport_type, transport_id, data, message_type, details=None
    ):
        """Envia la notificación según el tipo de transporte."""

        if transport_type == "email":
            self.send_email(transport_id, data, message_type, details)
        elif transport_type == "telegram":
            self.send_telegram(transport_id, data, message_type, details)
        else:
            print("Error: Transporte desconocido")

    def send_email(self, transport_id, data, message_type, details=None):
        """Simula el envío de un correo electrónico."""
        print(f"Enviando correo a: {transport_id}")
        message = email_message_manager(data, message_type, details)
        send_email(transport_id, message['subject'], message['body'])
        # Lógica de envío de correo

    def send_telegram(self, transport_id, data, message_type, details=None):
        """Envía un mensaje a través de Telegram."""
        print(f"Enviando Telegram a: {transport_id}")
        # Lógica para enviar el mensaje de Telegram
        message = telegram_message_manager(data, message_type, details)
        self.telegram_bot.send_message(transport_id, message)

    def notifier(self, data, message_type, details=None):
        """Itera sobre la lista de transportes y envía notificaciones."""
        # Si transport_list es una cadena JSON, la convierte a lista
        transport_list = data["transports"]
        owner = data["owner"]
        if isinstance(transport_list, str):
            transport_list = json.loads(transport_list)

        for id in transport_list:
            transport = self.fetch_transport(id, owner)
            if transport:
                self.send_notification(
                    transport["type"],
                    transport["transport_id"],
                    data,
                    message_type,
                    details,
                )
