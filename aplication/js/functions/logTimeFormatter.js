
export function logTimeFormatter(time) {

    /**
     * Recibe time, debe ser una cadena de texto que represente una fecha/hora en formato ISO (ejemplo 2025-01-23T10:00:00)
     * Formatea una fecha y hora: DD/MM/YYYY HH:mm:ss
     * Convierte time a la zona horaria del navegador cliente
     */
    const date = new Date(time + " UTC"); // Interpretar como UTC

    // Obtener los componentes individuales en la zona horaria del cliente
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Mes empieza en 0
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');

    // Formatear la fecha
    const formattedDate = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;

    return formattedDate;
}