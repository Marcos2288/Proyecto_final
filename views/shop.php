<?php
require_once "../config/helpers.php";

$authService = getAuthService();
$productService = getProductService();
$contentService = getContentService();

if (!$authService->isAuthenticated()) {
    header("Location: login.php");
    exit();
}

$productos = $productService->getAllProducts();
$antiSpoiler = !empty($_SESSION["modo_anti_spoiler"]);

if ($antiSpoiler) {
    $usuarioId = (int) $_SESSION["usuario"];
    $contenidosVistos = $contentService->getViewedContentIds($usuarioId);
    $maxSeasonsBySerie = $contentService->getMaxSeasonViewedPerContent($usuarioId);

    $productos = array_values(array_filter($productos, function ($producto) use ($contenidosVistos, $maxSeasonsBySerie) {
        if (empty($producto["contenido_id"])) {
            return true;
        }

        $contenidoId = (int) $producto["contenido_id"];
        if (!isset($contenidosVistos[$contenidoId])) {
            return false;
        }

        if (!empty($producto["temporada"])) {
            $temporadaProducto = (int) $producto["temporada"];
            $maxSeasonVisto = $maxSeasonsBySerie[$contenidoId] ?? 0;
            return $maxSeasonVisto >= $temporadaProducto;
        }

        return true;
    }));
}

$totalProductos = count($productos);
$precioMedio = $totalProductos
    ? array_sum(array_map(fn($p) => product_price((float) $p["precio"]), $productos)) / $totalProductos
    : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tienda | STREAM+</title>
<link rel="stylesheet" href="../public/style.css">
</head>

<body data-section="tienda">

<?php include "partials/header.php"; ?>

<main class="page-shell shop-page">
    <section class="app-hero app-hero--shop">
        <div class="app-hero__content">
            <span class="eyebrow">Tienda STREAM+</span>
            <h1>Productos para mejorar tu experiencia</h1>
            <p>Accesorios, tecnologia y articulos relacionados con tus contenidos favoritos.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="#contentGrid">Explorar productos</a>
                <a class="btn btn-secondary" href="cart.php">Ver carrito</a>
            </div>
        </div>

        <div class="hero-metrics" aria-label="Resumen de tienda">
            <div>
                <strong><?= $totalProductos ?></strong>
                <span>Productos</span>
            </div>
            <div>
                <strong><?= e(number_format($precioMedio, 2)) ?></strong>
                <span>Precio medio EUR</span>
            </div>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Catalogo</span>
                <h2>Destacados</h2>
            </div>
            <span class="muted"><?= $totalProductos ?> disponibles</span>
        </div>

        <?php if ($productos): ?>
            <div class="content-grid product-grid" id="contentGrid">
                <?php foreach ($productos as $p): ?>
                    <article class="product-card">
                        <a href="product_detail.php?id=<?= e($p["id"]) ?>">
                            <img src="<?= e($p["imagen"]) ?>" alt="<?= e($p["nombre"]) ?>">
                        </a>

                        <div class="product-card__body">
                            <span class="pill">Nuevo</span>
                            <h3><?= e($p["nombre"]) ?></h3>
                            <p class="product-price"><?= product_price_html($p["precio"]) ?></p>
                            <?php if (!empty($p["avg_rating"]) && $p["avg_rating"] > 0): ?>
                                <div class="stars">⭐ <?= number_format($p["avg_rating"], 1) ?>/10 (<?= $p["total_ratings"] ?>)</div>
                            <?php endif; ?>

                            <div class="card-actions">
                                <a class="btn btn-secondary" href="product_detail.php?id=<?= e($p["id"]) ?>">Ver mas</a>
                                <form action="../controllers/ShopController.php" method="POST">
                                    <input type="hidden" name="producto_id" value="<?= e($p["id"]) ?>">
                                    <button class="btn btn-primary" name="add" type="submit">Comprar</button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" id="contentGrid">
                <h3>No hay productos visibles</h3>
                <p>Con el modo anti spoiler activo solo aparecen productos de peliculas o series que hayas marcado como vistas.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
