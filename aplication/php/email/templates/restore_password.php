
<?php
require_once '../env.php';

function restore_password_template($hash)
{
    $validation_url = DOMAIN . "/aplication/public/restore_password.html?validation_hash=" . $hash;

    return <<<HTML
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔒 Restore password</title>
</head>

<body
    style="background-color: #130c27; color: #F5F0FE; font-family: Arial, sans-serif; margin: 0; padding: 0; width: 100%;">

    <table role="presentation"
        style="width: 100%; height: 100%; background-color: #130c27; text-align: center; border-spacing: 0; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 20px;">

                <table role="presentation"
                    style="max-width: 550px; width: 100%; background-color: #170F2F; border-radius: 8px; padding: 20px; box-shadow: 0px 0px 2px 0px #bfb0e8; text-align: center;">
                    <tr>
                        <td>
                            <h1 style="color: #B5F730; font-size: 24px; margin: 20px 0;">
                            🔒 Restore your password
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="color: #F5F0FE; font-size: 16px; margin: 20px 0;">
                                We receive a password recovery request.<br>
                                Recover your password by clicking on the following button.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <a href="$validation_url"
                                style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                Recover password
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <small style="color: #F5F0FE; font-size: 12px; margin-top: 20px; display: inline-block;">
                                This email was sent by <a href="https://elipticnet.com"
                                    style="color: #b499ff; text-decoration: none;">elipticnet.com</a>. If you did not request the change you can dismiss the email.
                            </small>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>

HTML;
}

?>