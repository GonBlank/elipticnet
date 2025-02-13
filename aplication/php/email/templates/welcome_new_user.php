
<?php
require_once __DIR__ . '/../../env.php';

function welcome_new_user_template($user_name)
{
    $url = APP_LINK;
    $home_url = APP_LINK . "/aplication/public/home.php";
    $font_url = APP_LINK . "/aplication/fonts/Lexend-VariableFont_wght.ttf";
    $url_image_logo = APP_LINK . "/aplication/img/email/logo.png";

    return <<<HTML
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elipticnet || Welcome!</title>
</head>

<style>
    @font-face {
        font-family: 'Lexend Variable';
        src: url($font_url) format('truetype');
        font-weight: 100 900;
        font-display: swap;
    }

    * {
        font-family: "Lexend Variable", sans-serif;
    }
</style>

<body style="margin: 0; padding: 0; color: #F5F0FE; font-family: 'Lexend', sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style="width: 600px;">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Header -->
                    <div style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">
                        <img style="width: 50px; font-size: 32px;" src="$url_image_logo" alt="🚀">
                        <h1 style="font-size: 32px; margin: 0;">Welcome to Elipticnet!</h1>
                    </div>

                    <!-- Main Content -->
                    <div style="color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px; margin: 10px 0;">
                        <div style="text-align: left;">
                            <p style="font-weight: 300; font-size: 13px; margin: 0;">Hello $user_name,</p>
                            <p style="font-weight: 300; font-size: 13px; margin: 10px 0;">
                                We are delighted to have you join our community! Elipticnet is here to help you monitor efficiently and stay in control at all times.
                            </p>
                            <p style="font-weight: 300; font-size: 13px; margin: 10px 0;">
                                Start exploring your dashboard, configure your monitoring preferences, and take advantage of our powerful tools tailored to your needs.
                            </p>
                        </div>

                        <!-- Button -->
                        <div style="text-align: center; margin: 25px 0;">
                            <a href="$home_url"
                                style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px;">
                                Go to Dashboard
                            </a>
                        </div>

                        <p style="font-weight: 300; font-size: 13px; margin: 10px 0;">
                            If you have any questions or need help getting started, feel free to reach out to our support team at any time.
                        </p>
                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <small style="color: black; font-size: 12px; display: inline-block;">
                            This email was sent by <a href="$url"
                                style="font-weight: 600; color: #7230f7; text-decoration: none;">elipticnet.com</a>. If you did not sign up for an account, please contact our 
                            <a href="mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."
                                style="font-weight: 600; color: #7230f7; text-decoration: none;">support team</a>.
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