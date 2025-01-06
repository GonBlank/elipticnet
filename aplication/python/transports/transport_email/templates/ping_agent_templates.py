import json
from datetime import datetime, timezone
import pytz

from global_functions.time_translate import time_translate
from global_functions.duration_format import duration_format
from global_functions.get_user_name import get_user_name

# ╔═══════════════╗
# ║ GLOBAL CONFIG ║
# ╚═══════════════╝

with open("/var/www/elipticnet/aplication/config/config.json", "r") as file:
    config = json.load(file)

DOMAIN = config["app"]["DOMAIN"]
TIME = datetime.now(timezone.utc)


# ping_agent_up_template(owner, id, name, ip, last_down)
# ping_agent_down_template(owner,id, name, ip, cause)
# ping_agent_latency_threshold_exceeded(id, name, ip, latency, threshold)
# ping_agent_latency_threshold_restored(id, name, ip, latency, threshold)


def test_template():
    html_content = f"""<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ping Agent Notification</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap');

    body {{
        font-family: "Lexend", sans-serif;
    }}

    p {{
        font-weight: 300;
        font-size: 13px;
    }}

    h1 {{
        font-size: 32px;
    }}

    small {{
        font-size: 11px;
        font-weight: 600;
    }}
</style>

<body style="margin: 0; padding: 0; color: #F5F0FE;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style=" width:600px">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Titulo -->
                    <div
                        style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">

                        <img style="width: 50px; font-size: 32px;"
                            src="http://192.168.0.189/aplication/img/public/arrow_circle_up.png" alt="✅">
                        <h1>
                            NAME - <span style="color: #B5F730;"> UP </span>
                        </h1>
                    </div>

                    <!-- Tabla presentacion -->
                    <div
                        style="color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px;margin: 10px 0;">

                        <div style=" text-align: left;">
                            <p>Hello Gonzalo,</p>
                            <p>Great news! The recent issue has been resolved, and your monitor is back online.
                                Everything is running smoothly again!</p>
                        </div>

                        <!-- Tabla de datos -->

                        <div
                            style="background-color: rgba(128, 128, 128, 0.096); border-radius: 8px  ;text-align: left; padding: 15px;">
                            <!--row-->
                            <div>
                                <small>Monitor name</small>
                                <p>PING 200.59.15.210</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Checked IP</small>
                                <p>192.168.1.1</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Incident started at</small>
                                <p>2025-01-05 10:00:00</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Resolved at</small>
                                <p>2025-01-05 10:05:00</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Duration</small>
                                <p>5 minutes</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!-- Botón -->
                            <div style="text-align: center;">
                                <a href="#"
                                    style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                    GO TO AGENT
                                </a>
                            </div>


                        </div>

                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <small style="color: black; font-size: 12px; margin-top: 20px; display: inline-block;">
                            This email was sent by <a href="https://elipticnet.com"
                                style="color: #7230f7; text-decoration: none;">elipticnet.com</a>. You can update your
                            alert preferences in the <a href="https://elipticnet.com"
                                style="color: #7230f7; text-decoration: none;">dashboard</a>.
                        </small>
                    </footer>

                </div>
            </td>
        </tr>
    </table>
</body>

</html>

"""
    return html_content


def ping_agent_up_template(owner, id, name, ip, last_down):

    # Convertir last_down a datetime si es una cadena
    if isinstance(last_down, str):
        last_down = datetime.strptime(last_down, "%Y-%m-%d %H:%M:%S")

    # Asegurarse de que last_down tenga zona horaria UTC
    if last_down.tzinfo is None:
        last_down = pytz.utc.localize(
            last_down
        )  # Localizar a UTC si no tiene zona horaria

    # Calcular la diferencia entre last_down y TIME
    time_diff = TIME - last_down

    # Formatear la duración utilizando la función duration_format
    duration = duration_format(time_diff)

    html_content = f"""
   <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ping Agent Notification</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap');

    body {{
        font-family: "Lexend", sans-serif;
    }}

    p {{
        font-weight: 300;
        font-size: 13px;
    }}

    h1 {{
        font-size: 32px;
    }}

    small {{
        font-size: 13px;
        font-weight: 600;
    }}
    a{{
        font-weight: 600;
    }}
</style>

<body style="margin: 0; padding: 0; color: #F5F0FE;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style=" width:600px">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Titulo -->
                    <div
                        style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">

                        <img style="width: 50px; font-size: 32px;"
                            src="http://192.168.0.189/aplication/img/public/arrow_circle_up.png" alt="✅">
                        <h1>
                            {name} - <span style="color: #B5F730;"> UP </span>
                        </h1>
                    </div>

                    <!-- Tabla presentacion -->
                    <div
                        style="color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px;margin: 10px 0;">

                        <div style=" text-align: left;">
                            <p>Hello {get_user_name(owner)},</p>
                            <p>Great news! The recent issue has been resolved, and your monitor is back online.
                                Everything is running smoothly again!</p>
                        </div>

                        <!-- Tabla de datos -->

                        <div
                            style="background-color: rgba(128, 128, 128, 0.096); border-radius: 8px  ;text-align: left; padding: 15px;">
                            <!--row-->
                            <div>
                                <small>Monitor name</small>
                                <p>{name}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Checked IP</small>
                                <p>{ip}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Incident started at</small>
                                <p>{time_translate(last_down, owner)}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Resolved at</small>
                                <p>{time_translate(TIME, owner)}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Duration</small>
                                <p>{duration}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!-- Botón -->
                            <div style="text-align: center;">
                                <a href="{DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id}"
                                    style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                    GO TO AGENT
                                </a>
                            </div>


                        </div>

                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <small style="color: black; font-size: 12px; margin-top: 20px; display: inline-block;">
                            This email was sent by <a href="{DOMAIN}"
                                style="color: #7230f7; text-decoration: none;">elipticnet.com</a>. You can update your
                            alert preferences in the <a href="{DOMAIN + "/aplication/public/ping_agent_edit.php?id=" + id}"
                                style="color: #7230f7; text-decoration: none;">agent</a>.
                        </small>
                    </footer>

                </div>
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
    <title>Ping Agent Notification</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap');

    body {{
        font-family: "Lexend", sans-serif;
    }}

    p {{
        font-weight: 300;
        font-size: 13px;
    }}

    h1 {{
        font-size: 32px;
    }}

    small {{
        font-size: 13px;
        font-weight: 600;
    }}
    
    a{{
        font-weight: 600;
    }}
</style>

<body style="margin: 0; padding: 0; color: #F5F0FE;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style=" width:600px">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Titulo -->
                    <div
                        style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">

                        <img style="width: 50px; font-size: 32px;"
                            src="http://192.168.0.189/aplication/img/public/arrow_circle_down.png" alt="❌">
                        <h1>
                            {name} - <span style="color: #F73051;"> DOWN </span>
                        </h1>
                    </div>

                    <!-- Tabla presentacion -->
                    <div
                        style="color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px;margin: 10px 0;">

                        <div style=" text-align: left;">
                            <p>Hello {get_user_name(owner)},</p>
                            <p style="font-weight: 600;">An incident has been detected on your monitor, and your service is currently down.</p>
                            <p>We are actively monitoring the situation and will notify you once the service is
                                restored.</p>
                        </div>

                        <!-- Tabla de datos -->

                        <div
                            style="background-color: rgba(128, 128, 128, 0.096); border-radius: 8px  ;text-align: left; padding: 15px;">
                            <!--row-->
                            <div>
                                <small>Monitor name</small>
                                <p>{name}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Checked IP</small>
                                <p>{ip}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Root cause</small>
                                <p>{cause}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Incident started at</small>
                                <p>{time_translate(TIME, owner)}</p>
                                <hr style="border-bottom: none;">
                            </div>


                            <!-- Botón -->
                            <div style="text-align: center;">
                                <a href="{DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id}"
                                    style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                    GO TO AGENT
                                </a>
                            </div>


                        </div>

                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <small style="color: black; font-size: 12px; margin-top: 20px; display: inline-block;">
                            This email was sent by <a href="{DOMAIN}"
                                style="color: #7230f7; text-decoration: none;">elipticnet.com</a>. You can update your
                            alert preferences in the <a href="{DOMAIN + "/aplication/public/ping_agent_edit.php?id=" + id}"
                                style="color: #7230f7; text-decoration: none;">agent</a>.
                        </small>
                    </footer>

                </div>
            </td>
        </tr>
    </table>
</body>

</html>
    """
    return html_content


def ping_agent_latency_threshold_exceeded(owner, id, name, ip, latency, threshold):
    html_content = f"""
  <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ping Agent Notification</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap');

    body {{
        font-family: "Lexend", sans-serif;
    }}

    p {{
        font-weight: 300;
        font-size: 13px;
    }}

    h1 {{
        font-size: 32px;
    }}

    small {{
        font-size: 13px;
        font-weight: 600;
    }}

    a {{
        font-weight: 600;
    }}
</style>

<body style="margin: 0; padding: 0; color: #F5F0FE;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style=" width:600px">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Titulo -->
                    <div
                        style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">

                        <img style="width: 50px; font-size: 32px;"
                            src="http://192.168.0.189/aplication/img/public/warning.png" alt="⚠️">
                        <h1>
                            {name} - <span style="color: #ffd000;"> WARNING </span>
                        </h1>
                    </div>

                    <!-- Tabla presentacion -->
                    <div
                        style="color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px;margin: 10px 0;">

                        <div style=" text-align: left;">
                            <p>Hello {get_user_name(owner)},</p>

                            <p style="font-weight: 600;">The latency for your monitor has exceeded the configured
                                threshold.</p>
                            <p>We’ll continue to monitor the situation and will notify you if the latency returns to
                                normal levels.</p>
                        </div>

                        <!-- Tabla de datos -->

                        <div
                            style="background-color: rgba(128, 128, 128, 0.096); border-radius: 8px  ;text-align: left; padding: 15px;">
                            <!--row-->
                            <div>
                                <small>Monitor name</small>
                                <p>{name}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Checked IP</small>
                                <p>{ip}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Current latency</small>
                                <p>{latency} ms</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Configured threshold</small>
                                <p>{threshold} ms</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Incident started at</small>
                                <p>{time_translate(TIME, owner)}</p>
                                <hr style="border-bottom: none;">
                            </div>


                            <!-- Botón -->
                            <div style="text-align: center;">
                                <a href="{DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id}"
                                    style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                    GO TO AGENT
                                </a>
                            </div>


                        </div>

                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <small style="color: black; font-size: 12px; margin-top: 20px; display: inline-block;">
                            This email was sent by <a href="{DOMAIN}"
                                style="color: #7230f7; text-decoration: none;">elipticnet.com</a>. You can update your
                            alert preferences in the <a href="{DOMAIN + "/aplication/public/ping_agent_edit.php?id=" + id}"
                                style="color: #7230f7; text-decoration: none;">agent</a>.
                        </small>
                    </footer>

                </div>
            </td>
        </tr>
    </table>
</body>

</html>
    """
    return html_content


def ping_agent_latency_threshold_restored(owner, id, name, ip, latency, threshold):
    html_content = f"""<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ping Agent Notification</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap');

    body {{
        font-family: "Lexend", sans-serif;
    }}

    p {{
        font-weight: 300;
        font-size: 13px;
    }}

    h1 {{
        font-size: 32px;
    }}

    small {{
        font-size: 13px;
        font-weight: 600;
    }}

    a {{
        font-weight: 600;
    }}
</style>

<body style="margin: 0; padding: 0; color: #F5F0FE;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding: 20px; text-align: center;">
        <tr>
            <td align="center" style=" width:600px">
                <!-- Card -->
                <div style="width: 600px;">
                    <!-- Titulo -->
                    <div
                        style="background-color: #170F2F; border-radius: 8px; padding: 15px; max-width: 100%; text-align: center;">

                        <img style="width: 50px; font-size: 32px;"
                            src="http://192.168.0.189/aplication/img/public/check_circle.png" alt="✅">
                        <h1>
                            {name} - <span style="color: #B5F730;"> RESTORED </span>
                        </h1>
                    </div>

                    <!-- Tabla presentacion -->
                    <div
                        style="color: black; border: 1px solid rgba(128, 128, 128, 0.192); border-radius: 8px; padding: 15px;margin: 10px 0;">

                        <div style=" text-align: left;">
                            <p>Hello {get_user_name(owner)},</p>

                            <p>Good news! The latency has dropped back to normal and is within the threshold you set.</p>
                        </div>

                        <!-- Tabla de datos -->

                        <div
                            style="background-color: rgba(128, 128, 128, 0.096); border-radius: 8px  ;text-align: left; padding: 15px;">
                            <!--row-->
                            <div>
                                <small>Monitor name</small>
                                <p>{name}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Checked IP</small>
                                <p>{ip}</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Current latency</small>
                                <p>{latency} ms</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Configured threshold</small>
                                <p>{threshold} ms</p>
                                <hr style="border-bottom: none;">
                            </div>

                            <!--row-->
                            <div>
                                <small>Resolved at</small>
                                <p>{time_translate(TIME, owner)}</p>
                                <hr style="border-bottom: none;">
                            </div>


                            <!-- Botón -->
                            <div style="text-align: center;">
                                <a href="{DOMAIN + "/aplication/public/ping_agent_view.php?id=" + id}"
                                    style="display: inline-block; padding: 12px 20px; background-color: #7230f7; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; margin: 25px 0;">
                                    GO TO AGENT
                                </a>
                            </div>


                        </div>

                    </div>

                    <footer style="margin-top: 10px; text-align: center;">
                        <small style="color: black; font-size: 12px; margin-top: 20px; display: inline-block;">
                            This email was sent by <a href="{DOMAIN}"
                                style="color: #7230f7; text-decoration: none;">elipticnet.com</a>. You can update your
                            alert preferences in the <a href="{DOMAIN + "/aplication/public/ping_agent_edit.php?id=" + id}"
                                style="color: #7230f7; text-decoration: none;">agent</a>.
                        </small>
                    </footer>

                </div>
            </td>
        </tr>
    </table>
</body>

</html>
    """
    return html_content
