
<?php
require_once __DIR__ . '/../../env.php';
//$url_image_logo = "https://elipticnet.com/img/favicon/android-chrome-192x192.png";
function pre_release_template_eng($hash_id, $email)
{
    $url = APP_LINK;
    $url_unsuscribe = APP_LINK . "/unsuscribe.html?id=" . $hash_id . "&email=" . $email;
    $url_image_logo = APP_LINK . "/img/email/logo.png";
    $url_image_access = APP_LINK . "/img/email/access.png";
    $url_image_calendar = APP_LINK . "/img/email/calendar.png";
    $url_image_notification = APP_LINK . "/img/email/notification.png";
    $url_image_discount = APP_LINK . "/img/email/discount.png";

    return <<<HTML
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elipticnet || Notification</title>
</head>

<body style="cursor: context-menu; font-family: Arial, sans-serif; margin: 0; padding: 0; color: #F5F0FE;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style="width:600px">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Title -->
                    <div
                        style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">
                        <img style="width: 70px; font-size: 32px;" src="$url_image_logo" alt="Elipticnet">
                        <h1 style="cursor: context-menu; font-size: 32px;">
                            Welcome to Early Access!
                        </h1>
                    </div>
                    <!-- Presentation Table -->
                    <div
                        style="font-weight: 300; font-size: 13px; color: #130c27; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px; margin: 10px 0;">
                        <div style="text-align: left;">
                            <h2 style="font-size: 20px;">Thank you for subscribing to Elipticnet Early Access!</h2>
                            <p>We’re excited to have you with us as we prepare for the launch of our monitoring software.</p>
                            <p><b>As an early subscriber, you’ll be the first to:</b></p>
                            <div style="text-align: left; background-color: rgba(128, 128, 128, 0.096); padding: 10px; border-radius: 8px;">
                                <div style="display: inline-block; vertical-align: middle; text-align: left;">
                                    <img src="$url_image_access" width="19px" height="19px" style="display: inline; vertical-align: middle; margin-right: 8px;" alt="🚪">
                                    <p style="display: inline; margin: 0; font-size: 14px; vertical-align: middle;">Receive updates about our progress.</p>
                                </div>
                                <div style="display: inline-block; vertical-align: middle; text-align: left; margin-top: 10px;">
                                    <img src="$url_image_calendar" width="19px" height="19px" style="display: inline; vertical-align: middle; margin-right: 8px;" alt="📅">
                                    <p style="display: inline; margin: 0; font-size: 14px; vertical-align: middle;">Access exclusive information about new features and releases.</p>
                                </div>
                                <div style="display: inline-block; vertical-align: middle; text-align: left;margin-top: 10px;">
                                    <img src="$url_image_notification" width="19px" height="19px" style="display: inline; vertical-align: middle; margin-right: 8px;" alt="🔔">
                                    <p style="display: inline; margin: 0; font-size: 14px; vertical-align: middle;">Be notified as soon as we launch the app.</p>
                                </div>
                                <div style="display: inline-block; vertical-align: middle; text-align: left;margin-top: 10px;">
                                    <img src="$url_image_discount" width="19px" height="19px" style="display: inline; vertical-align: middle; margin-right: 8px;" alt="🏷️">
                                    <p style="display: inline; margin: 0; font-size: 14px; vertical-align: middle;">Receive promotional codes for future premium features.</p>
                                </div>
                            </div>
                        </div>
                        <p style="text-align: left;">
                            We’re eager to share this journey with you and for you to experience everything we’ve been working on. Stay tuned for updates in your inbox!</p>
                        <p style="text-align: left;">If you have any questions or suggestions, feel free to contact us at
                            <a style="font-weight: 600; color: #7230f7; text-decoration: none; cursor: pointer;"
                                href="mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20query.">support@elipticnet.com</a>.
                        </p>
                    </div>
                    <footer style="margin-top: 10px; text-align: center;">
                        <p style="color: black; font-size: 10px; margin-top: 20px;">
                            If you wish to stop receiving these emails, click on <a
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


function pre_release_template_spa($hash_id, $email)
{
    $url = APP_LINK;
    $url_unsuscribe = APP_LINK . "/unsuscribe.html?id=" . $hash_id . "&email=" . $email;
    $url_image_logo = APP_LINK . "/img/email/logo.png";
    $url_image_access = APP_LINK . "/img/email/access.png";
    $url_image_calendar = APP_LINK . "/img/email/calendar.png";
    $url_image_notification = APP_LINK . "/img/email/notification.png";
    $url_image_discount = APP_LINK . "/img/email/discount.png";

    return <<<HTML
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elipticnet || Notificación</title>
</head>

<body style="cursor: context-menu; font-family: Arial, sans-serif; margin: 0; padding: 0; color: #F5F0FE;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style=" width:600px">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Titulo -->
                    <div
                        style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">
                        <img style="width: 70px; font-size: 32px;" src="$url_image_logo" alt="Elipticnet">
                        <h1 style=" cursor: context-menu; font-size: 32px;">
                            ¡Bienvenido al acceso anticipado!
                        </h1>
                    </div>
                    <!-- Tabla presentacion -->
                    <div
                        style="font-weight: 300; font-size: 13px; color: #130c27; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px; margin: 10px 0;">

                        <div style=" text-align: left;">
                            <h2 style="font-size: 20px;">¡Gracias por suscribirte al acceso anticipado de Elipticnet!
                            </h2>
                            <p>Estamos emocionados de tenerte con nosotros mientras preparamos el lanzamiento de nuestro
                                software de monitoreo.</p>

                            <p><b>Como suscriptor anticipado, serás el primero en:</b></p>
                            <div style="text-align: left; background-color:  rgba(128, 128, 128, 0.096); padding: 10px; border-radius: 8px;">
                                <div style="display: inline-block; vertical-align: middle; text-align: left;">
                                    <img src="$url_image_access" width="19px" height="19px" style="display: inline; vertical-align: middle; margin-right: 8px;" alt="🚪">
                                    <p style="display: inline; margin: 0; font-size: 14px; vertical-align: middle;">Recibir actualizaciones sobre nuestro progreso.</p>
                                </div>
                                <div style="display: inline-block; vertical-align: middle; text-align: left; margin-top: 10px;">
                                    <img src="$url_image_calendar" width="19px" height="19px" style="display: inline; vertical-align: middle; margin-right: 8px;" alt="📅">
                                    <p style="display: inline; margin: 0; font-size: 14px; vertical-align: middle;">Acceder a información exclusiva sobre nuevas funciones y lanzamientos.</p>
                                </div>
                                <div style="display: inline-block; vertical-align: middle; text-align: left;margin-top: 10px;">
                                    <img src="$url_image_notification" width="19px" height="19px" style="display: inline; vertical-align: middle; margin-right: 8px;" alt="🔔">
                                    <p style="display: inline; margin: 0; font-size: 14px; vertical-align: middle;">Ser notificado tan pronto como lancemos la aplicación.</p>
                                </div>
                                <div style="display: inline-block; vertical-align: middle; text-align: left;margin-top: 10px;">
                                    <img src="$url_image_discount" width="19px" height="19px" style="display: inline; vertical-align: middle; margin-right: 8px;" alt="🏷️">
                                    <p style="display: inline; margin: 0; font-size: 14px; vertical-align: middle;">Recibir códigos promocionales para futuras funciones premium.</p>
                                </div>
                                
                            </div>
                        </div>

                        <p style="text-align: left;">
                            Estamos ansiosos por compartir este camino contigo y por que experimentes todo lo que hemos
                            estado desarrollando. ¡Mantente atento a las novedades en tu bandeja de entrada!</p>

                        <p style="text-align: left;">Si tienes alguna pregunta o sugerencia, no dudes en contactarnos en
                            <a style="font-weight: 600; color: #7230f7; text-decoration: none; cursor: pointer;"
                                href="mailto:support@elipticnet.com?subject=Solicitud%20de%20Soporte&body=Por%20favor%2C%20proporcione%20detalles%20sobre%20su%20consulta.">support@elipticnet.com</a>.
                        </p>
                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <p style="color: black; font-size: 10px; margin-top: 20px;">
                            Si deseas dejar de recibir este tipo de correos, haz clic en <a
                                style="font-weight: 600; color: #7230f7; text-decoration: none; cursor: pointer;"
                                href="$url_unsuscribe">darse de baja</a>.
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