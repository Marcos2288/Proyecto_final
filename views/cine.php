<?php
require_once "../config/helpers.php";

$authService = getAuthService();
$contentService = getContentService();

if (!$authService->isAuthenticated()) {
    header("Location: login.php");
    exit();
}

$contenidos = $contentService->getAllContents();
$contenidos = array_reverse($contenidos);
$destacado = $contenidos[0] ?? null;
$recomendados = array_slice($contenidos, 0, 8);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cine | STREAM+</title>
<link rel="stylesheet" href="../public/style.css">
</head>

<body data-section="cine">

<?php include "partials/header.php"; ?>

<main class="cine-page">
    <section class="cinema-hero" style="<?php if ($destacado): ?>background-image: linear-gradient(90deg, rgba(8,8,10,0.94) 0%, rgba(8,8,10,0.62) 48%, rgba(8,8,10,0.2) 100%), url('<?= e($destacado["imagen"]) ?>');<?php endif; ?>">
        <div class="cinema-hero__content">
            <span class="eyebrow">Catalogo STREAM+</span>
            <h1><?= $destacado ? e($destacado["titulo"]) : "Cine y series" ?></h1>
            <p><?= $destacado ? e($destacado["descripcion"] ?? "Contenido destacado de la plataforma.") : "Explora el catalogo completo de peliculas y series." ?></p>
            <div class="hero-actions">
                <?php if ($destacado): ?>
                    <a class="btn btn-primary" href="content_detail.php?id=<?= e($destacado["id"]) ?>">Ver ahora</a>
                <?php endif; ?>
                <a class="btn btn-secondary" href="#contentGrid">Explorar catalogo</a>
            </div>
        </div>
    </section>

    <div class="page-shell">
        <section class="content-section">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Ultimos estrenos</span>
                    <h2>Tendencias</h2>
                </div>
                <span class="muted"><?= count($contenidos) ?> titulos</span>
            </div>

            <?php if ($contenidos): ?>
                <div class="content-grid media-grid" id="contentGrid">
                    <?php foreach ($contenidos as $c): ?>
                        <article class="media-card">
                            <a href="content_detail.php?id=<?= e($c["id"]) ?>">
                                <img src="<?= e($c["imagen"]) ?>" alt="<?= e($c["titulo"]) ?>">
                                <div class="media-card__body">
                                    <span class="pill"><?= e($c["tipo"] ?? "Contenido") ?></span>
                                    <h3><?= e($c["titulo"]) ?></h3>
                                    <?php if (!empty($c["avg_rating"]) && $c["avg_rating"] > 0): ?>
                                        <div class="stars">⭐ <?= number_format($c["avg_rating"], 1) ?>/10 (<?= $c["total_ratings"] ?>)</div>
                                    <?php endif; ?>
                                    <?php if (!empty($c["descripcion"])): ?>
                                        <p><?= e(excerpt($c["descripcion"])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state" id="contentGrid">
                    <h3>No hay contenidos publicados</h3>
                    <p>El catalogo aparecera aqui cuando se anadan peliculas o series.</p>
                </div>
            <?php endif; ?>
        </section>

        <?php if ($recomendados): ?>
            <section class="content-section">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">Seleccion rapida</span>
                        <h2>Recomendado para ti</h2>
                    </div>
                </div>

                <div class="poster-row">
                    <?php foreach ($recomendados as $c): ?>
                        <a href="content_detail.php?id=<?= e($c["id"]) ?>" class="poster-chip">
                            <img src="<?= e($c["imagen"]) ?>" alt="<?= e($c["titulo"]) ?>">
                            <span><?= e($c["titulo"]) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
