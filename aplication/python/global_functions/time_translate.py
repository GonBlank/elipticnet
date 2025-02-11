import pytz
from datetime import datetime
from global_functions.get_user_timezone import get_user_timezone

def time_translate(time, user_id):
    """Convierte el tiempo de UTC a la zona horaria del usuario y lo devuelve en formato '%Y-%m-%d %H:%M:%S'."""
    try:
        # Verificamos si el `time` es una cadena y la convertimos a datetime
        if isinstance(time, str):
            # Convertir el string en un objeto datetime
            time = datetime.strptime(time, "%Y-%m-%d %H:%M:%S")

        # Obtener la zona horaria del usuario desde la base de datos
        user_tz = get_user_timezone(user_id)
        
        if user_tz is None:
            print("No se encontró la zona horaria del usuario.")
            return None
        
        tz = pytz.timezone(user_tz)

        # Asegurarse de que `time` tenga zona horaria UTC si no la tiene
        if time.tzinfo is None:
            # Asignar zona horaria UTC si `time` no tiene tzinfo
            utc_time = pytz.utc.localize(time)  # Localizamos el tiempo a UTC
        else:
            utc_time = time.astimezone(pytz.utc)  # Convertimos el tiempo a UTC si ya tiene tzinfo
        
        # Ahora convertir el tiempo UTC a la zona horaria del usuario
        user_time = utc_time.astimezone(tz)

        # Devolver el tiempo formateado como una cadena
        return user_time.strftime("%Y-%m-%d %H:%M:%S")

    except Exception as e:
        print(f"Error al convertir el tiempo: {e}")
        return None
