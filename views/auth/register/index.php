<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?></title>
</head>
   
<body>
    <?php require_once 'template/partials/header.partial.php' ?>

    <main class="login-page">
        
        <div class="login-card">
            
            <section class="login-visual">
                <div class="visual-content">
                    <i class="fas fa-user-plus"></i>
                    <h1>ÚNETE</h1>
                    <p>Forma parte de la mayor comunidad de Trail Running</p>
                </div>
            </section>

            <section class="login-content">
                
                <header class="login-header">
                    <h2>Registro</h2>
                    <?php require_once("template/partials/mensaje.partial.php") ?>
                    <?php require_once("template/partials/error.partial.php") ?>
                </header>

                <form method="POST" action="<?= URL ?>auth/validate_register" class="login-form">
                    
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="input-group">
                        <label for="name">Nombre</label>
                        <input id="name" type="text" name="name" 
                               class="<?= isset($this->errors['name']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($this->name) ?>" required autofocus>
                        <small class="text-danger"><?= $this->errors['name'] ?? '' ?></small>
                    </div>

                    <div class="input-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" 
                               class="<?= isset($this->errors['email']) ? 'is-invalid' : '' ?>"
                               value="<?= htmlspecialchars($this->email) ?>" required>
                        <small class="text-danger"><?= $this->errors['email'] ?? '' ?></small>
                    </div>

                    <div class="input-group">
                        <label for="pass">Contraseña</label>
                        <input id="pass" type="password" name="password" 
                               class="<?= isset($this->errors['password']) ? 'is-invalid' : '' ?>"
                               required>
                        <small class="text-danger"><?= $this->errors['password'] ?? '' ?></small>
                    </div>

                    <div class="input-group">
                        <label for="password2">Repetir contraseña</label>
                        <input id="password2" type="password" name="password2" 
                               class="<?= isset($this->errors['password2']) ? 'is-invalid' : '' ?>"
                               required>
                        <small class="text-danger"><?= $this->errors['password2'] ?? '' ?></small>
                    </div>

                    <div class="form-buttons">
                        <a href="<?= URL ?>auth/login" class="btn-register">Volver</a>
                        <button type="submit" class="btn-submit">Crear cuenta</button>
                    </div>

                </form>
            </section>
        </div>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php' ?>
    </footer>
</body>

</html>

