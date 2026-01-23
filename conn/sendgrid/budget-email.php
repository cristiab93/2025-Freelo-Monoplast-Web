<?php

function BudgetEmailBody($user_name, $user_email, $user_phone, $cart)
{
    $rows = '';
    foreach ($cart as $item) {
        $img_url = $item['img'];
        if (strpos($img_url, 'http') !== 0) {
            $img_url = (defined('BASEURL') ? BASEURL : '') . '/' . ltrim($item['img'], '/');
        }
        $rows .= '
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                <img src="' . $img_url . '" width="50" height="50" style="object-fit: cover; border-radius: 4px;">
            </td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd;">
                <p style="margin: 0; font-weight: bold; color: #0A0338;">' . $item['name'] . '</p>
                <p style="margin: 0; font-size: 12px; color: #666;">' . $item['subname'] . '</p>
            </td>
            <td style="padding: 10px; border-bottom: 1px solid #ddd; text-align: center; color: #0A0338;">
                ' . $item['qty'] . '
            </td>
        </tr>';
    }

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
            table { width: 100%; border-collapse: collapse; }
            th { text-align: left; padding: 10px; background: #eee; color: #0A0338; }
            .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1 style="margin: 0; font-size: 24px;">Nuevo Presupuesto</h1>
            </div>
            <div class="content">
                <p>Has recibido una nueva solicitud de presupuesto:</p>
                
                <div class="user-info">
                    <p><strong>Nombre:</strong> ' . htmlspecialchars($user_name) . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($user_email) . '</p>
                    <p><strong>Teléfono:</strong> ' . htmlspecialchars($user_phone) . '</p>
                </div>

                <h3>Productos solicitados:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Producto</th>
                            <th style="text-align: center;">Cant.</th>
                        </tr>
                    </thead>
                    <tbody>
                        ' . $rows . '
                    </tbody>
                </table>
            </div>
            <div class="footer">
                &copy; ' . date('Y') . ' Monoplast - Todos los derechos reservados.
            </div>
        </div>
    </body>
    </html>';
}
