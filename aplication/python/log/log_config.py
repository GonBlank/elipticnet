# log_config.py
import logging
import os
from datetime import datetime, timedelta, timezone


def setup_logger(script_name):
    # Ruta fija para guardar los logs
    log_dir = "/var/www/elipticnet/aplication/python/log"
    os.makedirs(log_dir, exist_ok=True)  # Crear la carpeta si no existe

    # Crear un nombre de archivo de log basado en el nombre del script
    log_filename = os.path.join(log_dir, f"{script_name}.log")

    # Crear un logger
    logger = logging.getLogger(script_name)
    logger.setLevel(logging.DEBUG)  # Nivel global para todos los logs

    # Clase personalizada para manejar la zona horaria GMT-3
    class GMT3Formatter(logging.Formatter):
        converter = lambda *args: datetime.now(
            tz=timezone(timedelta(hours=-3))
        )  # GMT-3

        def formatTime(self, record, datefmt=None):
            ct = self.converter()
            if datefmt:
                return ct.strftime(datefmt)
            return ct.isoformat()

    # Definir un formato común para los logs
    formatter = GMT3Formatter(
        "%(asctime)s - %(name)s - %(levelname)s - %(message)s",
        datefmt="%Y-%m-%d %H:%M:%S",
    )

    # Manejador para guardar los logs en un archivo
    file_handler = logging.FileHandler(log_filename)
    file_handler.setLevel(logging.DEBUG)  # Puede ser otro nivel si lo prefieres
    file_handler.setFormatter(formatter)

    # Manejador para la consola
    console_handler = logging.StreamHandler()
    console_handler.setLevel(logging.INFO)  # Solo INFO y superior para la consola
    console_handler.setFormatter(formatter)

    # Agregar manejadores al logger
    logger.addHandler(file_handler)
    logger.addHandler(console_handler)

    return logger
