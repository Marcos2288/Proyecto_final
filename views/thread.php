<?php
require_once "../config/helpers.php";

$forumService = getForumService();

$temaId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$temaId) {
    header("Location: forum.php");
    exit;
}

$tema = $forumService->getTopicById($temaId);
if (!$tema) {
    header("Location: forum.php");
    exit;
}

$respuestas = $forumService->getRepliesByTopic($temaId);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../public/style.css">
<title><?= e($tema["titulo"]) ?> - Foro</title>
</head>

<body data-section="foro">

<?php include "partials/header.php"; ?>

<main class="forum-page reddit-thread-page">
    <a href="forum.php" class="forum-back">Volver al foro</a>

    <article class="reddit-post">
        <aside class="reddit-vote" aria-label="Actividad">
            <strong><?= count($respuestas) ?></strong>
            <span>respuestas</span>
        </aside>

        <div class="reddit-post__body">
            <div class="reddit-meta">
                <span class="forum-avatar"><?= e(strtoupper(substr($tema["autor"] ?: "U", 0, 1))) ?></span>
                <div>
                    <strong><?= e($tema["autor"] ?: "Usuario") ?></strong>
                    <span>
                        Publicado
                        <?php if (!empty($tema["fecha"])): ?>
                            <?= e(date("d/m/Y H:i", strtotime($tema["fecha"]))) ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <h1><?= e($tema["titulo"]) ?></h1>
        </div>
    </article>

    <section class="reddit-reply-box">
        <?php if (isset($_SESSION["usuario"])): ?>
            <form action="../controllers/ForumControllers.php" method="POST" class="forum-form reddit-form">
                <input type="hidden" name="tema_id" value="<?= e($temaId) ?>">
                <label for="texto">Anadir una respuesta</label>
                <textarea id="texto" name="texto" placeholder="Que opinas?" required maxlength="1200"></textarea>
                <button name="responder" type="submit">Responder</button>
            </form>
        <?php else: ?>
            <div class="forum-login-card">
                <p>Inicia sesion para responder a este tema.</p>
                <a href="login.php" class="forum-button">Iniciar sesion</a>
            </div>
        <?php endif; ?>
    </section>

    <section class="reddit-comments" aria-label="Respuestas">
        <div class="reddit-comments__head">
            <h2><?= count($respuestas) ?> respuestas</h2>
        </div>

        <?php if ($respuestas): ?>
            <?php foreach ($respuestas as $index => $respuesta): ?>
                <article class="reddit-comment">
                    <aside class="reddit-comment__rail">
                        <span class="forum-avatar"><?= e(strtoupper(substr($respuesta["autor"] ?: "U", 0, 1))) ?></span>
                    </aside>

                    <div class="reddit-comment__body">
                        <div class="reddit-comment__meta">
                            <strong><?= e($respuesta["autor"] ?: "Usuario") ?></strong>
                            <span>Respuesta #<?= $index + 1 ?></span>
                        </div>
                        <p><?= nl2br(e($respuesta["texto"])) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="forum-empty">
                <h3>Sin respuestas todavia</h3>
                <p>Esta conversacion acaba de empezar. Puedes ser el primero en responder.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
