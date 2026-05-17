<?php
require_once "../config/helpers.php";

$authService = getAuthService();
$productService = getProductService();
$contentService = getContentService();

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    die("Producto no encontrado");
}

$p = $productService->getProductById($id);

if (!$p) {
    die("Producto no encontrado");
}

$antiSpoiler = !empty($_SESSION["modo_anti_spoiler"]) && $authService->isAuthenticated();
$contenidosVistos = [];

if ($antiSpoiler) {
    $usuarioId = (int) $_SESSION["usuario"];
    $contenidosVistos = $contentService->getViewedContentIds($usuarioId);

    if (!empty($p["contenido_id"]) && !isset($contenidosVistos[(int) $p["contenido_id"]])) {
        header("Location: shop.php");
        exit();
    }
}

$recomendados = $productService->getProductsByCategory($p["categoria"], $id);

if ($antiSpoiler) {
    $recomendados = array_values(array_filter($recomendados, function ($producto) use ($contenidosVistos) {
        return empty($producto["contenido_id"]) || isset($contenidosVistos[(int) $producto["contenido_id"]]);
    }));
    $recomendados = array_slice($recomendados, 0, 6);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($p["nombre"]) ?> | STREAM+</title>
<link rel="stylesheet" href="../public/style.css">
</head>

<body data-section="tienda">

<?php include "partials/header.php"; ?>

<main class="page-shell product-page">
    <section class="product-hero">
        <img src="<?= e($p["imagen"]) ?>" class="product-img" alt="<?= e($p["nombre"]) ?>">

        <div class="product-info">
            <span class="pill"><?= e($p["categoria"] ?? "Producto") ?></span>
            <h1><?= e($p["nombre"]) ?></h1>
            <div class="stars">
                <?php if (!empty($p["avg_rating"]) && $p["avg_rating"] > 0): ?>
                    ⭐ <?= number_format($p["avg_rating"], 1) ?>/10 (<?= $p["total_ratings"] ?> calificaciones)
                <?php else: ?>
                    Sin calificaciones aún
                <?php endif; ?>
            </div>
            <p class="price"><?= product_price_html($p["precio"]) ?></p>
            <p><?= e($p["descripcion"] ?? "") ?></p>
            <p class="details"><?= e($p["detalles"] ?? "") ?></p>
            <p><strong>Stock:</strong> <?= e($p["stock"] ?? 0) ?></p>

            <form action="../controllers/ShopController.php" method="POST">
                <input type="hidden" name="producto_id" value="<?= e($p["id"]) ?>">
                <button class="btn btn-primary" name="add" type="submit">Anadir al carrito</button>
            </form>

            <?php
            $usuario_id = $_SESSION["usuario"] ?? null;
            $puedeCalificar = $usuario_id ? $productService->hasUserPurchasedProduct($usuario_id, $p["id"]) : false;
            $userRating = $usuario_id ? $ratingService->getUserProductRating($usuario_id, $p["id"]) : null;
            ?>

            <?php if ($puedeCalificar): ?>
                <div class="rating-section">
                    <h3>Califica este producto</h3>
                    <form action="../controllers/ShopController.php" method="POST" class="rating-form">
                        <input type="hidden" name="producto_id" value="<?= e($p["id"]) ?>">
                        <div class="rating-input">
                            <label for="rating">Nota (1-10):</label>
                            <input type="number" id="rating" name="rating" min="1" max="10" step="0.1" value="<?= $userRating ?: '' ?>" required>
                        </div>
                        <button type="submit" name="rate_product" class="btn btn-primary">Calificar</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="content-section">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Tienda</span>
                <h2>Te puede interesar</h2>
            </div>
        </div>

        <?php if ($recomendados): ?>
            <div class="content-grid product-grid">
                <?php foreach ($recomendados as $r): ?>
                    <article class="product-card">
                        <a href="product_detail.php?id=<?= e($r["id"]) ?>">
                            <img src="<?= e($r["imagen"]) ?>" alt="<?= e($r["nombre"]) ?>">
                            <div class="product-card__body">
                                <h3><?= e($r["nombre"]) ?></h3>
                                <p class="product-price"><?= product_price_html($r["precio"]) ?></p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>Sin recomendaciones visibles</h3>
                <p>El modo anti spoiler puede ocultar productos de contenidos que todavia no has visto.</p>
            </div>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
