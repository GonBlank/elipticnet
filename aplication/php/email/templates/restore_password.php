
<?php
require_once '../env.php';

function restore_password_template($hash)
{
    $validation_url = DOMAIN . "/aplication/public/restore_password.html?validation_hash=" . $hash;
    $url = DOMAIN;
    $url_image = DOMAIN . "/aplication/img/public/lock_reset.png";


    return <<<HTML
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elipticnet || Account Notification</title>
</head>


<style>
    @font-face {
        font-family: 'Lexend';
        font-style: normal;
        font-weight: 100;
        src: url('https://fonts.gstatic.com/s/lexend/v35/wlpzgwfFfC1xl7r4k_hvYw.woff2') format('woff2');
    }
    @font-face {
        font-family: 'Lexend';
        font-style: normal;
        font-weight: 300;
        src: url('https://fonts.gstatic.com/s/lexend/v35/wlpzgwfFfC1xl7r4k_chvYw.woff2') format('woff2');
    }
    @font-face {
        font-family: 'Lexend';
        font-style: normal;
        font-weight: 400;
        src: url('https://fonts.gstatic.com/s/lexend/v35/wlpzgwfFfC1xl7r4k5hvYw.woff2') format('woff2');
    }
    @font-face {
        font-family: 'Lexend';
        font-style: normal;
        font-weight: 700;
        src: url('https://fonts.gstatic.com/s/lexend/v35/wlpzgwfFfC1xl7r4k4lvYw.woff2') format('woff2');
    }

    body {
        font-family: 'Lexend', Arial, sans-serif;
    }
</style>

<body style="margin: 0; padding: 0; color: #F5F0FE; font-family: 'Lexend', sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style="width: 600px;">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Titulo -->
                    <div style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">
                        <img style="width: 50px; font-size: 32px;" src="$url_image" alt="🔄">
                        <h1 style="font-size: 32px; margin: 0;">Password Recovery Request</h1>
                    </div>

                    <!-- Tabla presentación -->
                    <div style="color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px; margin: 10px 0;">
                        <div style="text-align: left;">
                            <p style="font-weight: 300; font-size: 13px; margin: 0;">
                                We’ve received your request to reset your password. To proceed, please click the button below to create a new password.
                            </p>
                        </div>

                        <!-- Botón -->
                        <div style="text-align: center; margin: 25px 0;">
                            <a href="$validation_url" style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px;">
                                Reset password
                            </a>
                        </div>

                        <p style="font-weight: 300; font-size: 13px; margin: 0;">
                            For security reasons, this link will expire in 24 hours. 
                            <span style="font-weight: 600;">If you didn’t request a password reset</span>, please ignore this email or contact our 
                            <a href="mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue." style="font-weight: 600; color: #7230f7; text-decoration: none;">
                                support team
                            </a> immediately.
                        </p>

                        <p style="font-weight: 300; font-size: 13px; margin: 50px 0 0;">
                            Thank you for using <a href="$url" style="font-weight: 600; color: #7230f7; text-decoration: none;">Elipticnet</a>.
                        </p>
                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <small style="color: black; font-size: 12px; margin-top: 20px; display: inline-block;">
                            This email was sent by <a href="$url" style="font-weight: 600; color: #7230f7; text-decoration: none;">elipticnet.com</a>.
                        </small>
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