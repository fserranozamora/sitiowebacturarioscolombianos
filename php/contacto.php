<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Configuración de Google reCAPTCHA
    $recaptchaSecret = 'TU_CLAVE_SECRETA_RECAPTCHA'; // Reemplazar con tu clave secreta de Google
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    // Validar reCAPTCHA directamente con Google
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verifyUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
    $responseData = json_decode($response);

    if (!$responseData || !$responseData->success) {
        header('Location: ../contacto.html?status=captcha_error');
        exit;
    }

    // 2. Limpieza y validación de campos recibidos
    $nombre_empresa  = htmlspecialchars(trim($_POST['nombre_empresa'] ?? ''), ENT_QUOTES, 'UTF-8');
    $nombre_contacto = htmlspecialchars(trim($_POST['nombre_contacto'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email           = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $cargo           = htmlspecialchars(trim($_POST['cargo'] ?? ''), ENT_QUOTES, 'UTF-8');
    $pais            = htmlspecialchars(trim($_POST['pais'] ?? ''), ENT_QUOTES, 'UTF-8');
    $telefono        = htmlspecialchars(trim($_POST['telefono'] ?? ''), ENT_QUOTES, 'UTF-8');
    $mensaje         = htmlspecialchars(trim($_POST['mensaje'] ?? ''), ENT_QUOTES, 'UTF-8');

    if (!$email || empty($nombre_empresa) || empty($nombre_contacto) || empty($mensaje)) {
        header('Location: ../contacto.html?status=invalid_fields');
        exit;
    }

    // 3. Formateo del correo electrónico
    $para   = 'fserranozamora@gmail.com';
    $titulo = 'Nuevo mensaje de contacto - Actuarios Colombianos S.A.S.';

    $msjCorreo  = "Has recibido un nuevo mensaje desde el formulario de contacto web:\n\n";
    $msjCorreo .= "Nombre de la empresa: {$nombre_empresa}\n";
    $msjCorreo .= "Nombre de contacto:   {$nombre_contacto}\n";
    $msjCorreo .= "Correo electrónico:   {$email}\n";
    $msjCorreo .= "Cargo:                {$cargo}\n";
    $msjCorreo .= "País:                 {$pais}\n";
    $msjCorreo .= "Teléfono:             {$telefono}\n\n";
    $msjCorreo .= "Mensaje:\n{$mensaje}\n";

    // Cabeceras MIME para compatibilidad con servidores modernos y evitar SPAM
    $headers  = "From: Actuarios Colombianos <no-reply@actuaria.com.co>\r\n";
    $headers .= "Reply-To: {$email}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // 4. Envío de correo y redirección con respuesta
    if (mail($para, $titulo, $msjCorreo, $headers)) {
        header('Location: ../contacto.html?status=success');
    } else {
        header('Location: ../contacto.html?status=mail_error');
    }
    exit;
} else {
    header('Location: ../contacto.html');
    exit;
}