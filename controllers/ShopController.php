<?php
require_once "../config/bootstrap.php";

$cartService = getCartService();
$productService = getProductService();
$ratingService = getRatingService();
$authService = getAuthService();

function backToCart(): void
{
    header("Location: ../views/cart.php");
    exit();
}

if (isset($_POST["add"], $_POST["producto_id"])) {
    $productoId = (int) $_POST["producto_id"];

    if ($productoId > 0) {
        $cartService->addItem($productoId, 1);
        $_SESSION["cart_notice"] = "Producto añadido al carrito.";
    }

    backToCart();
}

if (isset($_POST["increment"], $_POST["producto_id"])) {
    $productoId = (int) $_POST["producto_id"];

    if ($productoId > 0) {
        $cartService->addItem($productoId, 1);
        $_SESSION["cart_notice"] = "Cantidad actualizada.";
    }

    backToCart();
}

if (isset($_POST["qty"], $_POST["producto_id"])) {
    $productoId = (int) $_POST["producto_id"];
    $qty = max(0, min(99, (int) $_POST["qty"]));
    $cartService->updateQuantity($productoId, $qty);
    $_SESSION["cart_notice"] = $qty > 0 ? "Cantidad actualizada." : "Producto eliminado.";
    backToCart();
}

if (isset($_POST["decrement"], $_POST["producto_id"])) {
    $productoId = (int) $_POST["producto_id"];
    $quantities = $cartService->getItemQuantities();
    $qty = max(0, ($quantities[$productoId] ?? 0) - 1);
    $cartService->updateQuantity($productoId, $qty);
    $_SESSION["cart_notice"] = "Cantidad actualizada.";
    backToCart();
}

if (isset($_POST["remove"], $_POST["producto_id"])) {
    $productoId = (int) $_POST["producto_id"];
    $cartService->removeItem($productoId);
    $_SESSION["cart_notice"] = "Producto eliminado.";
    backToCart();
}

if (isset($_POST["clear"])) {
    $cartService->clear();
    $_SESSION["cart_notice"] = "Carrito vaciado.";
    backToCart();
}

if (isset($_POST["apply_coupon"])) {
    $code = strtoupper(trim($_POST["coupon"] ?? ""));

    if ($code === "STREAM10") {
        $_SESSION["coupon"] = "STREAM10";
        $_SESSION["cart_notice"] = "Cupon STREAM10 aplicado.";
    } else {
        unset($_SESSION["coupon"]);
        $_SESSION["cart_notice"] = "Cupon no valido.";
    }

    backToCart();
}

if (isset($_POST["remove_coupon"])) {
    unset($_SESSION["coupon"]);
    $_SESSION["cart_notice"] = "Cupon eliminado.";
    backToCart();
}

if (isset($_POST["rate_product"])) {
    $productoId = filter_input(INPUT_POST, "producto_id", FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, "rating", FILTER_VALIDATE_FLOAT);

    if ($productoId && $rating !== false && $rating >= 1 && $rating <= 10 && $authService->isAuthenticated()) {
        try {
            $ratingService->rateProduct((int) $_SESSION["usuario"], $productoId, $rating);
        } catch (ValidationException $e) {
            // Ignorar la validación en la redirección.
        }
    }

    header("Location: ../views/product_detail.php?id=" . $productoId);
    exit();
}

if (isset($_POST["checkout"])) {
    if (!$authService->isAuthenticated()) {
        header("Location: ../views/login.php");
        exit();
    }

    try {
        $user = $authService->getCurrentUser();
        $cartService->validateCheckout();
        $items = $cartService->getCartWithDetails($user);

        if (!$items) {
            $_SESSION["cart_notice"] = "Tu carrito esta vacio.";
            backToCart();
        }

        $subtotal = PricingCalculator::calculateCartTotal($items, $user);
        $envio = $subtotal > 0 && $subtotal < 50 ? 4.99 : 0;
        $descuentoAuto = $subtotal >= 100 ? $subtotal * 0.1 : 0;
        $descuentoCupon = ($_SESSION["coupon"] ?? null) === "STREAM10" ? $subtotal * 0.1 : 0;
        $total = max(0, $subtotal + $envio - $descuentoAuto - $descuentoCupon);

        $cartService->checkout((int) $_SESSION["usuario"], $total);
        unset($_SESSION["coupon"]);
        $_SESSION["cart_notice"] = "Compra realizada correctamente.";
    } catch (Exception $e) {
        $_SESSION["cart_notice"] = "No se pudo finalizar la compra.";
    }

    backToCart();
}

header("Location: ../views/shop.php");
exit();
