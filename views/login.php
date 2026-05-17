<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | STREAM+</title>
<link rel="stylesheet" href="../public/style.css">
</head>

<body class="login-page">

<main class="login-screen">
    <section class="login-band" aria-label="Acceso a STREAM+">
        <form class="login-panel" action="../controllers/AuthController.php" method="POST">
            <div class="login-step login-step--title">
                <span class="eyebrow">STREAM+</span>
                <h1>Iniciar sesion</h1>
                <p>Entra para continuar viendo, comprar y participar en el foro.</p>
            </div>

            <?php if (isset($_GET["error"])): ?>
                <div class="login-step login-alert">
                    Email o contrasena incorrectos.
                </div>
            <?php endif; ?>

            <label class="login-step login-step--email">
                <span>Usuario o email</span>
                <input type="email" name="email" placeholder="tu@email.com" required>
            </label>

            <label class="login-step login-step--password">
                <span>Contrasena</span>
                <input type="password" name="password" placeholder="Tu contrasena" required>
            </label>

            <div class="login-step login-step--button">
                <button class="btn btn-primary" name="login" type="submit">Iniciar sesion</button>
            </div>

            <div class="login-step login-step--register">
                <span>No tienes cuenta?</span>
                <a href="register.php">Registrarse</a>
            </div>
        </form>
    </section>
</main>

</body>
</html>
