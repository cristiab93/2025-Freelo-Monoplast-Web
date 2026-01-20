<?php
include "_general.php";

// Definición de la Jerarquía (Categorias -> Subcategorias)
$HIERARCHY = [
    'artefactos' => [
        'label' => 'Artefactos y mamparas',
        'subs' => [
            'artefactos' => 'Artefactos',
            'griferia'   => 'Grifería',
            'mamparas'   => 'Mamparas'
        ]
    ],
    'calefaccion' => [
        'label' => 'Calefacción',
        'subs' => [
            'calderas'      => 'Calderas',
            'losa-radiante' => 'Losa radiante',
            'radiadores'    => 'Radiadores',
            'termotanques'  => 'Termotanques'
        ]
    ],
    'construccion' => [
        'label' => 'Construcción',
        'subs' => [
            'agua'              => 'Agua',
            'gas'               => 'Gas',
            'cloacal'           => 'Cloacal',
            'pvc'               => 'PVC',
            'polipropileno'     => 'Polipropileno',
            'galvanizado-epoxi' => 'Galvanizado / Epoxi',
            'bombas'            => 'Bombas',
            'canaletas'         => 'Canaletas',
            'tanques'           => 'Tanques',
            'valvulas'          => 'Válvulas'
        ]
    ],
    'infraestructura' => [
        'label' => 'Infraestructura',
        'subs' => [
            'fibra-optica'   => 'Fibra óptica',
            'canos'          => 'Caños',
            'cano-perfilado' => 'Caño perfilado',
            'pead'           => 'PEAD'
        ]
    ],
    'piletas' => [
        'label' => 'Piletas',
        'subs' => [
            'electrobombas'                => 'Electrobombas',
            'filtros'                      => 'Filtros',
            'limpieza-y-mantenimiento'     => 'Limpieza y mantenimiento',
            'productos-para-mantenimiento' => 'Productos para mantenimiento',
            'mangueras'                    => 'Mangueras'
        ]
    ],
    'riego' => [
        'label' => 'Sistemas de riego',
        'subs' => [
            'aspersores'        => 'Aspersores',
            'mangueras'         => 'Mangueras',
            'canos-hidraulicos' => 'Caños hidráulicos'
        ]
    ]
];

// Procesar actualización (AJAX o POST simple)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $cat = $_POST['category'] ?? '';
    $sub = $_POST['subcategory'] ?? '';

    // Validar slugs (opcional, pero recomendado)
    // Si $cat está vacío, se guarda vacío.
    
    $stmt = mysqli_prepare($conn, "UPDATE products SET product_category = ?, product_subcategory = ? WHERE product_id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $cat, $sub, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "OK";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit;
}

// Obtener productos
$query = "SELECT * FROM products ORDER BY product_id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error consultando tabla 'products': " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Categorías de Productos</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background-color: #f4f6f9; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.05); border: none; }
        select { font-size: 0.9rem; }
        .status-msg { font-size: 0.8rem; font-weight: bold; min-width: 60px; display: inline-block; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Administrar Categorías de Productos</h1>
        <a href="productos.php" class="btn btn-primary" target="_blank">Ver Página de Productos</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Subnombre</th>
                            <th>Categoría Actual</th>
                            <th>Nueva Categoría</th>
                            <th>Nueva Subcategoría</th>
                            <th style="width: 100px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <?php 
                                    $currCat = $row['product_category'];
                                    $currSub = $row['product_subcategory'];
                                    $img = $row['product_img'] ?: 'placeholder.png';
                                    if (!preg_match('/^http/', $img)) $img = "uploaded_img/" . $img; 
                                ?>
                                <tr id="row-<?= $row['product_id'] ?>">
                                    <td><?= $row['product_id'] ?></td>
                                    <td><img src="<?= htmlspecialchars($img) ?>" style="height: 40px; width: auto;" alt=""></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['product_subname']) ?></td>
                                    <td>
                                        <small class="d-block text-primary"><?= htmlspecialchars($currCat) ?></small>
                                        <small class="d-block text-secondary"><?= htmlspecialchars($currSub) ?></small>
                                    </td>
                                    <td>
                                        <select class="form-select cat-select" data-row="<?= $row['product_id'] ?>">
                                            <option value="">-- Seleccionar --</option>
                                            <?php foreach ($HIERARCHY as $slug => $data): ?>
                                                <option value="<?= $slug ?>" <?= $slug === $currCat ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($data['label']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select sub-select" id="sub-<?= $row['product_id'] ?>" data-selected="<?= htmlspecialchars($currSub) ?>">
                                            <option value="">-- Primero elige Categoría --</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-success btn-sm save-btn" data-id="<?= $row['product_id'] ?>">Guardar</button>
                                        <span class="status-msg text-success ms-2" id="msg-<?= $row['product_id'] ?>"></span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center py-4">No hay productos.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Pasar la jerarquía a JS
const HIERARCHY = <?= json_encode($HIERARCHY) ?>;

document.addEventListener('DOMContentLoaded', function() {
    
    // Función para poblar el select de subcategorías
    function populateSubcats(catSlug, subSelect, selectedSub = null) {
        subSelect.innerHTML = '<option value="">-- Subcategoría --</option>';
        if (!catSlug || !HIERARCHY[catSlug]) return;
        
        const subs = HIERARCHY[catSlug].subs;
        for (const [slug, label] of Object.entries(subs)) {
            const opt = document.createElement('option');
            opt.value = slug;
            opt.textContent = label;
            if (selectedSub && selectedSub === slug) {
                opt.selected = true;
            }
            subSelect.appendChild(opt);
        }
    }

    // Inicializar todos los selects
    document.querySelectorAll('.cat-select').forEach(sel => {
        const rowId = sel.getAttribute('data-row');
        const subSel = document.getElementById('sub-' + rowId);
        const initialSub = subSel.getAttribute('data-selected');
        
        // Poblar inicialmente
        populateSubcats(sel.value, subSel, initialSub);

        // Evento cambio
        sel.addEventListener('change', function() {
            populateSubcats(this.value, subSel, null);
        });
    });

    // Botones Guardar
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const row = document.getElementById('row-' + id);
            const cat = row.querySelector('.cat-select').value;
            const sub = row.querySelector('.sub-select').value;
            const msg = document.getElementById('msg-' + id);

            msg.textContent = '...';
            
            // Enviar AJAX
            const formData = new FormData();
            formData.append('action', 'update');
            formData.append('id', id);
            formData.append('category', cat);
            formData.append('subcategory', sub);

            fetch('tabla.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.text())
            .then(res => {
                if (res === 'OK') {
                    msg.textContent = 'OK';
                    setTimeout(() => msg.textContent = '', 2000);
                } else {
                    alert('Error: ' + res);
                    msg.textContent = 'Error';
                }
            })
            .catch(err => {
                console.error(err);
                msg.textContent = 'Fail';
            });
        });
    });

});
</script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
