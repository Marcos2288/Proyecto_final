<?php
require_once "../config/bootstrap.php";

$authService = getAuthService();
$contentService = getContentService();
$ratingService = getRatingService();

if (!$authService->isAuthenticated()) {
    header("Location: ../views/login.php");
    exit();
}

if (isset($_POST["visto"])) {
    $episodioId = filter_input(INPUT_POST, "episodio_id", FILTER_VALIDATE_INT);
    $serieId = filter_input(INPUT_POST, "serie_id", FILTER_VALIDATE_INT);

    if ($episodioId && $serieId) {
        $contentService->markEpisodeAsWatched((int) $_SESSION["usuario"], $episodioId);
    }

    header("Location: ../views/content_detail.php?id=" . $serieId);
    exit();
}

if (isset($_POST["contenido_visto"])) {
    $contenidoId = filter_input(INPUT_POST, "contenido_id", FILTER_VALIDATE_INT);

    if ($contenidoId) {
        $contentService->markContentAsWatched((int) $_SESSION["usuario"], $contenidoId);
    }

    header("Location: ../views/content_detail.php?id=" . $contenidoId);
    exit();
}

if (isset($_POST["rate_content"])) {
    $contenidoId = filter_input(INPUT_POST, "contenido_id", FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, "rating", FILTER_VALIDATE_FLOAT);

    if ($contenidoId && $rating !== false && $rating >= 1 && $rating <= 10) {
        try {
            $ratingService->rateContent((int) $_SESSION["usuario"], $contenidoId, $rating);
        } catch (ValidationException $e) {
            // Ignorar la validación en la redirección.
        }
    }

    header("Location: ../views/content_detail.php?id=" . $contenidoId);
    exit();
}
