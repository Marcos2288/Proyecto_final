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

function validationRedirectStatus(ValidationException $e): string
{
    $errors = $e->getErrors();
    if (isset($errors['serie'])) {
        return 'not_series';
    }
    return 'missing';
}

if (isset($_POST["crear_contenido"])) {
    $titulo = trim($_POST["titulo"] ?? "");
    $tipo = trim($_POST["tipo"] ?? "");
    $imagen = trim($_POST["imagen"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");

    try {
        if ($titulo === '' || $tipo === '' || $imagen === '') {
            throw new ValidationException(['missing' => 'Faltan campos obligatorios']);
        }

        $contentService->createContent($titulo, $tipo, $imagen, $descripcion);
        header("Location: ../views/developer.php?status=content_ok");
        exit();
    } catch (ValidationException $e) {
        header("Location: ../views/developer.php?status=missing");
        exit();
    }
}

if (isset($_POST["crear_episodio"])) {
    $serieId = filter_input(INPUT_POST, "serie_id", FILTER_VALIDATE_INT);
    $temporada = filter_input(INPUT_POST, "temporada", FILTER_VALIDATE_INT);
    $numero = filter_input(INPUT_POST, "numero", FILTER_VALIDATE_INT);
    $titulo = trim($_POST["titulo"] ?? "");
    $imagen = trim($_POST["imagen"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");

    try {
        if (!$serieId || !$temporada || !$numero || $titulo === '' || $imagen === '') {
            throw new ValidationException(['missing' => 'Faltan campos obligatorios']);
        }

        $contentService->createEpisode($serieId, $temporada, $numero, $titulo, $imagen, $descripcion);
        header("Location: ../views/developer.php?status=episode_ok");
        exit();
    } catch (ValidationException $e) {
        header("Location: ../views/developer.php?status=" . validationRedirectStatus($e));
        exit();
    }
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

    try {
        if ($nombre === '' || $precio === false || $imagen === '' || $categoria === '') {
            throw new ValidationException(['missing' => 'Faltan campos obligatorios']);
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

        header("Location: ../views/developer.php?status=product_ok");
        exit();
    } catch (ValidationException $e) {
        header("Location: ../views/developer.php?status=missing");
        exit();
    }
}

$tipo = trim($_GET["tipo"] ?? "");

if ($tipo !== "") {
    $contenidos = $contentService->getContentByType($tipo);
} else {
    $contenidos = $contentService->getAllContentsOrderedByTitle();
}
