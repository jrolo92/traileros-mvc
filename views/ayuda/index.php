<!doctype html>
<html lang="es"> 

<head>
    <?php require_once 'template/layouts/head.layout.php'; ?>
    <title><?php echo $this->title ?> </title>
</head>

<body>
    <?php require_once("template/partials/header.partial.php") ?>
    
    <main class="form-page-container">
        <div class="form-card"> <div class="back-navigation">
                <a href="javascript:history.back()" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver</span>
                </a>
            </div>

            <header class="form-card-header">
                <i class="fas fa-question-circle"></i>
                <h2>Centro de Ayuda</h2>
                <p>Entendiendo la arquitectura de nuestra aplicación</p>
            </header>

            <section class="help-content" style="padding: 20px; line-height: 1.6; color: #444;">
                <h3 style="color: $primary-green; margin-bottom: 15px;">Arquitectura MVC</h3>
                <p>
                    Esta aplicación ha sido desarrollada bajo el patrón <strong>Modelo-Vista-Controlador</strong>, 
                    lo que permite separar la lógica de negocio de la interfaz de usuario.
                </p>
                
                <div class="alert alert-info" style="background: #f0f7ff; border-left: 4px solid #3498db; padding: 15px; margin-top: 20px;">
                    <i class="fas fa-info-circle"></i> 
                    Recuerda que para gestionar carreras debes estar autenticado en el sistema.
                </div>
            </section>

        </div>
    </main>
    
    <footer class="footer">
		<?php require_once("template/partials/footer.partial.php") ?>
	</footer>
	
</body>

</html>