
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?= $this->title ?> </title>
</head>
<body>

    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <main>

        <?php require_once 'template/partials/header.partial.php'; ?>

        <section class="hero">
            <div class="hero-content">
                <h1>DESAFÍA TUS LÍMITES</h1>
                <p>La plataforma definitiva para amantes del Trail Running</p>
                <a href="<?= URL ?>carrera/render" class="btn-primary">Explorar Carreras</a>
            </div>
        </section>

        <section class="login-section">
            <div class="container">
                <form class="login-form" action="<?= URL ?>auth/login" method="POST">
                    <h2>Acceso Corredores</h2>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? '' ?>">
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="tu@email.com" required>
                    </div>
                    <div class="input-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" placeholder="********" required>
                    </div>
                    <button type="submit" class="btn-login">Entrar</button>
                    <p class="form-footer">¿No tienes cuenta? <a href="<?= URL ?>auth/register"">Regístrate aquí</a></p>
                </form>
            </div>
        </section>

        <section id="carreras" class="carreras-section">
            <div class="container">
                <h2 class="section-title">Próximos Eventos</h2>
                <div class="grid-carreras">
                    <article class="card">
                        <div class="card-image" style="background-image: url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=500');">
                            <span class="badge alta">Alta</span>
                        </div>
                        <div class="card-body">
                            <h3>Gran Vuelta Valle del Genal</h3>
                            <p class="location"><i class="fas fa-map-marker-alt"></i> Pujerra, Málaga</p>
                            <div class="stats">
                                <span><i class="fas fa-route"></i> 55km</span>
                                <span><i class="fas fa-mountain"></i> +2900m</span>
                            </div>
                            <a href="#" class="btn-outline">Ver detalles</a>
                        </div>
                    </article>
                    </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <?php require_once 'template/partials/footer.partial.php' ?>
    </footer>
</body>
</html>