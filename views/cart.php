<?php
require_once "../config/helpers.php";

$authService = getAuthService();
$productService = getProductService();
$contentService = getContentService();
$cartService = getCartService();

$carrito = array_values(array_filter(array_map('intval', $cartService->getCart())));
$cantidades = $cartService->getItemQuantities();
$productos = [];
$subtotal = 0;
$subtotalOriginal = 0;
$descuentoPremium = 0;
$totalItems = 0;
$notice = $_SESSION["cart_notice"] ?? null;
unset($_SESSION["cart_notice"]);

if ($cantidades) {
    $ids = array_keys($cantidades);
    $rows = $productService->getProductsByIds($ids);

    foreach ($rows as $row) {
        $precioOriginal = (float) $row["precio"];
        $precioFinal = product_price($precioOriginal);
        $row["cantidad"] = $cantidades[(int) $row["id"]] ?? 0;
        $row["precio_original"] = $precioOriginal;
        $row["precio_final"] = $precioFinal;
        $row["subtotal_original"] = $precioOriginal * $row["cantidad"];
        $row["subtotal"] = $precioFinal * $row["cantidad"];
        $subtotalOriginal += $row["subtotal_original"];
        $subtotal += $row["subtotal"];
        $descuentoPremium += premium_discount_amount($precioOriginal, $row["cantidad"]);
        $totalItems += $row["cantidad"];
        $productos[(int) $row["id"]] = $row;
    }
}

$envioGratisDesde = 50;
$envio = $subtotal > 0 && $subtotal < $envioGratisDesde ? 4.99 : 0;
$descuentoAuto = $subtotal >= 100 ? $subtotal * 0.1 : 0;
$coupon = $_SESSION["coupon"] ?? null;
$descuentoCupon = $coupon === "STREAM10" ? $subtotal * 0.1 : 0;
$descuento = $descuentoAuto + $descuentoCupon;
$descuentoTotal = $descuentoPremium + $descuento;
$total = max(0, $subtotal + $envio - $descuento);
$freeShippingProgress = $subtotal > 0 ? min(100, ($subtotal / $envioGratisDesde) * 100) : 0;

$recomendados = $productService->getAllProducts();
shuffle($recomendados);
$recomendados = array_slice($recomendados, 0, 12);

if (!empty($_SESSION["modo_anti_spoiler"]) && $authService->isAuthenticated()) {
    $usuarioId = (int) $_SESSION["usuario"];
    $contenidosVistos = $contentService->getViewedContentIds($usuarioId);

    $recomendados = array_values(array_filter($recomendados, function ($producto) use ($contenidosVistos) {
        return empty($producto["contenido_id"]) || isset($contenidosVistos[(int) $producto["contenido_id"]]);
    }));
}

$recomendados = array_slice($recomendados, 0, 4);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Carrito | STREAM+</title>
<link rel="stylesheet" href="../public/style.css">
</head>

<body data-section="tienda">

<?php include "partials/header.php"; ?>

<main class="page-shell cart-page">
    <section class="cart-hero">
        <div>
            <span class="eyebrow">Tienda STREAM+</span>
            <h1>Carrito</h1>
            <p>Revisa tus productos, ajusta cantidades y finaliza la compra con todo claro antes de pagar.</p>
            <div class="hero-actions">
                <a class="btn btn-secondary" href="shop.php">Seguir comprando</a>
            </div>
        </div>

        <div class="cart-stat">
            <strong><?= $totalItems ?></strong>
            <span><?= $totalItems === 1 ? "producto" : "productos" ?></span>
        </div>
    </section>

    <?php if ($notice): ?>
        <div class="cart-notice"><?= e($notice) ?></div>
    <?php endif; ?>

    <?php if ($productos): ?>
        <section class="cart-layout">
            <div class="cart-list" aria-label="Productos en el carrito">
                <?php foreach ($productos as $p): ?>
                    <article class="cart-item">
                        <a class="cart-item__image" href="product_detail.php?id=<?= e($p["id"]) ?>">
                            <img src="<?= e($p["imagen"]) ?>" alt="<?= e($p["nombre"]) ?>">
                        </a>

                        <div class="cart-item__body">
                            <span class="pill"><?= e($p["categoria"] ?? "Producto") ?></span>
                            <h3><?= e($p["nombre"]) ?></h3>
                            <p><?= e($p["descripcion"] ?? "") ?></p>
                            <span class="cart-price"><?= product_price_html($p["precio"]) ?></span>
                        </div>

                        <div class="cart-actions">
                            <form class="qty-form" action="../controllers/ShopController.php" method="POST">
                                <input type="hidden" name="producto_id" value="<?= e($p["id"]) ?>">
                                <button name="decrement" type="submit" aria-label="Restar">-</button>
                                <input type="number" name="qty" min="0" max="99" value="<?= e($p["cantidad"]) ?>" aria-label="Cantidad">
                                <button name="increment" type="submit" aria-label="Sumar">+</button>
                                <button class="qty-update" type="submit">Actualizar</button>
                            </form>

                            <form action="../controllers/ShopController.php" method="POST">
                                <input type="hidden" name="producto_id" value="<?= e($p["id"]) ?>">
                                <button class="cart-remove" name="remove" type="submit">Eliminar</button>
                            </form>

                            <strong><?= e(number_format((float) $p["subtotal"], 2)) ?> EUR</strong>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <aside class="cart-summary" aria-label="Resumen del pedido">
                <h2>Resumen</h2>
                <div class="summary-row">
                    <span>Subtotal productos</span>
                    <strong><?= e(number_format($subtotalOriginal, 2)) ?> EUR</strong>
                </div>
                <div class="summary-row">
                    <span>Descuento Premium 5%</span>
                    <strong><?= $descuentoPremium ? "-".e(number_format($descuentoPremium, 2))." EUR" : "0.00 EUR" ?></strong>
                </div>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong><?= e(number_format($subtotal, 2)) ?> EUR</strong>
                </div>
                <div class="summary-row">
                    <span>Envio</span>
                    <strong><?= $envio ? e(number_format($envio, 2))." EUR" : "Gratis" ?></strong>
                </div>
                <div class="summary-row">
                    <span>Descuento automatico</span>
                    <strong><?= $descuentoAuto ? "-".e(number_format($descuentoAuto, 2))." EUR" : "0.00 EUR" ?></strong>
                </div>
                <div class="summary-row">
                    <span>Cupon <?= $coupon ? e($coupon) : "" ?></span>
                    <strong><?= $descuentoCupon ? "-".e(number_format($descuentoCupon, 2))." EUR" : "0.00 EUR" ?></strong>
                </div>
                <div class="shipping-progress">
                    <div>
                        <span style="width: <?= e($freeShippingProgress) ?>%"></span>
                    </div>
                    <p>
                        <?= $envio ? "Te faltan ".e(number_format($envioGratisDesde - $subtotal, 2))." EUR para envio gratis." : "Envio gratis desbloqueado." ?>
                    </p>
                </div>

                <form class="coupon-form" action="../controllers/ShopController.php" method="POST">
                    <input type="text" name="coupon" placeholder="Codigo: STREAM10" value="<?= e($coupon ?? "") ?>">
                    <button name="apply_coupon" type="submit">Aplicar</button>
                    <?php if ($coupon): ?>
                        <button class="cart-remove" name="remove_coupon" type="submit">Quitar</button>
                    <?php endif; ?>
                </form>

                <div class="summary-row">
                    <span>Descuento total</span>
                    <strong><?= $descuentoTotal ? "-".e(number_format($descuentoTotal, 2))." EUR" : "0.00 EUR" ?></strong>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <strong><?= e(number_format($total, 2)) ?> EUR</strong>
                </div>

                <div class="summary-actions">
                    <form action="../controllers/ShopController.php" method="POST">
                        <button class="btn btn-primary" name="checkout" type="submit">Finalizar compra</button>
                    </form>
                    <form action="../controllers/ShopController.php" method="POST">
                        <button class="cart-clear" name="clear" type="submit">Vaciar carrito</button>
                    </form>
                </div>
            </aside>
        </section>
    <?php else: ?>
        <section class="cart-list cart-empty">
            <h2>Tu carrito esta vacio</h2>
            <p>Cuando anadas productos desde la tienda apareceran aqui con cantidades, totales y opciones de compra.</p>
            <a class="btn btn-primary" href="shop.php">Explorar tienda</a>
        </section>
    <?php endif; ?>

    <?php if ($recomendados): ?>
        <section class="cart-suggestions">
            <h2>Tambien puede interesarte</h2>
            <div class="content-grid product-grid">
                <?php foreach ($recomendados as $r): ?>
                    <article class="product-card">
                        <a href="product_detail.php?id=<?= e($r["id"]) ?>">
                            <img src="<?= e($r["imagen"]) ?>" alt="<?= e($r["nombre"]) ?>">
                            <div class="product-card__body">
                                <h3><?= e($r["nombre"]) ?></h3>
                                <p class="product-price"><?= product_price_html($r["precio"]) ?></p>
                                <?php if (!empty($r["avg_rating"]) && $r["avg_rating"] > 0): ?>
                                    <div class="stars">⭐ <?= number_format($r["avg_rating"], 1) ?>/10 (<?= $r["total_ratings"] ?>)</div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

</body>
</html>
