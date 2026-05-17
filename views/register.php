<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | STREAM+</title>
    <link rel="stylesheet" href="../public/style.css">
</head>

<body class="login-page register-page">

<main class="register-screen">
    <section class="register-panel" aria-label="Crear cuenta STREAM+">
        <div class="register-copy">
            <a class="logo" href="login.php">STREAM+</a>
            <span class="eyebrow">Nueva cuenta</span>
            <h1>Crear cuenta</h1>
            <p>Elige tu plan, entra al catalogo y lleva tus productos favoritos al carrito.</p>
        </div>

        <form action="../controllers/AuthController.php" method="POST" class="register-form">
            <label>
                <span>Nombre</span>
                <input type="text" name="nombre" placeholder="Tu nombre" required>
            </label>
            <label>
                <span>Email</span>
                <input type="email" name="email" placeholder="tu@email.com" required>
            </label>
            <label>
                <span>Contrasena</span>
                <input type="password" name="password" placeholder="Tu contrasena" required>
            </label>

            <fieldset class="plan-choice">
                <legend>Plan</legend>
                <label class="plan-option">
                    <input type="radio" name="tipo_plan" value="basico" checked>
                    <span>
                        <strong>Basico</strong>
                        <small>Catalogo, foro y tienda.</small>
                    </span>
                </label>
                <label class="plan-option plan-option--premium">
                    <input type="radio" name="tipo_plan" value="premium">
                    <span>
                        <strong>Premium</strong>
                        <small>5% de descuento en productos.</small>
                    </span>
                </label>
            </fieldset>

            <button class="btn btn-primary" name="register" type="submit">Crear cuenta</button>
            <p class="register-login">Ya tienes cuenta? <a href="login.php">Iniciar sesion</a></p>
        </form>
    </section>
</main>

</body>
</html>
