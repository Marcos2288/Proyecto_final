<?php
require_once "../config/bootstrap.php";

$authService = getAuthService();

if (isset($_POST["register"])) {
    try {
        $tipoPlan = $_POST["tipo_plan"] ?? "basico";
        $tipoPlan = in_array($tipoPlan, ["basico", "premium"], true) ? $tipoPlan : "basico";

        $authService->register(
            trim($_POST["nombre"] ?? ""),
            trim($_POST["email"] ?? ""),
            (string) ($_POST["password"] ?? ""),
            $tipoPlan
        );

        header("Location: ../views/login.php");
        exit();
    } catch (ValidationException $e) {
        header("Location: ../views/register.php?error=1");
        exit();
    }
}

if (isset($_POST["login"])) {
    try {
        $user = $authService->login(
            trim($_POST["email"] ?? ""),
            trim($_POST["password"] ?? "")
        );

        header("Location: ../views/home.php");
        exit();
    } catch (AuthenticationException|ValidationException $e) {
        header("Location: ../views/login.php?error=1");
        exit();
    }
}
