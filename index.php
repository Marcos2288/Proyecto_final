<?php
session_start();

$destino = isset($_SESSION["usuario"]) ? "views/home.php" : "views/login.php";
header("Location: " . $destino);
exit();
