<?php
$PAGE = 'admin-products';
include('../_general.php');

/* ===== Helpers para archivos ===== */
function ensure_dir($dir){
    if (!is_dir($dir)) mkdir($dir, 0775, true);
}
function random_filename($ext){
    do { $name = bin2hex(random_bytes(8)).'.'.$ext; } while(file_exists($name));
    return $name;
}
function handle_image_upload($field,&$error){
    $error = '';
    if (!isset($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) { $error = 'error_img'; return null; }

    $tmp = $_FILES[$field]['tmp_name'];
    $orig = $_FILES[$field]['name'];
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $allowed = ['png','jpg','jpeg','svg','webp'];
    if (!in_array($ext,$allowed)) { $error = 'bad_img_type'; return null; }

    $fi = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($fi,$tmp);
    finfo_close($fi);
    $ok_mimes = ['image/png','image/jpeg','image/svg+xml','image/webp'];
    if (!in_array($mime,$ok_mimes) && $ext !== 'svg') { $error = 'bad_img_type'; return null; }

    $dir = realpath(__DIR__ . '/../uploaded_img');
    if ($dir === false) { $dir = __DIR__ . '/../uploaded_img'; ensure_dir($dir); }

    $final = random_filename($ext);
    $abs   = rtrim($dir,'/').'/'.$final;
    if (!move_uploaded_file($tmp, $abs)) { $error = 'error_img'; return null; }

    return $final;
}

/* ===== Carga de categorías y subcategorías ===== */
/* ===== Carga de categorías y subcategorías (Desde DB con keywords) ===== */
$cat_rows = SelectQuery('categories')->Order('category_name','ASC')->Run();
$cat_rows = is_array($cat_rows) ? array_values($cat_rows) : [];

$sub_rows = SelectQuery('sub_categories')->Order('sub_category_name','ASC')->Run();
$sub_rows = is_array($sub_rows) ? array_values($sub_rows) : [];

$CATS = [];
$CATS_KEY_TO_NAME = [];
foreach ($cat_rows as $r) {
    // We use the KEY as the value in selects, but we might need the name for display
    // The previous code used ID as index. Now we check what the user wants.
    // "key_word ... es el string que luego se cargará en la tabla productos"
    $k = $r['category_key_word'];
    $CATS[$k] = $r['category_name']; // key => name
}

$SUBS_BY_FATHER = []; // fatherKey => [ ['key'=>..., 'name'=>...], ... ]
// Validation: sub_category_father now holds the parent KEY (string)
foreach ($sub_rows as $r) {
    $k = $r['sub_category_key_word'];
    $nm = $r['sub_category_name'];
    $fa = $r['sub_category_father']; // This is a keyword string now
    
    if (!isset($SUBS_BY_FATHER[$fa])) $SUBS_BY_FATHER[$fa] = [];
    $SUBS_BY_FATHER[$fa][] = ['key'=>$k, 'name'=>$nm];
}

/* ===== Acciones CRUD ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Common fields
    $name        = trim($_POST['product_name'] ?? '');
    $subname     = trim($_POST['product_subname'] ?? '');
    // Category inputs now send KEYWORDS
    $category_key = trim($_POST['product_category'] ?? ''); 
    $subcat_key   = trim($_POST['product_subcategory'] ?? '');
    $description = trim($_POST['product_description'] ?? '');
    
    // Validate
    // Strict validation: Name, Subname, Category, SUBCATEGORY, Description MUST be present
    // We treat '0' as invalid now too because it means "Sin subcategoría" which user doesn't want allowed.
    if ($subcat_key === '0' || $subcat_key === '') {
        header('Location: productos.php?m=invalid'); exit;
    }

    if ($name === '' || $subname === '' || $category_key === '' || $description === '') { 
        header('Location: productos.php?m=invalid'); exit; 
    }

    if ($action === 'create') {
        $date = date('Y-m-d');
        
        $imgErr = '';
        $uploaded_img = handle_image_upload('product_img_file',$imgErr); // Image is optional
        if ($imgErr === 'bad_img_type') { header('Location: productos.php?m=badimg'); exit; }
        if ($imgErr === 'error_img')     { header('Location: productos.php?m=uploadimg'); exit; }

        $measures = $_POST['product_measures'] ?? [];
        $measures_str = implode(',', array_map('trim', array_filter($measures)));

        InsertQuery('products')
            ->Value('product_name','s',$name)
            ->Value('product_subname','s',$subname)
            ->Value('product_category','s',$category_key)      // Store KEY
            ->Value('product_subcategory','s',$subcat_key)     // Store KEY
            ->Value('product_img','s',$uploaded_img ?? '')
            ->Value('product_description','s',$description)
            ->Value('product_size','s',$measures_str)
            ->Value('product_date','s',$date)
            ->Run();

        header('Location: productos.php?m=created'); exit;
    }

    if ($action === 'update') {
        $id = (int)($_POST['product_id'] ?? 0);
        if ($id <= 0) { header('Location: productos.php?m=invalid'); exit; }

        $current = SelectQuery('products')->Condition('product_id =','i',$id)->Limit(1)->Run();
        $keep_img = '';
        if ($current && is_array($current)) {
            $row = array_values($current)[0];
            $keep_img = $row['product_img'] ?? '';
        }

        $imgErr = '';
        $uploaded_img = handle_image_upload('product_img_file',$imgErr);
        if ($imgErr === 'bad_img_type') { header('Location: productos.php?m=badimg'); exit; }
        if ($imgErr === 'error_img')     { header('Location: productos.php?m=uploadimg'); exit; }

        $measures = $_POST['product_measures'] ?? [];
        $measures_str = implode(',', array_map('trim', array_filter($measures)));

        UpdateQuery('products')
            ->Value('product_name','s',$name)
            ->Value('product_subname','s',$subname)
            ->Value('product_category','s',$category_key)
            ->Value('product_subcategory','s',$subcat_key)
            ->Value('product_img','s', $uploaded_img !== null ? $uploaded_img : $keep_img)
            ->Value('product_description','s',$description)
            ->Value('product_size','s',$measures_str)
            ->Condition('product_id =','i',$id)
            ->Run();

        header('Location: productos.php?m=updated'); exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['product_id'] ?? 0);
        if ($id > 0) { DeleteQuery('products')->Condition('product_id =','i',$id)->Run(); }
        header('Location: productos.php?m=deleted'); exit;
    }
}

/* ===== Listado de productos ===== */
$rows = SelectQuery('products')->Order('product_date','DESC')->Limit(1000000)->Run();
$products = is_array($rows) ? array_values($rows) : [];

/* helpers de presentación */
// Map KEY -> Name using $CATS array
function view_cat_name($key, $CATS){
    return $CATS[$key] ?? $key;
}
function view_sub_name($subKey, $SUBS_BY_FATHER){
    foreach ($SUBS_BY_FATHER as $father => $list) {
        foreach ($list as $item) {
            if ($item['key'] === $subKey) return $item['name'];
        }
    }
    // If not found, return the key itself or an empty string if the key was empty
    return $subKey;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <title>Monoplast - Admin Productos</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome 5 + v4-shims (sin integrity) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/v4-shims.min.css">

    <!-- Admin styles existentes -->
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/admin.css" type="text/css">

    <style>
        .contenidoAdmin{
            padding: 20px 16px 60px 84px; /* espacio por el sidebar */
            max-width: 1400px;
            margin: 0 auto;
        }
        .inicioAdmin.jumbotron{
            background:#fff;
            padding:20px;
            border-radius:10px;
        }
        .table-wrap{
            overflow-x:auto;
        }
        .table td,.table th{vertical-align:middle}
        .mini-thumb{
            max-width:60px; max-height:60px; object-fit:contain; border-radius:4px;
        }
        .badge-cat{
            background-color:#007bff; color:#fff; padding:4px 8px; border-radius:12px; font-size:0.85rem; display:inline-block;
        }
        /* Dynamic Measures Styles (Pills) */
        .measures-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            border: 1px solid #ced4da;
            padding: 10px;
            border-radius: 4px;
            background: #f8f9fa;
        }
        .measure-pill {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #ced4da;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .measure-pill:focus-within {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .measure-pill input {
            border: none;
            background: transparent;
            width: 70px;
            outline: none;
            padding: 0;
            margin: 0;
            font-size: inherit;
        }
        .btn-remove-pill {
            margin-left: 8px;
            color: #6c757d;
            cursor: pointer;
            font-size: 0.8rem;
        }
        .btn-remove-pill:hover {
            color: #dc3545;
        }
        .btn-add-pill {
            background: #f8f9fa;
            color: #28a745;
            border: 1px dashed #28a745;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-add-pill:hover {
            background: #e2e6ea;
            border-style: solid;
        }
    </style>
</head>
<body>
<?php ShowAdminNavBar('nav-products'); ?>

<div class="area"></div>

<div class="contenidoAdmin">
  <div class="inicioAdmin jumbotron">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Productos</h2>
      <button class="btn btn-danger" type="button" id="btnCreate">Nuevo producto</button>
    </div>

    <?php if (isset($_GET['m'])): ?>
        <?php if ($_GET['m'] === 'created'): ?>
        <div class="alert alert-success mt-3">Producto creado</div>
        <?php elseif ($_GET['m'] === 'updated'): ?>
        <div class="alert alert-info mt-3">Producto actualizado</div>
        <?php elseif ($_GET['m'] === 'deleted'): ?>
        <div class="alert alert-warning mt-3">Producto eliminado</div>
        <?php elseif ($_GET['m'] === 'invalid'): ?>
        <div class="alert alert-danger mt-3">Datos incompletos (Todos los campos son obligatorios excepto imagen)</div>
        <?php elseif ($_GET['m'] === 'badimg'): ?>
        <div class="alert alert-danger mt-3">Formato de imagen no válido</div>
        <?php elseif ($_GET['m'] === 'uploadimg'): ?>
        <div class="alert alert-danger mt-3">Error al subir imagen</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="table-wrap mt-3">
      <table class="table table-bordered table-hover mb-0">
        <thead>
          <tr>
            <th style="width:60px">ID</th>
            <th style="width:100px">Imagen</th>
            <th style="width:180px">Nombre</th>
            <th style="width:220px">Subnombre</th>
            <th>Categoría</th>
            <th>Subcategoría</th>
            <th>Medidas</th>
            <th style="width:120px">Fecha</th>
            <th style="width:160px">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!count($products)): ?>
          <tr><td colspan="8">No hay productos cargados</td></tr>
        <?php else: foreach ($products as $p):
              $pcat = $p['product_category'] ?? ''; // Keyword
              $psub = $p['product_subcategory'] ?? ''; // Keyword
        ?>
          <tr>
            <td><?= (int)$p['product_id'] ?></td>
            <td><?php if (!empty($p['product_img'])): ?>
              <img class="mini-thumb" src="../uploaded_img/<?= htmlspecialchars($p['product_img']) ?>" alt="">
            <?php endif; ?></td>
            <td style="max-width:180px; word-break:break-word;"><?= htmlspecialchars($p['product_name'] ?? '') ?></td>
            <td style="max-width:220px; word-break:break-word;"><?= htmlspecialchars($p['product_subname'] ?? '') ?></td>
            <td><?= htmlspecialchars(view_cat_name($pcat, $CATS)) ?></td>
            <td><?= htmlspecialchars(view_sub_name($psub, $SUBS_BY_FATHER)) ?></td>
            <td>
              <?php 
                $sz = $p['product_size'] ?? '';
                if ($sz) {
                  $parts = explode(',', $sz);
                  foreach ($parts as $part) {
                    echo '<span class="badge badge-light border text-dark mr-1" style="font-size:0.75rem">'.htmlspecialchars(trim($part)).'</span>';
                  }
                }
              ?>
            </td>
            <td><?= htmlspecialchars(view_date($p['product_date'] ?? '')) ?></td>
            <td class="text-nowrap">
              <button
                  class="btn btn-sm btn-secondary btn-edit"
                  data-id="<?= (int)$p['product_id'] ?>"
                  data-name="<?= htmlspecialchars($p['product_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-subname="<?= htmlspecialchars($p['product_subname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-description="<?= htmlspecialchars($p['product_description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-size="<?= htmlspecialchars($p['product_size'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-category-key="<?= htmlspecialchars($pcat, ENT_QUOTES, 'UTF-8') ?>"
                  data-subcategory-key="<?= htmlspecialchars($psub, ENT_QUOTES, 'UTF-8') ?>"
                  type="button" title="Editar">
                <i class="fas fa-pencil-alt"></i>
              </button>

              <form method="post" action="productos.php" style="display:inline" onsubmit="return confirm('¿Eliminar este producto?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                <button class="btn btn-sm btn-danger" type="submit" title="Eliminar">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div style="width:100px; height: 50px"></div>

<!-- CREATE MODAL -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
    <form method="post" action="productos.php" class="modal-content" id="createForm" enctype="multipart/form-data">
      <style>
        #createModal .modal-header{padding:18px 22px}
        #createModal .modal-body{padding:16px 22px}
        #createModal .modal-footer{padding:12px 22px}
        #createModal .textarea-compact{height:80px; min-height:80px; resize:vertical;}
        #createModal .form-grid{display:grid; grid-template-columns: 1fr 1fr; gap:14px;}
        #createModal .triple-grid{display:grid; grid-template-columns: 1fr 1fr 1fr; gap:14px;}
        @media (max-width: 768px){
          #createModal .form-grid{grid-template-columns: 1fr;}
          #createModal .triple-grid{grid-template-columns: 1fr;}
        }
      </style>
      <div class="modal-header">
        <h5 class="modal-title">Nuevo producto</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="create">

        <div class="form-grid">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="product_name" id="c_name" class="form-control" placeholder="Nombre" required>
          </div>
          <div class="form-group">
            <label>Subnombre</label>
            <input type="text" name="product_subname" id="c_subname" class="form-control" placeholder="Subnombre" required>
          </div>
        </div>

        <div class="triple-grid" style="margin-top:12px;">
          <div class="form-group">
            <label>Categoría</label>
            <select name="product_category" id="c_category_id" class="form-control" required>
              <option value="" disabled selected>Seleccioná una categoría</option>
              <?php foreach ($CATS as $cKey=>$cName): ?>
                <option value="<?= htmlspecialchars($cKey) ?>"><?= htmlspecialchars($cName) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Subcategoría</label>
            <select name="product_subcategory" id="c_subcategory_id" class="form-control" required>
              <option value="" disabled selected>— Seleccioná subcategoría —</option>
            </select>
          </div>

          <div class="form-group">
            <label>Imagen</label>
            <input type="file" name="product_img_file" id="c_img" class="form-control"
                   accept=".jpg,.jpeg,.png,.svg,.webp,image/jpeg,image/png,image/svg+xml,image/webp">
            <small class="text-muted">Se guarda en /uploaded_img</small>
          </div>
        </div>

        <div class="form-group" style="margin-top:12px;">
          <label>Descripción</label>
          <textarea name="product_description" id="c_description" class="form-control textarea-compact" rows="3" placeholder="Descripción" required></textarea>
        </div>

        <div class="form-group" style="margin-top:12px;">
          <label class="d-flex justify-content-between">
            Medidas 
          </label>
          <div id="c_measures_container" class="measures-container mt-1">
            <div class="measure-pill btn-add-pill btn-add-measure" data-target="#c_measures_container" title="Agregar medida">
                <i class="fas fa-plus"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light cancel-create" data-dismiss="modal" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnCreateSave">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 800px;">
    <form method="post" action="productos.php" class="modal-content" id="editForm" enctype="multipart/form-data">
      <style>
        #editModal .modal-header{padding:18px 22px}
        #editModal .modal-body{padding:16px 22px}
        #editModal .modal-footer{padding:12px 22px}
        #editModal .textarea-compact{height:80px; min-height:80px; resize:vertical;}
        #editModal .form-grid{display:grid; grid-template-columns: 1fr 1fr; gap:14px;}
        #editModal .triple-grid{display:grid; grid-template-columns: 1fr 1fr 1fr; gap:14px;}
        @media (max-width: 768px){
          #editModal .form-grid{grid-template-columns: 1fr;}
          #editModal .triple-grid{grid-template-columns: 1fr;}
        }
      </style>
      <div class="modal-header">
        <h5 class="modal-title">Editar producto</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="product_id" id="e_id">

        <div class="form-grid">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="product_name" id="e_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Subnombre</label>
            <input type="text" name="product_subname" id="e_subname" class="form-control" required>
          </div>
        </div>

        <div class="triple-grid" style="margin-top:12px;">
          <div class="form-group">
            <label>Categoría</label>
            <select name="product_category" id="e_category_id" class="form-control" required>
              <option value="" disabled>Seleccioná una categoría</option>
              <?php foreach ($CATS as $cKey=>$cName): ?>
                <option value="<?= htmlspecialchars($cKey) ?>"><?= htmlspecialchars($cName) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Subcategoría</label>
            <select name="product_subcategory" id="e_subcategory_id" class="form-control" required>
              <option value="" disabled selected>— Seleccioná subcategoría —</option>
            </select>
          </div>

          <div class="form-group">
            <label>Imagen</label>
            <input type="file" name="product_img_file" id="e_img" class="form-control"
                   accept=".jpg,.jpeg,.png,.svg,.webp,image/jpeg,image/png,image/svg+xml,image/webp">
            <small class="text-muted">Dejar vacío para mantener la actual</small>
          </div>
        </div>

        <div class="form-group" style="margin-top:12px;">
          <label>Descripción</label>
          <textarea name="product_description" id="e_description" class="form-control textarea-compact" rows="3" required></textarea>
        </div>

        <div class="form-group" style="margin-top:12px;">
          <label class="d-flex justify-content-between">
            Medidas 
          </label>
          <div id="e_measures_container" class="measures-container mt-1">
            <div class="measure-pill btn-add-pill btn-add-measure" data-target="#e_measures_container" title="Agregar medida">
                <i class="fas fa-plus"></i>
            </div>
          </div>
        </div>
        <input type="hidden" id="e_size_raw">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light cancel-edit" data-dismiss="modal" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnEditSave">Actualizar</button>
      </div>
    </form>
  </div>
</div>

<script>
// Mapa de subcategorías por categoría (inyectado desde PHP: fatherKEY -> list of {key, name})
window.SUBS_BY_FATHER = <?php
  echo json_encode($SUBS_BY_FATHER, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>;

/* Rellena un <select> de subcategorías dado el fatherKey */
function fillSubcategorySelect(sel, fatherKey, selectedKey){
  var $s = $(sel);
  $s.empty();
  // Default disabled "Select one"
  $s.append('<option value="" disabled selected>— Seleccioná subcategoría —</option>');

  var list = (window.SUBS_BY_FATHER || {})[fatherKey] || [];
  for (var i = 0; i < list.length; i++) {
    var sc = list[i];
    // sc.key is the value
    var $opt = $('<option>').val(sc.key).text(sc.name);
    // Compare strings
    if (sc.key === selectedKey) {
        $opt.prop('selected', true);
    }
    $s.append($opt);
  }
}

$(function(){
  var isBS5 = typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function';
  var createEl = document.getElementById('createModal');
  var editEl   = document.getElementById('editModal');
  var createModal = isBS5 ? new bootstrap.Modal(createEl) : null;
  var editModal   = isBS5 ? new bootstrap.Modal(editEl)   : null;

  // Abrir "Nuevo"
  $('#btnCreate').on('click', function(){
    $('#createForm')[0].reset();
    $('#c_subcategory_id').html('<option value="" disabled selected>— Seleccioná subcategoría —</option>');
    if (isBS5) { createModal.show(); } else { $('#createModal').modal('show'); }
  });

  // Encadenado en CREATE
  $('#c_category_id').on('change', function(){
    fillSubcategorySelect('#c_subcategory_id', this.value, '');
  });

  // Abrir "Editar"
  $('.btn-edit').on('click', function(){
    var $b = $(this);
    $('#e_id').val($b.data('id'));
    $('#e_name').val($b.data('name'));
    $('#e_subname').val($b.data('subname'));
    $('#e_description').val($b.data('description'));

    var catKey = $b.data('category-key') || '';
    var subKey = $b.data('subcategory-key') || '';

    $('#e_category_id').val(catKey);
    fillSubcategorySelect('#e_subcategory_id', catKey, subKey);

    if (isBS5) { editModal.show(); } else { $('#editModal').modal('show'); }
  });

  // Encadenado en EDIT
  $('#e_category_id').on('change', function(){
    fillSubcategorySelect('#e_subcategory_id', this.value, '');
  });

  // Cerrar modales
  $('.cancel-create, #createModal .btn-close').on('click', function(){
    if (isBS5) { createModal.hide(); } else { $('#createModal').modal('hide'); }
  });
  $('.cancel-edit, #editModal .btn-close').on('click', function(){
    if (isBS5) { editModal.hide(); } else { $('#editModal').modal('hide'); }
  });
});

/* Dynamic Measures Helpers (Pills) */
function addMeasureRow(containerId, value = '') {
    var $container = $(containerId);
    var $addBtn = $container.find('.btn-add-pill');
    var html = `
        <div class="measure-pill">
            <input type="text" name="product_measures[]" value="${value}" placeholder="Medida">
            <span class="btn-remove-pill"><i class="fas fa-times"></i></span>
        </div>
    `;
    if ($addBtn.length) {
        $(html).insertBefore($addBtn);
    } else {
        $container.append(html);
    }
}

$(document).on('click', '.btn-add-measure', function() {
    var target = $(this).data('target');
    addMeasureRow(target);
});

$(document).on('click', '.btn-remove-pill', function() {
    $(this).closest('.measure-pill').remove();
});

$(function() {
    // Override the buttons to handle measures
    $('#btnCreate').on('click', function() {
        $('#c_measures_container').find('.measure-pill:not(.btn-add-pill)').remove();
        addMeasureRow('#c_measures_container'); // At least one empty row
    });

    $('.btn-edit').on('click', function() {
        var size = $(this).data('size') || '';
        $('#e_measures_container').find('.measure-pill:not(.btn-add-pill)').remove();
        if (size) {
            var parts = size.split(',');
            parts.forEach(function(p) {
                if (p.trim()) addMeasureRow('#e_measures_container', p.trim());
            });
        } else {
            addMeasureRow('#e_measures_container');
        }
    });
});
</script>
</body>
</html>
</body>
</html>
