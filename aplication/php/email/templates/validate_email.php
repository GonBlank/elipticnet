
<?php
require_once __DIR__ . '/../../env.php';

function validate_email_template($user_name, $hash)
{

    $validation_url = APP_LINK . "/aplication/public/login.php?validation_hash=" . $hash;
    $url = APP_LINK;
    $url_image = APP_LINK . "/aplication/img/public/user_check.png";

    return <<<HTML
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elipticnet || Account Notification</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
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

    * {
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
                    <div
                        style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">

                        <img style="width: 50px; font-size: 32px;"
                            src="$url_image" alt="✅">
                        <h1 style="font-size: 32px;">
                            Verify Your Email Address
                        </h1>
                    </div>

                    <!-- Tabla presentacion -->
                    <div
                        style="color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px; margin: 10px 0;">

                        <div style="text-align: left;">
                            <p style="font-weight: 300; font-size: 13px;">Hello $user_name,</p>

                            <p style="font-weight: 300; font-size: 13px;">Thank you for signing up for <a
                                    style="font-weight: 600; color: #7230f7; text-decoration: none;" href="$url">Elipticnet</a>.
                            </p>
                            <p style="font-weight: 300; font-size: 13px;">To complete the setup of your account, please
                                verify your email address by clicking the button below:</p>
                        </div>

                        <!-- Botón -->
                        <div style="text-align: center;">
                            <a href="$validation_url"
                                style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                Verify Email Address
                            </a>
                        </div>

                        <div style="text-align: left;">
                            <p style="font-weight: 300; font-size: 13px;">If you didn’t create this account, please
                                disregard this email.</p>
                            <p style="font-weight: 300; font-size: 13px;">The link will expire in 1 hour, so please
                                make sure to confirm your email as soon as possible. If you have any questions or need
                                assistance, feel free to contact us at <a
                                    style="font-weight: 600; color: #7230f7; text-decoration: none;"
                                    href="mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue.">support@elipticnet.com</a>.
                            </p>
                        </div>

                        <p style="font-weight: 300; font-size: 18px;">Welcome to <a
                                style="font-weight: 600; color: #7230f7; text-decoration: none;" href="$url">Elipticnet</a>!
                        </p>
                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <small style="color: black; font-size: 12px; margin-top: 20px; display: inline-block; font-weight: 600;">
                            This email was sent by <a style="font-weight: 600; color: #7230f7; text-decoration: none;"
                                href="$url">elipticnet.com</a>.
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