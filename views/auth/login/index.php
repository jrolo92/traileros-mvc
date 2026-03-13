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
                    <p>Tu próxima meta comienza aquí.</p>
                </div>
            </section>

            <section class="login-content">
                <header class="login-header">
                    <h2>LOGIN</h2>
                </header>

                <form class="login-form" method="POST" action="<?= URL ?>auth/validate_login">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($this->email); ?>" required autofocus>
                        <span class="error-msg"><?= $this->errors['email'] ?? '' ?></span>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <span class="error-msg"><?= $this->errors['password'] ?? '' ?></span>
                    </div>

                    <div class="form-actions-extra">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember"> 
                            <span>Recuerdame</span>
                        </label>
                        <a href="#" class="forgot-link">¿Olvidó su contraseña?</a>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-submit">Acceder</button>
                        <a href="<?= URL ?>auth/register" class="btn-register">Registrar</a>
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