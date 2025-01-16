<?php
require_once __DIR__ . '/../env.php';
require_once 'close_sesion.php';


close_session();
// Redirect the user to the login page
header("Location: /aplication/public/login.php");
exit;
?>
