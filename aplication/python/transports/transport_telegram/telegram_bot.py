#!/var/www/elipticnet/aplication/python/elipticnet_env/bin/python
import json
import asyncio
from telegram import Bot


class TelegramBot:
    def __init__(self, config_path):
        """
        Inicializa el bot de Telegram cargando el token desde el archivo de configuración.
        """
        self.token = self._load_token(config_path)
        self.bot = Bot(self.token)

    @staticmethod
    def _load_token(config_path):
        """
        Carga el token de Telegram desde el archivo de configuración.
        """
        try:
            with open(config_path, "r") as file:
                config = json.load(file)
                return config["telegram"]["TOKEN"]
        except (FileNotFoundError, KeyError, json.JSONDecodeError) as e:
            raise RuntimeError(f"Error cargando configuración: {e}")

    async def _send_message(self, user_id, message, parse_mode="Markdown"):
        """
        Envía un mensaje de manera asíncrona.
        """
        try:
            await self.bot.send_message(chat_id=user_id, text=message, parse_mode=parse_mode)
        except Exception as e:
            print(f"Error al enviar mensaje: {e}")

    def send_message(self, user_id, message):
        """
        Método público para enviar un mensaje de manera sincrónica.
        Asegura que el bucle de eventos esté abierto antes de intentar ejecutar la tarea.
        """
        # Obtener el bucle de eventos actual o crear uno nuevo si es necesario
        loop = asyncio.get_event_loop()
        if loop.is_running():
            # Si ya hay un bucle de eventos en ejecución, ejecutamos la tarea dentro de ese bucle
            asyncio.ensure_future(self._send_message(user_id, message))
        else:
            # Si no hay un bucle de eventos en ejecución, ejecutamos uno nuevo
            loop.run_until_complete(self._send_message(user_id, message))
