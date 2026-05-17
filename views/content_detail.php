<?php
require_once "../config/helpers.php";

$authService = getAuthService();
$contentService = getContentService();
$ratingService = getRatingService();

$usuario_id = $_SESSION["usuario"] ?? null;

if (!$usuario_id) {
    header("Location: login.php");
    exit();
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    die("Contenido no encontrado");
}

$c = $contentService->getContentById($id);

if (!$c) {
    die("Contenido no encontrado");
}

$temporadas = $contentService->getSeasons($id);
$temporadaSeleccionada = (int) ($_GET["season"] ?? ($temporadas[0] ?? 1));
$episodios = $contentService->getEpisodes($id, $temporadaSeleccionada);

$usuarioId = (int) $usuario_id;
$contenidosVistos = $contentService->getViewedContentIds($usuarioId);
$episodiosVistosIds = $contentService->getWatchedEpisodeIds($usuarioId, $id);
$antiSpoiler = !empty($_SESSION["modo_anti_spoiler"]);
$contenidoVisto = isset($contenidosVistos[(int) $c["id"]]);
$esPelicula = strtolower((string) ($c["tipo"] ?? "")) === "pelicula";

$totalEpisodios = count($episodios);
$episodiosVistos = 0;
foreach ($episodios as $episodio) {
    if (in_array((int) $episodio["id"], $episodiosVistosIds, true)) {
        $episodiosVistos++;
    }
}

$progreso = $totalEpisodios > 0 ? round(($episodiosVistos / $totalEpisodios) * 100) : 0;
$userRating = $contenidoVisto ? $ratingService->getUserContentRating($usuarioId, $id) : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($c["titulo"]) ?> | STREAM+</title>
<link rel="stylesheet" href="../public/style.css">
</head>

<body data-section="cine">

<?php include "partials/header.php"; ?>

<main class="detail-page">
    <section class="detail-hero" style="background-image: linear-gradient(90deg, rgba(9,10,14,0.96), rgba(9,10,14,0.72) 45%, rgba(9,10,14,0.22)), url('<?= e($c["imagen"]) ?>');">
        <div class="detail-hero__poster">
            <img src="<?= e($c["imagen"]) ?>" alt="<?= e($c["titulo"]) ?>">
        </div>

        <div class="detail-hero__content">
            <a class="detail-back" href="cine.php">Volver al catalogo</a>
            <span class="pill"><?= e($c["tipo"] ?? "Contenido") ?></span>
            <?php if (!empty($c["avg_rating"]) && $c["avg_rating"] > 0): ?>
                <div class="stars">⭐ <?= number_format($c["avg_rating"], 1) ?>/10 (<?= $c["total_ratings"] ?> calificaciones)</div>
            <?php endif; ?>
            <h1><?= e($c["titulo"]) ?></h1>
            <p><?= e($c["descripcion"] ?? "Sin descripcion disponible.") ?></p>

            <div class="detail-actions">
                <?php if (!$esPelicula): ?>
                    <a class="btn btn-primary" href="#episodes"><?= $totalEpisodios ? "Ver episodios" : "Ver informacion" ?></a>
                <?php endif; ?>
                <a class="btn btn-secondary" href="forum.php">Comentar en foro</a>
                <?php if (!$contenidoVisto || $episodiosVistos < $totalEpisodios): ?>
                    <form action="../controllers/EpisodeController.php" method="POST" class="inline-form">
                        <input type="hidden" name="contenido_id" value="<?= e($c["id"]) ?>">
                        <button class="btn btn-secondary" name="contenido_visto" type="submit">
                            Marcar <?= $totalEpisodios ? "serie" : "pelicula" ?> como vista
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if ($contenidoVisto): ?>
                <div class="rating-section">
                    <h3>Califica este contenido</h3>
                    <form action="../controllers/EpisodeController.php" method="POST" class="rating-form">
                        <input type="hidden" name="contenido_id" value="<?= e($c["id"]) ?>">
                        <div class="rating-input">
                            <label for="rating">Nota (1-10):</label>
                            <input type="number" id="rating" name="rating" min="1" max="10" step="0.1" value="<?= $userRating ?: '' ?>" required>
                        </div>
                        <button type="submit" name="rate_content" class="btn btn-primary">Calificar</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!$esPelicula): ?>
    <section class="page-shell detail-body">
        <div class="detail-summary">
            <article>
                <span class="eyebrow">Tipo</span>
                <strong><?= e($c["tipo"] ?? "Contenido") ?></strong>
            </article>
            <article>
                <span class="eyebrow">Episodios</span>
                <strong><?= $totalEpisodios ?></strong>
            </article>
            <article>
                <span class="eyebrow">Progreso</span>
                <strong><?= $progreso ?>%</strong>
            </article>
        </div>

        <section class="content-section" id="episodes">
            <div class="section-heading">
                <div>
                    <span class="eyebrow"><?= $totalEpisodios ? "Temporadas" : "Detalle" ?></span>
                    <h2><?= $totalEpisodios ? "Episodios" : "Informacion" ?></h2>
                </div>
                <?php if ($totalEpisodios): ?>
                    <span class="muted"><?= $episodiosVistos ?> de <?= $totalEpisodios ?> vistos</span>
                <?php endif; ?>
            </div>

            <?php if (count($temporadas) > 1): ?>
            <div class="season-selector">
                <form method="GET" action="content_detail.php" class="season-form">
                    <input type="hidden" name="id" value="<?= e($id) ?>">
                    <label for="season-select">Selecciona temporada:</label>
                    <select id="season-select" name="season" onchange="this.form.submit()">
                        <?php foreach ($temporadas as $t): ?>
                            <option value="<?= e($t) ?>" <?= $t === $temporadaSeleccionada ? "selected" : "" ?>>
                                Temporada <?= e($t) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <?php endif; ?>

            <?php if ($episodios): ?>
                <div class="episodes-grid detail-episodes">
                    <?php foreach ($episodios as $e): ?>
                        <?php
                            $visto = in_array((int) $e["id"], $episodiosVistosIds, true);
                            $oculto = $antiSpoiler && !$visto;
                            $cardClass = $visto ? "is-seen" : ($oculto ? "is-locked" : "");
                        ?>

                        <article class="episode-card <?= e($cardClass) ?>">
                            <div class="episode-thumb">
                                <img
                                    class="<?= $oculto ? "episode-image--locked" : "" ?>"
                                    src="<?= e($e["imagen"]) ?>"
                                    alt="<?= $oculto ? "Episodio no visto" : e($e["titulo"]) ?>"
                                >
                                <?php if ($visto): ?>
                                    <span class="episode-lock episode-lock--seen">Visto</span>
                                <?php else: ?>
                                    <span class="episode-lock">No visto</span>
                                <?php endif; ?>
                            </div>

                            <div class="episode-info">
                                <span class="episode-number">T<?= e($e["temporada"]) ?> E<?= e($e["numero"]) ?></span>
                                <h3><?= $oculto ? "????????" : e($e["titulo"]) ?></h3>
                                <p><?= $oculto ? "Marca el episodio como visto para revelar la sinopsis." : e($e["descripcion"]) ?></p>

                                <?php if (!$visto): ?>
                                    <form action="../controllers/EpisodeController.php" method="POST">
                                        <input type="hidden" name="episodio_id" value="<?= e($e["id"]) ?>">
                                        <input type="hidden" name="serie_id" value="<?= e($c["id"]) ?>">
                                        <button class="btn btn-primary" name="visto" type="submit">Marcar como visto</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="detail-panel">
                    <h3>Sin episodios asociados</h3>
                    <p>Este contenido se muestra como pelicula o especial. La informacion principal esta disponible en la cabecera.</p>
                </div>
            <?php endif; ?>
        </section>
    </section>
    <?php endif; ?>
</main>

</body>
</html>
