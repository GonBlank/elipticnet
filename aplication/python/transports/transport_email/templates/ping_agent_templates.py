import json

# ╔═══════════════╗
# ║ GLOBAL CONFIG ║
# ╚═══════════════╝

with open("/var/www/elipticnet/aplication/config/config.json", "r") as file:
    config = json.load(file)

DOMAIN = config["app"]["DOMAIN"]


# ping_agent_up_template(owner, id, name, ip, last_down)
# ping_agent_down_template(owner,id, name, ip, cause)
# ping_agent_latency_threshold_exceeded(id, name, ip, latency, threshold)
# ping_agent_latency_threshold_restored(id, name, ip, latency, threshold)


def ping_agent_up_template(owner, id, name, ip, last_down):
    html_content = f"""
   <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello! Validate your account</title>
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
                                ✅ PING AGENT {name} - UP
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="color: #F5F0FE; font-size: 16px; margin: 20px 0;">
                                IP:{ip}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id}"
                                style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                GO TO AGENT
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <small style="color: #F5F0FE; font-size: 12px; margin-top: 20px; display: inline-block;">
                                This email was sent by <a href="https://elipticnet.com"
                                    style="color: #b499ff; text-decoration: none;">elipticnet.com</a>. If you didn't
                                request the account, ignore the email.
                            </small>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
    """
    return html_content


def ping_agent_down_template(owner, id, name, ip, cause):
    html_content = f"""
   <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello! Validate your account</title>
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
                                ❌ PING AGENT {name} - DOWN
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="color: #F5F0FE; font-size: 16px; margin: 20px 0;">
                                IP:{ip}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id}"
                                style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                GO TO AGENT
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <small style="color: #F5F0FE; font-size: 12px; margin-top: 20px; display: inline-block;">
                                This email was sent by <a href="https://elipticnet.com"
                                    style="color: #b499ff; text-decoration: none;">elipticnet.com</a>. If you didn't
                                request the account, ignore the email.
                            </small>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
    """
    return html_content


def ping_agent_latency_threshold_exceeded(id, name, ip, latency, threshold):
    html_content = f"""
   <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello! Validate your account</title>
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
                                ⚠️ PING AGENT {name} - LATENCY THRESHOLD EXCEEDED
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="color: #F5F0FE; font-size: 16px; margin: 20px 0;">
                                IP:{ip}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id}"
                                style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                GO TO AGENT
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <small style="color: #F5F0FE; font-size: 12px; margin-top: 20px; display: inline-block;">
                                This email was sent by <a href="https://elipticnet.com"
                                    style="color: #b499ff; text-decoration: none;">elipticnet.com</a>. If you didn't
                                request the account, ignore the email.
                            </small>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
    """
    return html_content


def ping_agent_latency_threshold_restored(id, name, ip, latency, threshold):
    html_content = f"""
   <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello! Validate your account</title>
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
                                ✅ PING AGENT {name} - LATENCY THRESHOLD RESTORED
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p style="color: #F5F0FE; font-size: 16px; margin: 20px 0;">
                                IP:{ip}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id}"
                                style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                GO TO AGENT
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <small style="color: #F5F0FE; font-size: 12px; margin-top: 20px; display: inline-block;">
                                This email was sent by <a href="https://elipticnet.com"
                                    style="color: #b499ff; text-decoration: none;">elipticnet.com</a>. If you didn't
                                request the account, ignore the email.
                            </small>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
    """
    return html_content
