<?php
    class Contact extends Controller 
    {
        function __construct(){
            parent::__construct();
            // Iniciamos sesión una sola vez para todos los métodos de este controlador
            if (session_status() == PHP_SESSION_NONE) {
                sec_session_start();
            }
        }

        public function render(){
            // No requisitos de login ni de privilegios (todo el mundo puede contactar)

            // Crear un token CSRF para los formularios
            $this->generateTokenCsrf();

            // Comprobar si hay mensajes en la sesión y pasarlos a la vista
            $this->checkMessages();

            if (isset($_SESSION['errors'])) {
                $this->view->error = $_SESSION['errors'];
                $this->view->contact = $_SESSION['contact'];
                
                unset($_SESSION['errors']);
                unset($_SESSION['contact']);
            } else {
                    // Inicializar objeto vacío si es la primera vez que entra
                    $this->view->contact = new class_contact();
            }

            // Creo la propiedad  title para la vista
            $this->view->title = "Formulario de Contacto - Traileros";


            // Renderizo la vista del formulario de contacto
            $this->view->render('contact/index');
        }

        /*
        Método: validate()
        Descripción: Valida el formulario de contacto y si es correcto envía un email al administrador 
        con los datos del formulario
    */

    public function validate(){

        // Verificar el token CSRF
        $this->checkTokenCsrf($_POST['csrf_token'] ?? '');

        // Recogemos los datos del formulario saneados
        // Prevenir ataques XSS
        $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $subject = filter_var($_POST['subject'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $message = filter_var($_POST['message'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        // Crear un objeto de la clase Contact
        $contact = new class_contact($name, $email, $subject, $message);

        // Validamos los campos del formulario

        // Creo un array asociativo para almacenar los posibles errores del formulario
        // $error['nombre'] =  'Nombre es obligatorio'

        $errors = [];

        // Validamos el name
        // Regla validación: obligatorio
        if (empty($name)) {
            $errors['name'] = "El nombre es obligatorio";
        }

        // Validación de los apellidos
        // Regla validación: obligatorio
        if (empty($email)) {
            $errors['email'] = "El email es obligatorio";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "El formato del email no es correcto";
        }

        // Vaidamos el subject
        // Regla validación: obligatorio
        if (empty($subject)) {
            $errors['subject'] = "El asunto es obligatorio";
        }

        // Validamos el message
        // Regla validación: obligatorio
        if (empty($message)) {
            $errors['message'] = "El campo message no puede estar vacío";
        }

        // Fin Validación

        // Si hay errores vuelvo al formulario mostrando los errores
        if (!empty($errors)) {

            // Almaceno los errores en la sesión
            $_SESSION['errors'] = $errors;

            // Almaceno los datos del formulario en la sesión para rellenar el formulario
            $_SESSION['contact'] = $contact;

            // Mensaje de error general para la vista
            $_SESSION['error'] = "Errores en el formulario";

            // Redirijo al formulario
            header('Location: ' . URL . 'contact');
            exit();
        }

        // Preparamos correo al administrador con los datos del formulario
        $destinatario = "admin@traileros.com";
        $asunto = "Contacto Web Traileros" . $subject;

        $cuerpo = " <html>
                    <head>
                        <title>Nuevo mensaje de contacto</title>
                    </head>
                    <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
                        <h2 style='color: #333;>Has recibido un nuevo mensaje desde el formulario de contacto</h2>
                        <p><strong>De:</strong> {$name} ({$email})</p>
                        <p><strong>Asunto:</strong> {$subject}</p>
                        <hr>
                        <p><strong>Mensaje:</strong></p>
                        <p style='background: #f4f4f4; padding: 15px; border-radius: 5px;'>" . nl2br($message) . "</p>
                        <hr>
                        <p>Este mensaje fue enviado automáticamente desde el sistema de Traileros.</p>
                    </body>
                    </html>";

        // Validacion envio email
        if (Email::enviar($destinatario, $asunto, $cuerpo)) $_SESSION['notify'] = "¡Mensaje enviado con éxito!";
        else $this->handleError("No se pudo enviar el mensaje. Por favor, inténtelo más tarde.");


        // Redirigir a la lista de alumnos
        header('Location: ' . URL . 'contact');
        exit();
    }

    }

?>