<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Configuración de Google reCAPTCHA
    $recaptchaSecret = 'TU_CLAVE_SECRETA_RECAPTCHA'; // Reemplazar con la clave secreta generada en Google
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    // Validar token de reCAPTCHA directamente con Google
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verifyUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
    $responseData = json_decode($response);

    if (!$responseData || !$responseData->success) {
        header('Location: ../consultoria.html?status=captcha_error');
        exit;
    }

    // 2. Sanitización y filtrado de datos del formulario de consultoría
    $nombre_empresa   = htmlspecialchars(trim($_POST['nombre_empresa'] ?? ''), ENT_QUOTES, 'UTF-8');
    $nombre_contacto  = htmlspecialchars(trim($_POST['nombre_contacto'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email            = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $cargo            = htmlspecialchars(trim($_POST['cargo'] ?? ''), ENT_QUOTES, 'UTF-8');
    $numero_empleados = htmlspecialchars(trim($_POST['numero_empleados'] ?? ''), ENT_QUOTES, 'UTF-8');
    $pais             = htmlspecialchars(trim($_POST['pais'] ?? ''), ENT_QUOTES, 'UTF-8');
    $telefono         = htmlspecialchars(trim($_POST['telefono'] ?? ''), ENT_QUOTES, 'UTF-8');
    $mensaje          = htmlspecialchars(trim($_POST['mensaje'] ?? ''), ENT_QUOTES, 'UTF-8');

    // Comprobar campos obligatorios
    if (!$email || empty($nombre_empresa) || empty($nombre_contacto) || empty($mensaje)) {
        header('Location: ../consultoria.html?status=invalid_fields');
        exit;
    }

    // 3. Construcción del mensaje de correo
    $para   = 'fserranozamora@gmail.com';
    $titulo = 'Solicitud de Consultoría Actuarial - Actuarios Colombianos S.A.S.';

    $msjCorreo  = "Has recibido una nueva solicitud de consultoría desde el sitio web:\n\n";
    $msjCorreo .= "Empresa:              {$nombre_empresa}\n";
    $msjCorreo .= "Nombre de contacto:   {$nombre_contacto}\n";
    $msjCorreo .= "Correo electrónico:   {$email}\n";
    $msjCorreo .= "Cargo:                {$cargo}\n";
    $msjCorreo .= "Número de empleados:  {$numero_empleados}\n";
    $msjCorreo .= "País:                 {$pais}\n";
    $msjCorreo .= "Teléfono:             {$telefono}\n\n";
    $msjCorreo .= "Mensaje o detalles del requerimiento:\n{$mensaje}\n";

    // Cabeceras MIME para prevenir filtros de correo no deseado (SPAM)
    $headers  = "From: Actuarios Colombianos <no-reply@actuaria.com.co>\r\n";
    $headers .= "Reply-To: {$email}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // 4. Envío y redirección al usuario
    if (mail($para, $titulo, $msjCorreo, $headers)) {
        header('Location: ../consultoria.html?status=success');
    } else {
        header('Location: ../consultoria.html?status=mail_error');
    }
    exit;
} else {
    header('Location: ../consultoria.html');
    exit;
}