<?php
require_once "../config/helpers.php";

$contentService = getContentService();
$productService = getProductService();
$authService = getAuthService();

$q = trim($_GET["q"] ?? "");

if ($q === "") {
    header("Location: home.php");
    exit();
}

$contenidos = $contentService->searchContentsByTitle($q);
$episodios = $contentService->searchEpisodes($q);
$productos = $productService->searchProductsByName($q);

if (!empty($_SESSION["modo_anti_spoiler"]) && $authService->isAuthenticated()) {
    $usuarioId = (int) $_SESSION["usuario"];
    $contenidosVistos = $contentService->getViewedContentIds($usuarioId);
    $productos = array_values(array_filter($productos, function ($producto) use ($contenidosVistos) {
        return empty($producto["contenido_id"]) || isset($contenidosVistos[(int) $producto["contenido_id"]]);
    }));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Busqueda | STREAM+</title>
<link rel="stylesheet" href="../public/style.css">
</head>

<body>

<?php include "partials/header.php"; ?>

<main class="page-shell">
    <section class="content-section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Busqueda</span>
                <h1>Resultados para "<?= e($q) ?>"</h1>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <h2>Peliculas y series</h2>
            <span class="muted"><?= count($contenidos) ?> resultados</span>
        </div>

        <div class="content-grid">
            <?php foreach ($contenidos as $c): ?>
                <article class="media-card">
                    <a href="content_detail.php?id=<?= e($c["id"]) ?>">
                        <img src="<?= e($c["imagen"]) ?>" alt="<?= e($c["titulo"]) ?>">
                        <div class="media-card__body">
                            <span class="pill"><?= e($c["tipo"] ?? "Contenido") ?></span>
                            <h3><?= e($c["titulo"]) ?></h3>
                            <p><?= e(excerpt($c["descripcion"] ?? "")) ?></p>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <h2>Episodios</h2>
            <span class="muted"><?= count($episodios) ?> resultados</span>
        </div>

        <div class="content-grid">
            <?php foreach ($episodios as $e): ?>
                <article class="media-card">
                    <img src="<?= e($e["imagen"]) ?>" alt="<?= e($e["titulo"]) ?>">
                    <div class="media-card__body">
                        <span class="pill"><?= e($e["serie"]) ?></span>
                        <h3><?= e($e["titulo"]) ?></h3>
                        <p><?= e(excerpt($e["descripcion"] ?? "")) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <h2>Tienda</h2>
            <span class="muted"><?= count($productos) ?> resultados</span>
        </div>

        <div class="content-grid product-grid">
            <?php foreach ($productos as $p): ?>
                <article class="product-card">
                    <a href="product_detail.php?id=<?= e($p["id"]) ?>">
                        <img src="<?= e($p["imagen"]) ?>" alt="<?= e($p["nombre"]) ?>">
                        <div class="product-card__body">
                            <h3><?= e($p["nombre"]) ?></h3>
                            <p class="product-price"><?= product_price_html($p["precio"]) ?></p>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

</body>
</html>
