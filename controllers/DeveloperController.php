<?php
require_once "../config/bootstrap.php";

$authService = getAuthService();
$contentService = getContentService();
$productService = getProductService();

if (!$authService->isAuthenticated()) {
    header("Location: ../views/login.php");
    exit();
}

if (!$authService->isDeveloper()) {
    header("Location: ../views/home.php");
    exit();
}

function redirectDev(string $status): void
{
    header("Location: ../views/developer.php?status=" . $status);
    exit();
}

function validationRedirectStatus(ValidationException $e): string
{
    $errors = $e->getErrors();
    if (isset($errors['serie'])) {
        return 'not_series';
    }
    return 'missing';
}

try {
    if (isset($_POST["crear_contenido"])) {
        $titulo = trim($_POST["titulo"] ?? "");
        $tipo = strtolower(trim($_POST["tipo"] ?? "serie"));
        $imagen = trim($_POST["imagen"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");

        if ($titulo === "" || $imagen === "" || !in_array($tipo, ["serie", "pelicula"], true)) {
            redirectDev("missing");
        }

        $contentService->createContent($titulo, $tipo, $imagen, $descripcion);
        redirectDev("content_ok");
    }

    if (isset($_POST["crear_episodio"])) {
        $serieId = filter_input(INPUT_POST, "serie_id", FILTER_VALIDATE_INT);
        $temporada = filter_input(INPUT_POST, "temporada", FILTER_VALIDATE_INT);
        $numero = filter_input(INPUT_POST, "numero", FILTER_VALIDATE_INT);
        $titulo = trim($_POST["titulo"] ?? "");
        $imagen = trim($_POST["imagen"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");

        if (!$serieId || !$temporada || !$numero || $titulo === "" || $imagen === "") {
            redirectDev("missing");
        }

        $contentService->createEpisode($serieId, $temporada, $numero, $titulo, $imagen, $descripcion);
        redirectDev("episode_ok");
    }

    if (isset($_POST["crear_producto"])) {
        $nombre = trim($_POST["nombre"] ?? "");
        $precio = filter_input(INPUT_POST, "precio", FILTER_VALIDATE_FLOAT);
        $imagen = trim($_POST["imagen"] ?? "");
        $categoria = trim($_POST["categoria"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");
        $detalles = trim($_POST["detalles"] ?? "");
        $stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);
        $rating = filter_input(INPUT_POST, "rating", FILTER_VALIDATE_FLOAT);
        $contenidoId = filter_input(INPUT_POST, "contenido_id", FILTER_VALIDATE_INT);
        $temporada = filter_input(INPUT_POST, "temporada", FILTER_VALIDATE_INT);

        if ($nombre === "" || $precio === false || $imagen === "") {
            redirectDev("missing");
        }

        $productService->createProduct(
            $nombre,
            $precio,
            $imagen,
            $categoria,
            $descripcion,
            $detalles,
            $stock ?: 0,
            $contenidoId ?: null,
            $temporada ?: null,
            $rating ?: 0.0
        );

        redirectDev("product_ok");
    }
} catch (ValidationException $e) {
    redirectDev(validationRedirectStatus($e));
} catch (Exception $e) {
    redirectDev("error");
}

redirectDev("idle");
