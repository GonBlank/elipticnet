
<?php
require_once '../env.php';

function delete_account_email_template()
{

    return <<<HTML
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elipticnet || 🗑️ Deleted account</title>
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
                                🗑️ Deleted account
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="color: #F5F0FE; font-size: 16px; margin: 20px 0;">
                                We are sorry to see you go and we hope you return soon.<br>
                                If you have experienced any problems you can contact our support team.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="mailto:support@elipticnet.com?subject=Support%20Request&body=Please%20provide%20details%20about%20your%20issue."
                                style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                Talk to support
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <small style="color: #F5F0FE; font-size: 12px; margin-top: 20px; display: inline-block;">
                                This email was sent by <a href="https://elipticnet.com"
                                    style="color: #b499ff; text-decoration: none;">elipticnet.com</a>. If you were the one who requested the account deletion, you do not need to take any further action.
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