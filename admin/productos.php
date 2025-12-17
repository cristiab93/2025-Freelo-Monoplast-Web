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
$cat_rows = SelectQuery('categories')->Order('category_name','ASC')->Run();
$cat_rows = is_array($cat_rows) ? array_values($cat_rows) : [];

$sub_rows = SelectQuery('sub_categories')->Order('sub_category_name','ASC')->Run();
$sub_rows = is_array($sub_rows) ? array_values($sub_rows) : [];

$CATS = [];
foreach ($cat_rows as $r) {
    $CATS[(int)$r['category_id']] = trim($r['category_name']);
}

$SUBS = [];           // id => ['name'=>..., 'father'=>...]
$SUBS_BY_FATHER = []; // fatherId => [ ['id'=>..., 'name'=>...], ... ]
foreach ($sub_rows as $r) {
    $id = (int)$r['sub_category_id'];
    $fa = (int)$r['sub_category_father'];
    $nm = trim($r['sub_category_name']);
    $SUBS[$id] = ['name'=>$nm, 'father'=>$fa];
    if (!isset($SUBS_BY_FATHER[$fa])) $SUBS_BY_FATHER[$fa] = [];
    $SUBS_BY_FATHER[$fa][] = ['id'=>$id, 'name'=>$nm];
}

/* ===== Acciones CRUD ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name        = trim($_POST['product_name'] ?? '');
        $subname     = trim($_POST['product_subname'] ?? '');
        $category_id = (int)($_POST['product_category'] ?? 0);
        $subcat_id   = (int)($_POST['product_subcategory'] ?? 0);
        $description = trim($_POST['product_description'] ?? '');
        $date        = date('Y-m-d');

        if ($name === '' || $category_id <= 0) { header('Location: productos.php?m=invalid'); exit; }

        $imgErr = '';
        $uploaded_img = handle_image_upload('product_img_file',$imgErr);
        if ($imgErr === 'bad_img_type') { header('Location: productos.php?m=badimg'); exit; }
        if ($imgErr === 'error_img')     { header('Location: productos.php?m=uploadimg'); exit; }

        InsertQuery('products')
            ->Value('product_name','s',$name)
            ->Value('product_subname','s',$subname)
            ->Value('product_category','i',$category_id)   // guarda ID
            ->Value('product_subcategory','i',$subcat_id)  // guarda ID (0 = sin subcat)
            ->Value('product_img','s',$uploaded_img ?? '')
            ->Value('product_description','s',$description)
            ->Value('product_date','s',$date)
            ->Run();

        header('Location: productos.php?m=created'); exit;
    }

    if ($action === 'update') {
        $id          = (int)($_POST['product_id'] ?? 0);
        $name        = trim($_POST['product_name'] ?? '');
        $subname     = trim($_POST['product_subname'] ?? '');
        $category_id = (int)($_POST['product_category'] ?? 0);
        $subcat_id   = (int)($_POST['product_subcategory'] ?? 0);
        $description = trim($_POST['product_description'] ?? '');

        if ($id <= 0 || $name === '' || $category_id <= 0) { header('Location: productos.php?m=invalid'); exit; }

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

        UpdateQuery('products')
            ->Value('product_name','s',$name)
            ->Value('product_subname','s',$subname)
            ->Value('product_category','i',$category_id)
            ->Value('product_subcategory','i',$subcat_id)
            ->Value('product_img','s', $uploaded_img !== null ? $uploaded_img : $keep_img)
            ->Value('product_description','s',$description)
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

/* helpers de presentación para datos viejos (texto) */
function view_cat_name($raw, $CATS){
    if (is_numeric($raw)) {
        $id = (int)$raw;
        return $CATS[$id] ?? (string)$raw;
    }
    // fallback por nombre
    $txt = trim((string)$raw);
    foreach ($CATS as $id=>$nm) if (strcasecmp($nm,$txt)===0) return $nm;
    return $txt;
}
function view_sub_name($raw, $SUBS){
    if (is_numeric($raw)) {
        $id = (int)$raw;
        return $SUBS[$id]['name'] ?? (string)$raw;
    }
    $txt = trim((string)$raw);
    foreach ($SUBS as $id=>$obj) if (strcasecmp($obj['name'],$txt)===0) return $obj['name'];
    return $txt ?: '—';
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

    <link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome 5 + v4-shims (sin integrity) para navbar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/v4-shims.min.css">

    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/admin.css" type="text/css">

    <style>
        .contenidoAdmin{
            padding: 20px 16px 60px 84px; /* deja espacio al sidebar fijo */
            max-width: 1400px;
            margin: 0 auto;
        }
        .inicioAdmin.jumbotron{
            background:#fff;
            padding:20px;
            border-radius:10px;
        }
        .table-wrap{overflow-x:auto;}

        /* Miniatura grande */
        .mini-thumb{
            width:124px; height:90px; /* ~50% más grande que 84x60 */
            object-fit:contain;
            border:1px solid #eee;border-radius:4px;background:#fafafa
        }
        .table td,.table th{vertical-align:middle}
        .badge-cat{background:#0d6efd;color:#fff;border-radius:999px;padding:6px 10px;font-size:12px}
    </style>
</head>
<body>
<?php ShowAdminNavBar('nav-products'); ?>
<div class="area"></div>

<div class="contenidoAdmin">
  <div class="inicioAdmin jumbotron">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Productos</h2>
      <button class="btn btn-primary" type="button" id="btnCreate">Nuevo producto</button>
    </div>

    <?php if (isset($_GET['m']) && $_GET['m'] === 'created'): ?>
      <div class="alert alert-success mt-3">Producto creado</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'updated'): ?>
      <div class="alert alert-info mt-3">Producto actualizado</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'deleted'): ?>
      <div class="alert alert-warning mt-3">Producto eliminado</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'invalid'): ?>
      <div class="alert alert-danger mt-3">Datos incompletos</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'badimg'): ?>
      <div class="alert alert-danger mt-3">Imagen inválida. Permitidos: jpg, jpeg, png, svg, webp</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'uploadimg'): ?>
      <div class="alert alert-danger mt-3">No se pudo subir la imagen</div>
    <?php endif; ?>

    <div class="table-wrap mt-3">
      <table class="table table-bordered table-hover mb-0">
        <thead>
          <tr>
            <th style="width:72px">ID</th>
            <th style="width:140px">Imagen</th>
            <th>Nombre</th>
            <th>Subnombre</th>
            <th>Categoría</th>
            <th>Subcategoría</th>
            <th style="width:120px">Fecha</th>
            <th style="width:230px">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!count($products)): ?>
          <tr><td colspan="8">No hay productos cargados</td></tr>
        <?php else: foreach ($products as $p):
              $pcat = $p['product_category'] ?? 0;
              $psub = $p['product_subcategory'] ?? 0;
              // nombres legibles con fallback
              $catName = view_cat_name($pcat, $CATS);
              $subName = view_sub_name($psub, $SUBS);
              // ids para modal editar (fallback si son textos)
              $catIdForBtn = is_numeric($pcat) ? (int)$pcat : array_search($catName, $CATS, true);
              if ($catIdForBtn === false) $catIdForBtn = 0;
              $subIdForBtn = is_numeric($psub) ? (int)$psub : 0;
        ?>
          <tr>
            <td><?= (int)$p['product_id'] ?></td>
            <td><?php if (!empty($p['product_img'])): ?>
              <img class="mini-thumb" src="../uploaded_img/<?= htmlspecialchars($p['product_img']) ?>" alt="">
            <?php endif; ?></td>
            <td><?= htmlspecialchars($p['product_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($p['product_subname'] ?? '') ?></td>
            <td><span class="badge-cat"><?= htmlspecialchars($catName) ?></span></td>
            <td><?= htmlspecialchars($subName) ?></td>
            <td><?= htmlspecialchars($p['product_date'] ?? '') ?></td>
            <td>
              <button
                  class="btn btn-sm btn-secondary btn-edit"
                  data-id="<?= (int)$p['product_id'] ?>"
                  data-name="<?= htmlspecialchars($p['product_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-subname="<?= htmlspecialchars($p['product_subname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-description="<?= htmlspecialchars($p['product_description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                  data-category-id="<?= (int)$catIdForBtn ?>"
                  data-subcategory-id="<?= (int)$subIdForBtn ?>"
                  type="button">Editar</button>

              <form method="post" action="productos.php" style="display:inline" onsubmit="return confirm('¿Eliminar este producto?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
                <button class="btn btn-sm btn-danger" type="submit">Eliminar</button>
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
  <div class="modal-dialog modal-dialog-centered" style="max-width: min(1200px, 95vw);">
    <form method="post" action="productos.php" class="modal-content" id="createForm" enctype="multipart/form-data">
      <style>
        #createModal .modal-header{padding:18px 22px}
        #createModal .modal-body{padding:16px 22px}
        #createModal .modal-footer{padding:12px 22px}
        #createModal .textarea-compact{height:140px; min-height:120px; resize:vertical;}
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
            <input type="text" name="product_subname" id="c_subname" class="form-control" placeholder="Subnombre">
          </div>
        </div>

        <div class="triple-grid" style="margin-top:12px;">
          <div class="form-group">
            <label>Categoría</label>
            <select name="product_category" id="c_category_id" class="form-control" required>
              <option value="" disabled selected>Seleccioná una categoría</option>
              <?php foreach ($CATS as $cid=>$cname): ?>
                <option value="<?= (int)$cid ?>"><?= htmlspecialchars($cname) ?></option>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Seleccioná una categoría</small>
          </div>

          <div class="form-group">
            <label>Subcategoría</label>
            <select name="product_subcategory" id="c_subcategory_id" class="form-control">
              <option value="0">— Sin subcategoría —</option>
            </select>
          </div>

          <div class="form-group">
            <label>Imagen</label>
            <input type="file" name="product_img_file" id="c_img" class="form-control"
                   accept=".jpg,.jpeg,.png,.svg,.webp,image/jpeg,image/png,image/svg+xml,image/webp">
            <small class="text-muted">Se guarda en /uploaded_img (DB guarda el nombre)</small>
          </div>
        </div>

        <div class="form-group" style="margin-top:12px;">
          <label>Descripción</label>
          <textarea name="product_description" id="c_description" class="form-control textarea-compact" rows="3" placeholder="Descripción"></textarea>
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
  <div class="modal-dialog modal-dialog-centered" style="max-width: min(1200px, 95vw);">
    <form method="post" action="productos.php" class="modal-content" id="editForm" enctype="multipart/form-data">
      <style>
        #editModal .modal-header{padding:18px 22px}
        #editModal .modal-body{padding:16px 22px}
        #editModal .modal-footer{padding:12px 22px}
        #editModal .textarea-compact{height:140px; min-height:120px; resize:vertical;}
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
            <input type="text" name="product_subname" id="e_subname" class="form-control">
          </div>
        </div>

        <div class="triple-grid" style="margin-top:12px;">
          <div class="form-group">
            <label>Categoría</label>
            <select name="product_category" id="e_category_id" class="form-control" required>
              <option value="" disabled>Seleccioná una categoría</option>
              <?php foreach ($CATS as $cid=>$cname): ?>
                <option value="<?= (int)$cid ?>"><?= htmlspecialchars($cname) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Subcategoría</label>
            <select name="product_subcategory" id="e_subcategory_id" class="form-control">
              <option value="0">— Sin subcategoría —</option>
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
          <textarea name="product_description" id="e_description" class="form-control textarea-compact" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light cancel-edit" data-dismiss="modal" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnEditSave">Actualizar</button>
      </div>
    </form>
  </div>
</div>

<script>
// Mapa de subcategorías por categoría (inyectado desde PHP)
window.SUBS_BY_FATHER = <?php
  echo json_encode($SUBS_BY_FATHER, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
?>;

/* Rellena un <select> de subcategorías dado el fatherId */
function fillSubcategorySelect(sel, fatherId, selectedId){
  var $s = $(sel);
  $s.empty();
  $s.append('<option value="0">— Sin subcategoría —</option>');

  var list = (window.SUBS_BY_FATHER || {})[String(fatherId || '')] || [];
  for (var i = 0; i < list.length; i++) {
    var sc = list[i];
    var $opt = $('<option>').val(sc.id).text(sc.name);
    if (String(sc.id) === String(selectedId || '')) $opt.prop('selected', true);
    $s.append($opt);
  }
}

// Alias por compatibilidad si en algún lado quedó este nombre:
function fillCategorySelect(sel, fatherId, selectedId){
  fillSubcategorySelect(sel, fatherId, selectedId);
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
    // limpiar subcats
    $('#c_subcategory_id').html('<option value="0">— Sin subcategoría —</option>');
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

    var catId = $b.data('category-id') || '';
    var subId = $b.data('subcategory-id') || '0';

    $('#e_category_id').val(String(catId));
    fillSubcategorySelect('#e_subcategory_id', catId, subId);

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
</script>
</body>
</html>
