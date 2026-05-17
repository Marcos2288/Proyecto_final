<?php
require_once "../config/bootstrap.php";

$authService = getAuthService();

if (!$authService->isAuthenticated()) {
    header("Location: ../views/login.php");
    exit();
}

if (isset($_POST["guardar_preferencias"])) {
    $antiSpoiler = isset($_POST["modo_anti_spoiler"]) ? 1 : 0;

    try {
        $authService->updateProfile((int) $_SESSION["usuario"], [
            'modo_anti_spoiler' => $antiSpoiler
        ]);
        header("Location: ../views/profile.php?tab=config&saved=1");
        exit();
    } catch (Exception $e) {
        header("Location: ../views/profile.php?tab=config&saved=0");
        exit();
    }
}

header("Location: ../views/profile.php");
exit();
