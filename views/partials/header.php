<header class="main-header">
    <a class="logo" href="home.php">STREAM+</a>

    <nav class="nav-menu">
        <a href="home.php" data-nav="home">Inicio</a>
        <a href="cine.php" data-nav="cine">Cine</a>
        <a href="shop.php" data-nav="tienda">Tienda</a>
        <a href="forum.php" data-nav="foro">Foro</a>
        <a href="profile.php" data-nav="profile">Perfil</a>
        <?php if (function_exists('getAuthService') && getAuthService()->isDeveloper()): ?>
            <a href="developer.php" data-nav="developer">Dev</a>
        <?php endif; ?>
    </nav>

    <div class="search-container" role="search">
        <div class="input-wrapper">
            <div class="effect-layer outer-glow"></div>
            <div class="effect-layer dark-layer"></div>
            <div class="effect-layer bright-layer"></div>
            <div class="effect-layer primary-border"></div>

            <input
                type="text"
                id="searchInput"
                class="search-field"
                placeholder="Buscar..."
                autocomplete="off"
                aria-label="Buscar"
            >
        </div>
    </div>

    <?php if (isset($_SESSION["usuario"])): ?>
        <a class="header-logout" href="../logout.php">Cerrar sesion</a>
    <?php else: ?>
        <a class="header-logout" href="login.php">Entrar</a>
    <?php endif; ?>
</header>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("searchInput");
    const grid = document.getElementById("contentGrid");
    const section = document.body.dataset.section;

    document.querySelectorAll("[data-nav]").forEach((link) => {
        if (link.dataset.nav === section) {
            link.classList.add("active");
        }
    });

    if (!input || !grid) return;

    let timeout = null;

    input.addEventListener("input", () => {
        clearTimeout(timeout);

        const query = input.value.trim();

        if (query.length === 0) {
            location.reload();
            return;
        }

        timeout = setTimeout(() => {
            fetch(`../controllers/SearchLiveController.php?q=${encodeURIComponent(query)}&section=${encodeURIComponent(section)}`)
                .then((res) => res.json())
                .then((data) => {
                    grid.innerHTML = "";

                    if (data.length === 0) {
                        grid.innerHTML = '<div class="empty-state"><h3>Sin resultados</h3><p>Prueba con otro termino de busqueda.</p></div>';
                        return;
                    }

                    data.forEach((item) => {
                        grid.innerHTML += item.html;
                    });

                    window.applySmartImages?.(grid);
                })
                .catch((err) => console.error(err));
        }, 300);
    });
});

window.applySmartImages = (root = document) => {
    const proxyPath = "../public/image_proxy.php";
    const shouldProxy = (src) => {
        if (!src || src.startsWith("data:") || src.includes("/public/image_proxy.php")) return false;

        try {
            const url = new URL(src, window.location.href);
            return url.protocol.startsWith("http") && url.host !== window.location.host;
        } catch {
            return false;
        }
    };

    const proxyUrl = (src) => `${proxyPath}?url=${encodeURIComponent(src)}`;

    root.querySelectorAll("img").forEach((img) => {
        if (!img.dataset.originalSrc) {
            img.dataset.originalSrc = img.getAttribute("src") || img.currentSrc || img.src;
        }

        const useProxy = () => {
            if (img.dataset.proxyTried === "1") {
                img.classList.add("image-failed");
                img.alt = img.alt || "Imagen no disponible";
                img.src = "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 600'%3E%3Crect width='600' height='600' fill='%23101116'/%3E%3Cpath d='M120 390l96-118 72 82 58-69 134 158H120z' fill='%2320222d'/%3E%3Ccircle cx='392' cy='190' r='42' fill='%237c6dff'/%3E%3Ctext x='300' y='510' fill='%23a9adbd' font-family='Arial' font-size='30' text-anchor='middle'%3EImagen no disponible%3C/text%3E%3C/svg%3E";
                return;
            }

            const original = img.dataset.originalSrc || img.src;

            if (!original || original.includes("/public/image_proxy.php")) {
                img.classList.add("image-failed");
                return;
            }

            img.dataset.proxyTried = "1";
            img.src = proxyUrl(original);
        };

        const classify = () => {
            if (!img.naturalWidth || !img.naturalHeight) return;

            const ratio = img.naturalWidth / img.naturalHeight;
            img.classList.add("smart-image");

            if (img.closest(".media-card, .product-card, .product-hero, .cart-item, .hero-feature, .detail-hero__poster, .poster-chip")) {
                img.classList.add("smart-image--cover");
            } else if (img.src.includes("m.media-amazon.com")) {
                img.classList.add("smart-image--contain");
            } else {
                img.classList.add("smart-image--cover");
            }
        };

        img.addEventListener("error", useProxy);
        img.addEventListener("load", classify);

        if (shouldProxy(img.dataset.originalSrc) && img.dataset.proxied !== "1") {
            img.dataset.proxied = "1";
            img.src = proxyUrl(img.dataset.originalSrc);
            return;
        }

        if (img.complete) {
            if (img.naturalWidth === 0) {
                useProxy();
            } else {
                classify();
            }
        }
    });
};

document.addEventListener("DOMContentLoaded", () => {
    window.applySmartImages();
});
</script>
