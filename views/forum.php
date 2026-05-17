<?php
require_once "../config/helpers.php";

$forumService = getForumService();
$temas = $forumService->getAllTopics();

$totalTemas = count($temas);
$totalRespuestas = array_sum(array_map(fn($tema) => (int) $tema["total_respuestas"], $temas));
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../public/style.css">
<title>Foro</title>
</head>

<body data-section="foro">

<?php include "partials/header.php"; ?>

<main class="forum-page">
    <section class="forum-hero">
        <div class="forum-hero__content">
            <span class="forum-kicker">Comunidad STREAM+</span>
            <h1>Foro de cine, series y recomendaciones</h1>
            <p>Comparte teorias, pide recomendaciones y abre conversaciones con otros usuarios de la plataforma.</p>
        </div>

        <div class="forum-stats" aria-label="Resumen del foro">
            <div>
                <strong><?= $totalTemas ?></strong>
                <span>Temas</span>
            </div>
            <div>
                <strong><?= $totalRespuestas ?></strong>
                <span>Respuestas</span>
            </div>
        </div>
    </section>

    <section class="forum-layout">
        <aside class="forum-compose">
            <div class="forum-panel">
                <span class="forum-panel__eyebrow">Nuevo debate</span>
                <h2>Crear tema</h2>
                <p>Abre una conversacion clara para que otros usuarios puedan entrar rapido.</p>

                <?php if (isset($_SESSION["usuario"])): ?>
                    <form action="../controllers/ForumControllers.php" method="POST" class="forum-form">
                        <label for="titulo">Titulo del tema</label>
                        <input id="titulo" type="text" name="titulo" placeholder="Ej. Mejor final de temporada" required maxlength="120">
                        <button name="crear" type="submit">Publicar tema</button>
                    </form>
                <?php else: ?>
                    <div class="forum-login-card">
                        <p>Inicia sesion para publicar un tema nuevo.</p>
                        <a href="login.php" class="forum-button">Iniciar sesion</a>
                    </div>
                <?php endif; ?>
            </div>
        </aside>

        <section class="forum-feed" aria-labelledby="forum-title">
            <div class="forum-section-head">
                <div>
                    <span class="forum-panel__eyebrow">Ultima actividad</span>
                    <h2 id="forum-title">Temas recientes</h2>
                </div>
                <span class="forum-count"><?= $totalTemas ?> publicados</span>
            </div>

            <?php if ($temas): ?>
                <div class="forum-topic-list">
                    <?php foreach ($temas as $t): ?>
                        <article class="forum-topic">
                            <a class="forum-topic__main" href="thread.php?id=<?= e($t["id"]) ?>">
                                <span class="forum-avatar"><?= e(strtoupper(substr($t["autor"] ?: "U", 0, 1))) ?></span>
                                <div>
                                    <h3><?= e($t["titulo"]) ?></h3>
                                    <p>
                                        Publicado por <?= e($t["autor"] ?: "Usuario") ?>
                                        <?php if (!empty($t["fecha"])): ?>
                                            - <?= e(date("d/m/Y H:i", strtotime($t["fecha"]))) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </a>

                            <div class="forum-topic__meta">
                                <strong><?= (int) $t["total_respuestas"] ?></strong>
                                <span>respuestas</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="forum-empty">
                    <h3>Todavia no hay temas</h3>
                    <p>Se el primero en abrir una conversacion para la comunidad.</p>
                </div>
            <?php endif; ?>
        </section>
    </section>
</main>

</body>
</html>
