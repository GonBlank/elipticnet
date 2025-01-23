export function elapsedTime(time, formatter = true) {

    /**
     * Calcula el tiempo transcurrido entre una fecha/hora dada (time) y la fecha/hora actual
     * time debe ser una cadena de texto que represente una fecha/hora en formato ISO (ejemplo 2025-01-23T10:00:00)
     * 
     * Si formatter = False devuelve:
     *{
     *  days: int,
     *  hours: int,
     *  minutes: int,
     *  seconds: int
     * }
     * 
     * Si formatter = true devuelve una cadena de texto con el tiempo transcurrido en formato mínimo (solo los datos necesarios)
     */

    // Convertir la cadena de texto 'time' en un objeto Date
    const inputDate = new Date(time + " UTC");  // Agregar " UTC" para asegurar que se interprete como UTC

    // Obtener la fecha y hora actual del cliente
    const currentDate = new Date();

    // Calcular la diferencia en milisegundos
    const timeDiffMilliseconds = currentDate - inputDate;

    // Calcular la diferencia en días, horas, minutos y segundos
    const timeDiffSeconds = Math.floor(timeDiffMilliseconds / 1000);
    const timeDiffMinutes = Math.floor(timeDiffSeconds / 60);
    const timeDiffHours = Math.floor(timeDiffMinutes / 60);
    const timeDiffDays = Math.floor(timeDiffHours / 24);

    // Obtener el resto de horas, minutos y segundos
    const remainingHours = timeDiffHours % 24;
    const remainingMinutes = timeDiffMinutes % 60;
    const remainingSeconds = timeDiffSeconds % 60;

    // Crear un objeto con el timeDiff calculado
    const timeDiff = {
        days: timeDiffDays,
        hours: remainingHours,
        minutes: remainingMinutes,
        seconds: remainingSeconds
    };

    if (formatter) {
       return elapsedTimeFormatter(timeDiff);
    } else {
        return timeDiff;
    }
}

function elapsedTimeFormatter(timeDiff) {
    let timeDiffString = '';

    // Si hay días
    if (timeDiff.days > 0) {
        timeDiffString += ` ${timeDiff.days}day${timeDiff.days > 1 ? 's' : ''}`;
    }

    // Si hay horas
    else if (timeDiff.hours > 0) {
        timeDiffString += ` ${timeDiff.hours}hr${timeDiff.hours > 1 ? 's' : ''}`;
    }

    // Si hay minutos
    else if (timeDiff.minutes > 0) {
        timeDiffString += ` ${timeDiff.minutes}min${timeDiff.minutes > 1 ? 's' : ''}`;
    }

    // Si hay segundos
    else if (timeDiff.seconds > 0) {
        timeDiffString += ` ${timeDiff.seconds}sec${timeDiff.seconds > 1 ? 's' : ''}`;
    }

    // Si no hay tiempo transcurrido
    else {
        timeDiffString = '0sec';
    }

    return timeDiffString;
}