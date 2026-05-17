<?php
require_once "../config/helpers.php";

$authService = getAuthService();
$contentService = getContentService();

if (!$authService->isAuthenticated() || !$authService->isDeveloper()) {
    header("Location: login.php");
    exit();
}

$contenidos = $contentService->getAllContents();
$series = $contentService->getContentByType('serie');
$status = $_GET["status"] ?? "";
$messages = [
    "content_ok" => "Contenido anadido correctamente.",
    "episode_ok" => "Episodio anadido correctamente.",
    "product_ok" => "Producto anadido correctamente.",
    "missing" => "Faltan campos obligatorios.",
    "not_series" => "Solo puedes anadir episodios a contenidos de tipo serie.",
    "error" => "No se pudo guardar. Revisa que la base de datos tenga esas columnas."
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Desarrollador | STREAM+</title>
<link rel="stylesheet" href="../public/style.css">
</head>

<body data-section="developer">

<?php include "partials/header.php"; ?>

<main class="page-shell developer-page">
    <section class="developer-hero">
        <div>
            <span class="eyebrow">Panel interno</span>
            <h1>Desarrollador</h1>
            <p>Anade peliculas, series, episodios y productos sin tocar la base de datos manualmente.</p>
        </div>
    </section>

    <?php if (isset($messages[$status])): ?>
        <div class="dev-alert"><?= e($messages[$status]) ?></div>
    <?php endif; ?>

    <section class="developer-grid">
        <article class="dev-panel">
            <span class="eyebrow">Catalogo</span>
            <h2>Nueva pelicula o serie</h2>
            <form action="../controllers/DeveloperController.php" method="POST" class="dev-form">
                <label>Titulo<input type="text" name="titulo" required></label>
                <label>Tipo
                    <select name="tipo">
                        <option value="serie">Serie</option>
                        <option value="pelicula">Pelicula</option>
                    </select>
                </label>
                <label>URL imagen<input type="url" name="imagen" required></label>
                <label>Descripcion<textarea name="descripcion" rows="5"></textarea></label>
                <button class="btn btn-primary" name="crear_contenido" type="submit">Guardar contenido</button>
            </form>
        </article>

        <article class="dev-panel">
            <span class="eyebrow">Series</span>
            <h2>Nuevo episodio</h2>
            <form action="../controllers/DeveloperController.php" method="POST" class="dev-form">
                <label>Serie
                    <select name="serie_id" required>
                        <?php foreach ($series as $contenido): ?>
                            <option value="<?= e($contenido["id"]) ?>"><?= e($contenido["titulo"]) ?> - <?= e($contenido["tipo"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div class="dev-inline">
                    <label>Temporada<input type="number" name="temporada" value="1" min="1" required></label>
                    <label>Episodio<input type="number" name="numero" value="1" min="1" required></label>
                </div>
                <label>Titulo<input type="text" name="titulo" required></label>
                <label>URL imagen<input type="url" name="imagen" required></label>
                <label>Descripcion<textarea name="descripcion" rows="5"></textarea></label>
                <button class="btn btn-primary" name="crear_episodio" type="submit" <?= $series ? "" : "disabled" ?>>Guardar episodio</button>
            </form>
        </article>

        <article class="dev-panel">
            <span class="eyebrow">Tienda</span>
            <h2>Nuevo producto</h2>
            <form action="../controllers/DeveloperController.php" method="POST" class="dev-form">
                <label>Nombre<input type="text" name="nombre" required></label>
                <div class="dev-inline">
                    <label>Precio<input type="number" name="precio" min="0" step="0.01" required></label>
                    <label>Stock<input type="number" name="stock" min="0" value="0"></label>
                </div>
                <label>URL imagen<input type="url" name="imagen" required></label>
                <label>Categoria<input type="text" name="categoria"></label>
                <label>Relacionado con
                    <select name="contenido_id" id="contenido_select">
                        <option value="">Sin relacion</option>
                        <?php foreach ($contenidos as $contenido): ?>
                            <option value="<?= e($contenido["id"]) ?>" data-tipo="<?= e($contenido["tipo"]) ?>"><?= e($contenido["titulo"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label id="temporada_label" style="display:none;">Temporada (si aplica)<input type="number" name="temporada" id="temporada_input" min="1" value="1"></label>
                <label>Descripcion<textarea name="descripcion" rows="4"></textarea></label>
                <label>Detalles<textarea name="detalles" rows="4"></textarea></label>
                <button class="btn btn-primary" name="crear_producto" type="submit">Guardar producto</button>
            </form>
        </article>
    </section>

    <script>
        const contenidoSelect = document.getElementById('contenido_select');
        const temporadaLabel = document.getElementById('temporada_label');
        const temporadaInput = document.getElementById('temporada_input');

        function updateTemporadaField() {
            const selectedOption = contenidoSelect.options[contenidoSelect.selectedIndex];
            const isSerie = selectedOption.dataset.tipo === 'serie';
            temporadaLabel.style.display = isSerie ? 'block' : 'none';
            if (!isSerie) {
                temporadaInput.value = '';
            }
        }

        contenidoSelect.addEventListener('change', updateTemporadaField);
    </script>
</main>

</body>
</html>
