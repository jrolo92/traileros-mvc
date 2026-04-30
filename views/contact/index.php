<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?> </title>
</head>
<body>
    <?php require_once 'template/partials/header.partial.php'; ?>
    

    <div class="form-page-container">
        <div class="form-card">
            
            <header class="form-card-header">
                <i class="bi bi-envelope-paper"></i>
                <h2>Contacto</h2>
                <p>¿Tienes alguna duda o sugerencia? Escríbenos y te responderemos lo antes posible.</p>
            </header>

            <!-- Notificaciones -->
            <?php require_once "template/partials/mensaje.partial.php" ?>
            <?php require_once "template/partials/error.partial.php" ?> 

            <form action="<?= URL ?>contact/validate" method="POST" class="custom-form">
                
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="form-row">
                    <!-- Nombre -->
                    <div class="col">
                        <div class="form-group">
                            <label for="name">Nombre Completo</label>
                            <input type="text" name="name" id="name" 
                                class="<?= isset($this->error['name']) ? 'input-error' : '' ?>"
                                value="<?= htmlspecialchars($this->contact->name ?? '') ?>" 
                                placeholder="Tu nombre...">
                            <?php if (isset($this->error['name'])): ?>
                                <span class="error-text"><?= $this->error['name'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col">
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" id="email" 
                                class="<?= isset($this->error['email']) ? 'input-error' : '' ?>"
                                value="<?= htmlspecialchars($this->contact->email ?? '') ?>" 
                                placeholder="ejemplo@correo.com">
                            <?php if (isset($this->error['email'])): ?>
                                <span class="error-text"><?= $this->error['email'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Asunto -->
                <div class="form-group">
                    <label for="subject">Asunto</label>
                    <input type="text" name="subject" id="subject" 
                        class="<?= isset($this->error['subject']) ? 'input-error' : '' ?>"
                        value="<?= htmlspecialchars($this->contact->subject ?? '') ?>" 
                        placeholder="¿Sobre qué quieres hablar?">
                    <?php if (isset($this->error['subject'])): ?>
                        <span class="error-text"><?= $this->error['subject'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Mensaje -->
                <div class="form-group">
                    <label for="message">Mensaje</label>
                    <textarea name="message" id="message" rows="6" 
                            class="<?= isset($this->error['message']) ? 'input-error' : '' ?>"
                            placeholder="Escribe aquí tu mensaje detallado..."><?= htmlspecialchars($this->contact->message ?? '') ?></textarea>
                    <?php if (isset($this->error['message'])): ?>
                        <span class="error-text"><?= $this->error['message'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <a href="<?= URL ?>index" class="btn-secondary">Volver al inicio</a>
                    
                    <div class="main-buttons">
                        <button type="submit" class="btn-account-save">
                            <i class="fas fa-paper-plane"></i>
                            Enviar Mensaje
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <footer class="footer">
        <?php require_once "template/partials/footer.partial.php"?>
    </footer>
</body>
</html>