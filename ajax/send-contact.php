<?php
include_once("../_general.php");
require_once("../conn/sendgrid.php");
require_once("../conn/sendgrid/contact-email.php");

$name = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : '';

if (!empty($name) && !empty($email) && !empty($message)) {
    // Construct email body
    $html_body = ContactEmailBody($name, $email, $message);
    
    // Send email to the specified address
    // Send email to multiple recipients
    $recipients = ["cristian.benavente@kahloagencia.com", "info@monoplast.com.ar"];
    $subject = "Nueva Consulta de Contacto - " . $name;
    $result = true;

    foreach ($recipients as $recipient) {
        $sent = SG_SendMail($name, "notificaciones@kahloagencia.com", $recipient, $html_body, $subject);
        if (!$sent) $result = false;
    }
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Tu mensaje se envió con éxito.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Hubo un error al enviar el mensaje. Intentá de nuevo.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: Todos los campos son obligatorios.']);
}
