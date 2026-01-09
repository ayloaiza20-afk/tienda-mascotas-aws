<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.html");
    exit;
}

header("Location: login.html"); // o el dashboard principal
exit;
