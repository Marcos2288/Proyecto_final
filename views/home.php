<?php
require_once "../config/helpers.php";

$authService = getAuthService();
$contentService = getContentService();
$productService = getProductService();

$novedades = $contentService->getAllContents();
$novedades = array_slice($novedades, 0, 6);
$recomendados = $contentService->getRandomContents(6);
$tienda = $productService->getAllProducts();
$tienda = array_slice($tienda, 0, 6);
$destacado = $novedades[0] ?? null;

if (!empty($_SESSION["modo_anti_spoiler"]) && $authService->isAuthenticated()) {
    $contenidosVistos = $contentService->getViewedContentIds((int) $_SESSION["usuario"]);
    $tienda = array_values(array_filter($tienda, function ($producto) use ($contenidosVistos) {
        return !empty($producto["contenido_id"])
            && isset($contenidosVistos[(int) $producto["contenido_id"]]);
    }));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inicio | STREAM+</title>
<link rel="stylesheet" href="../public/style.css">
</head>

<body data-section="home">

<?php include "partials/header.php"; ?>

<main class="page-shell home-page">
    <section class="app-hero app-hero--home">
        <div class="app-hero__content">
            <span class="eyebrow">STREAM+</span>
            <h1>Todo tu entretenimiento en un solo sitio</h1>
            <p>Descubre peliculas, series, debates y productos recomendados para seguir disfrutando despues de cada capitulo.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="cine.php">Ver catalogo</a>
                <a class="btn btn-secondary" href="shop.php">Ir a la tienda</a>
            </div>
        </div>

        <?php if ($destacado): ?>
            <a class="hero-feature" href="content_detail.php?id=<?= e($destacado["id"]) ?>">
                <img src="<?= e($destacado["imagen"]) ?>" alt="<?= e($destacado["titulo"]) ?>">
                <div>
                    <span>Destacado</span>
                    <strong><?= e($destacado["titulo"]) ?></strong>
                </div>
            </a>
        <?php endif; ?>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Recien llegado</span>
                <h2>Novedades</h2>
            </div>
            <a href="cine.php">Ver todo</a>
        </div>

        <div class="content-grid">
            <?php foreach ($novedades as $n): ?>
                <article class="media-card">
                    <a href="content_detail.php?id=<?= e($n["id"]) ?>">
                        <img src="<?= e($n["imagen"]) ?>" alt="<?= e($n["titulo"]) ?>">
                        <div class="media-card__body">
                            <span class="pill">Nuevo</span>
                            <h3><?= e($n["titulo"]) ?></h3>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Para continuar</span>
                <h2>Recomendado para ti</h2>
            </div>
        </div>

        <div class="content-grid">
            <?php foreach ($recomendados as $r): ?>
                <article class="media-card">
                    <a href="content_detail.php?id=<?= e($r["id"]) ?>">
                        <img src="<?= e($r["imagen"]) ?>" alt="<?= e($r["titulo"]) ?>">
                        <div class="media-card__body">
                            <h3><?= e($r["titulo"]) ?></h3>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Tienda</span>
                <h2>Tendencias en productos</h2>
            </div>
            <a href="shop.php">Comprar</a>
        </div>

        <div class="content-grid product-grid">
            <?php foreach ($tienda as $t): ?>
                <article class="product-card">
                    <a href="product_detail.php?id=<?= e($t["id"]) ?>">
                        <img src="<?= e($t["imagen"]) ?>" alt="<?= e($t["nombre"]) ?>">
                        <div class="product-card__body">
                            <span class="pill">Oferta</span>
                            <h3><?= e($t["nombre"]) ?></h3>
                            <p class="product-price"><?= product_price_html($t["precio"]) ?></p>
                            <?php if (!empty($t["avg_rating"]) && $t["avg_rating"] > 0): ?>
                                <div class="stars">⭐ <?= number_format($t["avg_rating"], 1) ?>/10 (<?= $t["total_ratings"] ?>)</div>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

</body>
</html>
