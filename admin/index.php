<?php
$PAGE = 'admin-index';
include('../_general.php');

// --- Datos base ---
$rows = SelectQuery('products')->Order('product_date','DESC')->Limit(1000000)->Run();
$rows = is_array($rows) ? array_values($rows) : [];

$total_products = count($rows);

// categorías únicas (limpiando vacíos)
$cat = [];
$last_date = '';
foreach ($rows as $r) {
    $c = trim($r['product_category'] ?? '');
    if ($c !== '') $cat[$c] = true;
}
// Conteo real de categorías desde la tabla categories
$cat_rows = SelectQuery('categories')->Run();
$total_categories = is_array($cat_rows) ? count($cat_rows) : 0;

// subcategorías totales
$sub_rows = SelectQuery('sub_categories')->Run();
$total_subcategories = is_array($sub_rows) ? count($sub_rows) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <title>Monoplast - Admin</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">

    <link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
    <script src="../js/jquery-3.5.1.min.js"></script>
    <!-- Usa el bundle (incluye Popper) y evita el 404 de bootstrap.min.js -->
    <script src="../js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome 5 + v4-shims (SIN integrity para que no lo bloquee) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/v4-shims.min.css">

    <!-- Quitamos css/all.css local para evitar 404 de webfonts -->
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/admin.css" type="text/css">

    <style>
        .admin-shell{
            max-width: 1200px;
            margin: 32px auto 56px;
            padding: 24px 28px;
            background:#fff;
            border-radius:12px;
        }
        .metric-card{border:1px solid #e9ecef;border-radius:12px;padding:16px;background:#fff}
        .metric-number{font-size:28px;font-weight:700}
        .metric-label{color:#6c757d}
        @media (max-width: 576px){
            .admin-shell{margin:20px auto 40px;padding:16px}
        }
    </style>
</head>
<body>
    <?php ShowAdminNavBar('nav-index'); ?>
    <div class="area"></div>

    <div class="admin-shell">
        <h2 class="mb-2">Panel de administración de Monoplast</h2>
        <p class="text-muted mb-4">Resumen rápido.</p>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="metric-card">
                    <div class="metric-number"><?= (int)$total_products ?></div>
                    <div class="metric-label">Productos</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="metric-card">
                    <div class="metric-number"><?= (int)$total_categories ?></div>
                    <div class="metric-label">Categorías</div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="metric-card">
                    <div class="metric-number"><?= (int)$total_subcategories ?></div>
                    <div class="metric-label">Subcategorías</div>
                </div>
            </div>
        </div>
    </div>

    <div style="width:100px; height:50px"></div>
</body>
</html>
