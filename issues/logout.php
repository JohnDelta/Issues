<?php

$logged_in_session_key = "loggedin";

session_start();
$_SESSION = array();
session_destroy();

if (isset($_COOKIE[$logged_in_session_key])) {
	setcookie($logged_in_session_key, "", time() - 3600, "/"); // expire it
}

header("location: login.php");
exit;

?>