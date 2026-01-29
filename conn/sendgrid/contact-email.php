<?php

function ContactEmailBody($name, $email, $message)
{
    return '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 20px auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
            .header { background: #0A0338; color: #fff; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .user-info { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
            .user-info p { margin: 5px 0; }
            .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin: 0; font-size: 24px;">Nueva Consulta de Contacto</h1>
            </div>
            <div class="content">
                <p>Has recibido una nueva consulta desde el sitio web:</p>
                
                <div class="user-info">
                    <p><strong>Nombre:</strong> ' . htmlspecialchars($name) . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                </div>

                <h3>Mensaje:</h3>
                <div style="white-space: pre-wrap; background: #f4f4f4; padding: 15px; border-radius: 4px;">' . htmlspecialchars($message) . '</div>
            </div>
            <div class="footer">
                &copy; ' . date('Y') . ' Monoplast - Todos los derechos reservados.
            </div>
        </div>
    </body>
    </html>';
}
