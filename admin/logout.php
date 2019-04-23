<?php

session_save_path(__DIR__ . '/session');
session_start();
//$_SESSION = array();
session_destroy();
session_unset();
header('Location:login.html');

?>
