<?php
require_once "../config/helpers.php";

$authService = getAuthService();

if (!$authService->isAuthenticated()) {
    header("Location: login.php");
    exit();
}

$user = $authService->getCurrentUser() ?: [
    "nombre" => "Usuario Demo",
    "email" => "demo@stream.com",
    "tipo_plan" => "basico"
];

$usuarioId = (int) $_SESSION["usuario"];
$antiSpoiler = !empty($_SESSION["modo_anti_spoiler"]);
$planLabel = plan_label($user["tipo_plan"] ?? "basico");
$compras = $authService->getPurchaseHistory($usuarioId);
$historial = $authService->getViewedHistory($usuarioId);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil | STREAM+</title>
<link rel="stylesheet" href="../public/style.css">
</head>

<body data-section="profile">

<?php include "partials/header.php"; ?>

<main class="page-shell profile-page">
    <section class="profile-hero">
        <div class="profile-avatar" aria-hidden="true"><?= e(strtoupper(substr($user["nombre"], 0, 1))) ?></div>
        <div>
            <span class="eyebrow">Cuenta STREAM+</span>
            <h1><?= e($user["nombre"]) ?></h1>
            <p><?= e($user["email"]) ?></p>
        </div>
        <span class="plan-badge"><?= e($planLabel) ?></span>
    </section>

    <section class="profile-layout">
        <aside class="profile-tabs" aria-label="Secciones del perfil">
            <button class="tab active" data-tab="datos" type="button">Datos personales</button>
            <button class="tab" data-tab="suscripcion" type="button">Suscripcion</button>
            <button class="tab" data-tab="compras" type="button">Compras</button>
            <button class="tab" data-tab="historial" type="button">Historial</button>
            <button class="tab" data-tab="config" type="button">Configuracion</button>
        </aside>

        <div class="profile-content">
            <section id="datos" class="profile-section active">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">Identidad</span>
                        <h2>Datos personales</h2>
                    </div>
                </div>
                <div class="info-list">
                    <div>
                        <span>Nombre</span>
                        <strong><?= e($user["nombre"]) ?></strong>
                    </div>
                    <div>
                        <span>Email</span>
                        <strong><?= e($user["email"]) ?></strong>
                    </div>
                </div>
            </section>

            <section id="suscripcion" class="profile-section">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">Plan actual</span>
                        <h2>Suscripcion</h2>
                    </div>
                </div>
                <div class="profile-card">
                    <span class="plan-badge"><?= e($planLabel) ?></span>
                    <p>Tu cuenta mantiene acceso al catalogo, foro y tienda de STREAM+<?= is_premium_user($user) ? " con 5% de descuento en productos." : "." ?></p>
                </div>
            </section>

            <section id="compras" class="profile-section">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">Tienda</span>
                        <h2>Compras recientes</h2>
                    </div>
                </div>
                <?php if ($compras): ?>
                    <div class="info-list">
                        <?php foreach ($compras as $compra): ?>
                            <div>
                                <span><?= e($compra["nombre"]) ?> x<?= e($compra["cantidad"]) ?></span>
                                <strong><?= e(number_format(product_price((float) $compra["precio"], $user) * (int) $compra["cantidad"], 2)) ?> EUR</strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>No hay compras aun</h3>
                        <p>Los productos que anadas al carrito apareceran aqui.</p>
                    </div>
                <?php endif; ?>
            </section>

            <section id="historial" class="profile-section">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">Actividad</span>
                        <h2>Historial</h2>
                    </div>
                </div>
                <?php if ($historial): ?>
                    <div class="info-list">
                        <?php foreach ($historial as $item): ?>
                            <div>
                                <span>Visto recientemente</span>
                                <strong><?= e($item["titulo"]) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>Sin actividad reciente</h3>
                        <p>Cuando marques episodios como vistos, se mostraran en esta zona.</p>
                    </div>
                <?php endif; ?>
            </section>

            <section id="config" class="profile-section">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow">Preferencias</span>
                        <h2>Configuracion</h2>
                    </div>
                </div>
                <div class="profile-card">
                    <?php if (isset($_GET["saved"])): ?>
                        <div class="profile-notice">Preferencias guardadas.</div>
                    <?php endif; ?>

                    <form action="../controllers/ProfileController.php" method="POST" class="profile-preferences">
                        <label class="toggle-row">
                            <span>
                                <strong>Modo anti spoiler</strong>
                                <small>Oculta episodios no vistos y productos relacionados con contenidos pendientes.</small>
                            </span>
                            <input type="checkbox" name="modo_anti_spoiler" value="1" <?= $antiSpoiler ? "checked" : "" ?>>
                        </label>
                        <button class="btn btn-primary" name="guardar_preferencias" type="submit">Guardar preferencias</button>
                    </form>
                </div>
            </section>
        </div>
    </section>
</main>

<script>
document.querySelectorAll(".tab").forEach((btn) => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".tab").forEach((tab) => tab.classList.remove("active"));
        document.querySelectorAll(".profile-section").forEach((section) => section.classList.remove("active"));

        btn.classList.add("active");
        document.getElementById(btn.dataset.tab).classList.add("active");
    });
});

const selectedTab = new URLSearchParams(window.location.search).get("tab");
if (selectedTab) {
    const tabButton = document.querySelector(`.tab[data-tab="${selectedTab}"]`);
    const tabSection = document.getElementById(selectedTab);

    if (tabButton && tabSection) {
        document.querySelectorAll(".tab").forEach((tab) => tab.classList.remove("active"));
        document.querySelectorAll(".profile-section").forEach((section) => section.classList.remove("active"));
        tabButton.classList.add("active");
        tabSection.classList.add("active");
    }
}
</script>

</body>
</html>
