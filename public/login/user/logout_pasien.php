<?php
session_start();
$_SESSION = [];
session_unset();
session_destroy();

// Hapus semua cookie yang dipakai untuk remember me
setcookie('id_user', '', time() - 3600, "/");
setcookie('key_user', '', time() - 3600, "/");
setcookie('login_user', '', time() - 3600, "/");

// Hapus cookie session PHP
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

header("Location: ../user_login.php");
exit;


?>