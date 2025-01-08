
<?php
require_once '../env.php';

function delete_account_email_template($user_name)
{
    $url = DOMAIN;
    $url_image = DOMAIN . "/aplication/img/public/delete.png";
    

    return <<<HTML
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elipticnet || Account Notification</title>
</head>

<body style="margin: 0; padding: 0; color: #F5F0FE; font-family: 'Lexend', sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style="width: 600px;">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Titulo -->
                    <div style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">
                        <img style="width: 50px; font-size: 32px;" src="$url_image" alt="🗑️">
                        <h1 style="font-size: 32px; margin: 0;">Deleted account</h1>
                    </div>

                    <!-- Tabla presentación -->
                    <div style="color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px; margin: 10px 0;">
                        <div style="text-align: left;">
                            <p style="font-weight: 300; font-size: 13px; margin: 0;">Dear $user_name,</p>
                            <p style="font-weight: 300; font-size: 13px; margin: 10px 0;">
                                We’re sorry to see you go! Your account has been successfully deleted from our platform.
                            </p>
                            <p style="font-weight: 300; font-size: 13px; margin: 10px 0;">
                                If you don’t mind, we’d appreciate it if you could take a moment to let us know why you decided to leave. Your feedback is invaluable and helps us improve our service.
                            </p>
                        </div>

                        <!-- Botón -->
                        <div style="text-align: center; margin: 25px 0;">
                            <a href="https://forms.gle/UQynKp7z7E35sEAb9" 
                               style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px;">
                               Share Feedback
                            </a>
                        </div>

                        <p style="font-weight: 300; font-size: 13px; margin: 10px 0;">
                            Thank you for having been a part of our community. If you ever decide to return, we’ll be more than happy to welcome you back.
                        </p>
                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <small style="color: black; font-size: 12px; display: inline-block;">
                            This email was sent by <a href="$url" style="font-weight: 600; color: #7230f7; text-decoration: none;">elipticnet.com</a>. 
                            If you did not request this action, contact our 
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