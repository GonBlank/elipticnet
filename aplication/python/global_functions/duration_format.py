from datetime import timedelta

def duration_format(time_diff: timedelta) -> str:
    """Devuelve la duración en formato mínimo y exacto (sin unidades innecesarias)."""
    # Inicializar la lista de partes de la duración
    duration_parts = []

    # Si hay días, agregarlo a la duración
    if time_diff.days > 0:
        duration_parts.append(f"{time_diff.days} day{'s' if time_diff.days > 1 else ''}")

    # Si hay horas, agregarlo a la duración
    if time_diff.seconds // 3600 > 0:
        hours = time_diff.seconds // 3600
        duration_parts.append(f"{hours} hour{'s' if hours > 1 else ''}")

    # Si hay minutos, agregarlo a la duración
    if (time_diff.seconds % 3600) // 60 > 0:
        minutes = (time_diff.seconds % 3600) // 60
        duration_parts.append(f"{minutes} minute{'s' if minutes > 1 else ''}")

    # Si hay segundos, agregarlo a la duración
    if time_diff.seconds % 60 > 0:
        seconds = time_diff.seconds % 60
        duration_parts.append(f"{seconds} second{'s' if seconds > 1 else ''}")

    # Si la lista de partes no está vacía, unirlas con espacios
    return ' '.join(duration_parts) if duration_parts else '0 seconds'
