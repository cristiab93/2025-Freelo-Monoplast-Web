<?php
include_once("_general.php");
require_once("conn/sendgrid.php");

$name = isset($_POST['nombre']) ? $_POST['nombre'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$phone = isset($_POST['telefono']) ? $_POST['telefono'] : '';

if (!empty($name) && !empty($email)) {
    // Get cart from session
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    
    if (!empty($cart)) {
        // Generate a unique hashed ID for this quote
        $budget_hash = bin2hex(random_bytes(16)); // 32 chars hex string

        // Save to database
        foreach ($cart as $id => $item) {
            $real_id = sed_decryption($item['id']);
            if (!$real_id) $real_id = 0; // Fallback

            InsertQuery('presupuestos')
                ->Value('budget_hash', 's', $budget_hash)
                ->Value('client_name', 's', $name)
                ->Value('client_email', 's', $email)
                ->Value('client_phone', 's', $phone)
                ->Value('product_id', 'i', (int)$real_id)
                ->Value('product_name', 's', $item['name'])
                ->Value('product_qty', 'i', (int)$item['qty'])
                ->Run();
        }

        // Construct email body
        $html_body = BudgetEmailBody($name, $email, $phone, $cart);
        
        // Send email to the specified address
        // Enviar email a múltiples destinatarios
        $recipients = ["cristian.benavente@kahloagencia.com", "info@monoplast.com.ar"];
        $subject = "Nueva Solicitud de Presupuesto [#$budget_hash] - " . $name;
        $result = true;

        foreach ($recipients as $recipient) {
            $sent = SG_SendMail($name, "notificaciones@kahloagencia.com", $recipient, $html_body, $subject);
            if (!$sent) $result = false;
        }
        
        if ($result) {
            // Vaciar carrito
            $_SESSION['cart'] = [];
            echo "Tu presupuesto ha sido enviado con éxito.";
        } else {
            echo "Error al enviar el presupuesto. Intente nuevamente.";
        }
    } else {
        echo "Error: El presupuesto está vacío.";
    }
} else {
    echo "Error: Todos los campos son obligatorios";
}
