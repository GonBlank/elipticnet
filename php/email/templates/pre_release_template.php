
<?php
require_once __DIR__ . '/../../env.php';

function pre_release_template_eng($hash_id , $email)
{

    $url = DOMAIN;
    $url_unsuscribe = DOMAIN . "/unsuscribe.html?id=" . $hash_id . "&email=" . $email;
    $url_image = DOMAIN . "/img/logo.webp";

    return <<<HTML
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elipticnet || Notification</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap');
</style>

<body style="font-family: 'Lexend', sans-serif; margin: 0; padding: 0; color: #F5F0FE;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style=" width:600px">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Titulo -->
                    <div
                        style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">

                        <img style="width: 70px; font-size: 32px;" src="$url_image" alt="📡">
                        <h1 style="font-size: 32px;">
                            Welcome to the Elipticnet<br> Early Access List!
                        </h1>
                    </div>
                    <!-- Tabla presentacion -->
                    <div
                        style="font-weight: 300; font-size: 13px; color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px;margin: 10px 0;">

                        <div style=" text-align: left;">

                            <p>Thank you for subscribing to the pre-release list of Elipticnet! </p>
                            <p>
                                We're thrilled to have you on board as we prepare to launch our network
                                monitoring software proyect.
                            </p>

                            <p>As an early subscriber, you’ll be the first to: </p>

                            <li> Receive updates about our progress. </li>

                            <li>Get exclusive insights into our features.</li>

                            <li>Be notified as soon as we launch.</li>

                        </div>

                        <p style="text-align: left;">
                            We’re excited to share this journey with you and can’t wait for you to experience what we’ve
                            been working on. Stay tuned for updates in your inbox!</p>

                        <p style="text-align: left;">If you have any questions or suggestions, feel free to contact us
                            at <a style="font-weight: 600; color: #7230f7; text-decoration: none; cursor: pointer;"
                                href="mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue.">support@elipticnet.com</a>.
                        </p>

                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <p style="color: black; font-size: 10px; margin-top: 20px;">
                            If you want to stop receiving these types of emails, click  <a
                                style="font-weight: 600; color: #7230f7; text-decoration: none; cursor: pointer;"
                                href="$url_unsuscribe">unsubscribe</a>.
                        </p>
                        <p style="color: black; font-size: 10px;">
                            This email was sent by <a
                                style="font-weight: 600; color: #7230f7; text-decoration: none; cursor: pointer;"
                                href="$url">elipticnet.com</a>.
                        </p>
                    </footer>

                </div>
            </td>
        </tr>
    </table>
</body>

</html>


HTML;
}


function pre_release_template_spa($hash_id , $email)
{
    $url = DOMAIN;
    $url_unsuscribe = DOMAIN . "/unsuscribe.html?id=" . $hash_id . "&email=" . $email;
    $url_image = DOMAIN . "/img/logo.webp";

    return <<<HTML
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elipticnet || Notificación</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap');
</style>

<body style="font-family: 'Lexend', sans-serif; margin: 0; padding: 0; color: #F5F0FE;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style=" width:600px">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Titulo -->
                    <div
                        style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">

                        <img style="width: 70px; font-size: 32px;" src="$url_image" alt="📡">
                        <h1 style="font-size: 32px;">
                            ¡Bienvenido a la lista de acceso anticipado de Elipticnet!
                        </h1>
                    </div>
                    <!-- Tabla presentacion -->
                    <div
                        style="font-weight: 300; font-size: 13px; color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px;margin: 10px 0;">

                        <div style=" text-align: left;">

                            <p>¡Gracias por suscribirte a la lista de pre-lanzamiento de Elipticnet!</p>
                            <p>
                                Estamos emocionados de tenerte a bordo mientras nos preparamos para lanzar nuestro proyecto.
                            </p>

                            <p>Como suscriptor anticipado, serás el primero en:</p>

                            <li> Recibir actualizaciones sobre nuestro progreso. </li>

                            <li> Obtener información exclusiva sobre nuevas características.</li>

                            <li> Ser notificado en cuanto lancemos la aplicación.</li>

                        </div>

                        <p style="text-align: left;">
                            Estamos emocionados de compartir este viaje contigo y no podemos esperar para que
                            experimentes lo que hemos estado trabajando. ¡Mantente atento a las actualizaciones en tu
                            bandeja de entrada!</p>

                        <p style="text-align: left;">Si tienes alguna pregunta o sugerencia, no dudes en contactarnos
                            en <a style="font-weight: 600; color: #7230f7; text-decoration: none; cursor: pointer;"
                                href="mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue.">support@elipticnet.com</a>.
                        </p>

                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <p style="color: black; font-size: 10px; margin-top: 20px;">
                            Si deseas dejar de recibir este tipo de correos, haz clic en <a
                                style="font-weight: 600; color: #7230f7; text-decoration: none; cursor: pointer;"
                                href= "$url_unsuscribe">darse de baja</a>.
                        </p>
                        <p style="color: black; font-size: 10px;">
                            Este correo fue enviado por <a
                                style="font-weight: 600; color: #7230f7; text-decoration: none; cursor: pointer;"
                                href="$url">elipticnet.com</a>.
                        </p>
                    </footer>

                </div>
            </td>
        </tr>
    </table>
</body>

</html>
HTML;
}


?>