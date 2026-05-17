<?php
require_once __DIR__ . "/../config/helpers.php";

$contentService = getContentService();
$productService = getProductService();
$authService = getAuthService();

$q = $_GET["q"] ?? "";
$section = $_GET["section"] ?? "";
$like = "%" . $q . "%";
$data = [];

if ($section === "cine") {
    $items = $contentService->searchContentsByTitle($like);

    foreach ($items as $item) {
        $data[] = [
            "html" => '
        <article class="media-card">
            <a href="content_detail.php?id='.e($item["id"]).'">
                <img src="'.e($item["imagen"]).'" alt="'.e($item["titulo"]).'">
                <div class="media-card__body">
                    <span class="pill">'.e($item["tipo"] ?? "Contenido").'</span>
                    <h3>'.e($item["titulo"]).'</h3>
                    <p>'.e(excerpt($item["descripcion"] ?? "")).'</p>
                </div>
            </a>
        </article>'
        ];
    }
} elseif ($section === "tienda") {
    $antiSpoiler = !empty($_SESSION["modo_anti_spoiler"]) && $authService->isAuthenticated();
    $items = $productService->searchProductsByName($like);

    if ($antiSpoiler) {
        $contenidosVistos = $contentService->getViewedContentIds((int) $_SESSION["usuario"]);
        $items = array_values(array_filter($items, function ($producto) use ($contenidosVistos) {
            return !empty($producto["contenido_id"]) && isset($contenidosVistos[(int) $producto["contenido_id"]]);
        }));
    }

    foreach ($items as $item) {
        $data[] = [
            "html" => '
        <article class="product-card">
            <a href="product_detail.php?id='.e($item["id"]).'">
                <img src="'.e($item["imagen"]).'" alt="'.e($item["nombre"]).'">
            </a>
            <div class="product-card__body">
                <span class="pill">Producto</span>
                <h3>'.e($item["nombre"]).'</h3>
                <p class="product-price">'.product_price_html($item["precio"]).'</p>
                <div class="card-actions">
                    <a class="btn btn-secondary" href="product_detail.php?id='.e($item["id"]).'">Ver mas</a>
                    <form action="../controllers/ShopController.php" method="POST">
                        <input type="hidden" name="producto_id" value="'.e($item["id"]).'">
                        <button class="btn btn-primary" name="add" type="submit">Comprar</button>
                    </form>
                </div>
            </div>
        </article>'
        ];
    }
}

echo json_encode($data);
