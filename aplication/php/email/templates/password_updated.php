
<?php
require_once '../env.php';

function password_updated_email_template($name)
{
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
                        <h1 style="font-size: 32px; margin: 0;">Password updated</h1>
                    </div>

                    <!-- Tabla presentación -->
                    <div style="color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px; margin: 10px 0;">
                        <div style="text-align: left;">
                            <p style="font-weight: 300; font-size: 13px; margin: 0;">Hello $name,</p>

                            <p style="font-weight: 300; font-size: 13px; margin: 10px 0;">
                                We want to confirm that your password has been successfully updated. If you made this change, no further action is required.
                            </p>

                            <p style="font-weight: 300; font-size: 13px; margin: 10px 0;">
                                <span style="font-weight: 600;">If you did not request this change</span>, please contact our 
                                <a href="mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue." 
                                   style="font-weight: 600; color: #7230f7; text-decoration: none;">
                                   support team
                                </a> immediately to secure your account.
                            </p>

                            <p style="font-weight: 300; font-size: 13px; margin: 10px 0;">
                                If you have any questions or concerns, feel free to reach out to us at 
                                <a href="mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue." 
                                   style="font-weight: 600; color: #7230f7; text-decoration: none;">
                                   support@elipticnet.com
                                </a>.
                            </p>
                        </div>

                        <p style="font-weight: 300; font-size: 13px; margin: 50px 0 0;">
                            Thank you for trusting in <a href="$url" style="font-weight: 600; color: #7230f7; text-decoration: none;">Elipticnet</a>.
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