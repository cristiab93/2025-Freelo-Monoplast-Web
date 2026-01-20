<?php
include "_general.php";

// --------------------------------------------------------------------------
// 1. DEFINICIÓN DE MAPAS Y NORMALIZACIÓN (Adaptado de lo que pasó el usuario)
// --------------------------------------------------------------------------

$CATEGORY_MAP_APP = [
    'artefactos-y-mamparas' => 'Artefactos y mamparas',
    'calefaccion'           => 'Calefacción',
    'construccion'          => 'Construcción',
    'infraestructura'       => 'Infraestructura',
    'piletas'               => 'Piletas',
    'riego'                 => 'Sistemas de riego', // Adjusted to match site "Sistemas de riego" vs "Riego"
];

// Mapeo simple de subcategorías (Slug -> Nombre Bonito)
$SUBCATEGORY_MAP_APP = [
    'artefactos'                   => 'Artefactos',
    'griferia'                     => 'Grifería',
    'mamparas'                     => 'Mamparas',
    'calderas'                     => 'Calderas',
    'losa-radiante'                => 'Losa radiante',
    'radiadores'                   => 'Radiadores',
    'termotanques'                 => 'Termotanques',
    'agua'                         => 'Agua',
    'gas'                          => 'Gas',
    'cloacal'                      => 'Cloacal',
    'pvc'                          => 'PVC',
    'polipropileno'                => 'Polipropileno',
    'galvanizado-epoxi'            => 'Galvanizado / Epoxi',
    'bombas'                       => 'Bombas',
    'canaletas'                    => 'Canaletas',
    'tanques'                      => 'Tanques',
    'valvulas'                     => 'Válvulas',
    'fibra-optica'                 => 'Fibra óptica',
    'canos'                        => 'Caños',
    'cano-perfilado'               => 'Caño perfilado',
    'pead'                         => 'PEAD',
    'electrobombas'                => 'Electrobombas',
    'filtros'                      => 'Filtros',
    'limpieza-y-mantenimiento'     => 'Limpieza y mantenimiento',
    'productos-para-mantenimiento' => 'Productos para mantenimiento',
    'mangueras'                    => 'Mangueras',
    'aspersores'                   => 'Aspersores',
    'canos-hidraulicos'            => 'Caños hidráulicos',
];

// Estructura Jerárquica para validación
$HIERARCHY = [
    'artefactos-y-mamparas' => ['artefactos','griferia','mamparas'],
    'calefaccion' => ['calderas','losa-radiante','radiadores','termotanques'],
    'construccion' => ['agua','gas','cloacal','pvc','polipropileno','galvanizado-epoxi','bombas','canaletas','tanques','valvulas'],
    'infraestructura' => ['fibra-optica','canos','cano-perfilado','pead'],
    'piletas' => ['electrobombas','filtros','limpieza-y-mantenimiento','productos-para-mantenimiento','mangueras'],
    'riego' => ['aspersores','mangueras','canos-hidraulicos']
];

// Variaciones de nombres de categoría en "p" -> Slug correcto
$CAT_ALIASES = [
    'artefactos y mamparas' => 'artefactos-y-mamparas',
    'calefaccion' => 'calefaccion',
    'calefacción' => 'calefaccion',
    'construccion' => 'construccion',
    'construcción' => 'construccion', 
    'infraestructura' => 'infraestructura',
    'piletas' => 'piletas',
    'riego' => 'riego',
    'sistemas de riego' => 'riego'
];

// function normalize_slug_custom($str) {
//     if (!$str) return '';
//     $str = mb_strtolower($str, 'UTF-8');
//     // Manual replacements for common accented chars to ensure consistency
//     $replacements = [
//         'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
//         'ñ' => 'n', 'ü' => 'u', 'ç' => 'c'
//     ];
//     $str = strtr($str, $replacements);
//     $str = preg_replace('/[^a-z0-9]+/', '-', $str);
//     $str = trim($str, '-');
//     return $str;
// }

function normalize_slug_custom($str) {
    if (!$str) return '';
    $str = mb_strtolower($str, 'UTF-8');
    // Manual replacements for accented chars
    $replacements = [
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'ñ'=>'n', 'ü'=>'u', 'ç'=>'c'
    ];
    $str = strtr($str, $replacements);
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    return trim($str, '-');
}

function resolve_category_from_path($path_str) {
    global $CAT_ALIASES, $HIERARCHY;
    
    // El path suele ser "Construcción | Pedido de presupuesto | Construcción / Agua"
    // Separamos por "|" 
    $parts = explode('|', $path_str);
    
    // Estrategia: Buscar el segmento más específico que tenga formato "Cat / Sub" O "Cat / Sub / OtraCosa"
    // Priorizamos segmentos con "/" que coincidan con nuestra jerarquía.
    
    foreach ($parts as $segment) {
        $subparts = explode('/', $segment);
        $subparts = array_map('trim', $subparts);
        
        if (count($subparts) >= 2) {
            // Tenemos al menos "Cat / Algo"
            $catRaw = normalize_slug_custom($subparts[0]);
            
            // 1. Identificar Categoria
            $catSlug = null;
            foreach ($HIERARCHY as $hCat => $hSubs) {
                // Chequeo exacto del slug de la categoría
                if ($catRaw === $hCat || $catRaw === str_replace('-', '', $hCat)) {
                   $catSlug = $hCat; 
                   break;
                }
            }
            if (!$catSlug && isset($CAT_ALIASES[str_replace('-',' ',$catRaw)])) {
                $catSlug = $CAT_ALIASES[str_replace('-',' ',$catRaw)];
            }
            
            if ($catSlug) {
                // 2. Identificar Subcategoria
                // Iteramos sobre las partes siguientes para ver si alguna matchea con subcategorias válidas
                // Ejemplo: "Construcción / Agua / IPS" -> subparts[1]="Agua", subparts[2]="IPS"
                
                $validSubs = $HIERARCHY[$catSlug];
                
                // Revisamos subparts[1] ... end
                for ($i = 1; $i < count($subparts); $i++) {
                    $subRaw = normalize_slug_custom($subparts[$i]);
                    // Check exact or contains
                    if (in_array($subRaw, $validSubs)) {
                        return ['cat' => $catSlug, 'sub' => $subRaw];
                    }
                    // Fuzzy check
                    foreach ($validSubs as $vs) {
                        // "agua" == "agua"
                        if ($subRaw === $vs) return ['cat' => $catSlug, 'sub' => $vs];
                    }
                }
                
                // Si llegamos aquí, encontramos categoría pero no subcategoría en este segmento.
                // Podríamos retornar solo categoría, pero mejor seguimos buscando por si otro segmento tiene la subcategoría.
                // (O retornamos cat y sub vacío si es lo mejor que tenemos)
            }
        }
    }
    
    // Si no encontramos match compuesto, buscamos match simple de categoría
    foreach ($parts as $segment) {
        $clean = normalize_slug_custom($segment);
        foreach ($HIERARCHY as $hCat => $hSubs) {
            // Match exacto o alias
            if ($clean === $hCat) return ['cat' => $hCat, 'sub' => ''];
            if (isset($CAT_ALIASES[str_replace('-',' ',$clean)]) && $CAT_ALIASES[str_replace('-',' ',$clean)] === $hCat) {
                return ['cat' => $hCat, 'sub' => ''];
            }
        }
    }

    return ['cat' => '', 'sub' => ''];
}

// --------------------------------------------------------------------------
// 2. PROCESAMIENTO
// --------------------------------------------------------------------------

$msg = '';

// EJECUTAR MIGRACIÓN
if (isset($_POST['migrate']) && $_POST['migrate'] === '1') {
    $qAll = "SELECT * FROM p";
    $rAll = mysqli_query($conn, $qAll);
    $count = 0;
    while ($row = mysqli_fetch_assoc($rAll)) {
        $res = resolve_category_from_path($row['categorias_path'] ?? '');
        if ($res['cat']) {
            // Actualizar tabla products
            // Usamos product_id como clave (asumiendo que coinciden)
            $pid = (int)$row['product_id'];
            $c = $res['cat'];
            $s = $res['sub'];
            
            // Verificar si el producto existe en 'products' antes de hacer update (opcional)
            // Hacemos UPDATE directo
            $upd = "UPDATE products SET product_category = '$c', product_subcategory = '$s' WHERE product_id = $pid";
            mysqli_query($conn, $upd);
            $count++;
        }
    }
    $msg = "Se actualizaron $count productos exitosamente.";
}


$query = "SELECT * FROM p LIMIT 200"; 
$result = mysqli_query($conn, $query);

$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $path = $r['categorias_path'] ?? '';
    // Intentar resolver
    $resolved = resolve_category_from_path($path);
    
    $r['found_cat'] = $resolved['cat'];
    $r['found_sub'] = $resolved['sub'];
    
    $r['found_cat_name'] = $CATEGORY_MAP_APP[$resolved['cat']] ?? '-';
    $r['found_sub_name'] = $SUBCATEGORY_MAP_APP[$resolved['sub']] ?? '-';
    
    $rows[] = $r;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Migración de Tabla P</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Análisis de Migración (Tabla 'p')</h1>
        <form method="post" onsubmit="return confirm('¿Estás seguro de migrar estos datos a la tabla products?');">
            <input type="hidden" name="migrate" value="1">
            <button class="btn btn-danger">EJECUTAR MIGRACIÓN AHORA</button>
        </form>
    </div>
    
    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <p>Mostrando interpretación de categorías basada en <code>categorias_path</code>.</p>
    
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Slug</th>
                    <th>Desc. Corta</th>
                    <th>Desc. Larga</th>
                    <th>Precio</th>
                    <th>Medidas</th>
                    <th>Categorias Path (Original)</th>
                    <th>Cat Detectada (Slug)</th>
                    <th>Sub Detectada (Slug)</th>
                    <th class="bg-primary text-white">Cat Final</th>
                    <th class="bg-primary text-white">Sub Final</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= $row['product_id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td class="small"><?= htmlspecialchars($row['slug']) ?></td>
                    <td class="small text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($row['descripcion_corta']) ?>"><?= htmlspecialchars($row['descripcion_corta']) ?></td>
                    <td class="small text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($row['descripcion_larga']) ?>"><?= htmlspecialchars($row['descripcion_larga']) ?></td>
                    <td><?= htmlspecialchars($row['precio_actual']) ?></td>
                    <td><?= htmlspecialchars($row['medidas']) ?></td>

                    <td class="small text-muted"><?= htmlspecialchars($row['categorias_path']) ?></td>
                    
                    <td class="<?= $row['found_cat'] ? 'text-success fw-bold' : 'text-danger' ?>">
                        <?= $row['found_cat'] ?: '?' ?>
                    </td>
                    <td class="<?= $row['found_sub'] ? 'text-success' : 'text-warning' ?>">
                        <?= $row['found_sub'] ?: '-' ?>
                    </td>
                    
                    <td><?= $row['found_cat_name'] ?></td>
                    <td><?= $row['found_sub_name'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
