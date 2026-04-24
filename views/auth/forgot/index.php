<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Menú fijo superior -->
    <?php require_once 'template/partials/header.partial.php'?>

    <!-- Capa Principal -->
    <main class="login-page">
        <div class="login-card">
            
            <section class="login-visual">
                <div class="visual-content">
                    <i class="fas fa-mountain"></i>
                    <h1>TRAILEROS</h1>
                    <p>¿Has olvidado tu contraseña?</p>
                </div>
            </section>

            <section class="login-content">
                <div class="back-navigation">
                    <a href="<?= URL ?>auth/login" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver</span>
                    </a>
                </div>
                <header class="login-header">
                    <h2>Introduce tu email</h2>
                </header>

                <form class="login-form" method="POST" action="<?= URL ?>auth/recuperar">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($this->email); ?>" required autofocus>
                        <span class="error-msg"><?= $this->errors['email'] ?? '' ?></span>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </section>

        </div>
    </main>


    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php'?>
    </footer>

</body>
</body>
</html>