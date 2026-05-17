<?php
require_once "../config/bootstrap.php";

$authService = getAuthService();
$forumService = getForumService();

if (!$authService->isAuthenticated()) {
    header("Location: ../views/login.php");
    exit;
}

if (isset($_POST["crear"])) {
    $titulo = trim($_POST["titulo"] ?? "");

    try {
        $forumService->createTopic((int) $_SESSION["usuario"], $titulo);
        header("Location: ../views/forum.php");
        exit;
    } catch (ValidationException $e) {
        header("Location: ../views/forum.php");
        exit;
    }
}

if (isset($_POST["responder"])) {
    $temaId = filter_input(INPUT_POST, "tema_id", FILTER_VALIDATE_INT);
    $texto = trim($_POST["texto"] ?? "");

    if (!$temaId) {
        header("Location: ../views/forum.php");
        exit;
    }

    try {
        $forumService->replyTopic($temaId, (int) $_SESSION["usuario"], $texto);
        header("Location: ../views/thread.php?id=" . $temaId);
        exit;
    } catch (ValidationException $e) {
        header("Location: ../views/forum.php");
        exit;
    }
}
