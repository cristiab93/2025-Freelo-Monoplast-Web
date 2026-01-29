<?php
$PAGE = 'admin-subcategories';
include('../_general.php');

/* ====================== helpers ====================== */
function fetch_categories(){
    $rows = SelectQuery('categories')->Order('category_name','ASC')->Limit(1000000)->SetIndex(-1)->Run();
    return is_array($rows) ? array_values($rows) : [];
}
function cat_exists_by_key($key){
    $key = trim($key);
    if ($key === '') return false;
    $r = SelectQuery('categories')->Condition('category_key_word =','s',$key)->Limit(1)->Run();
    return !empty($r);
}
/* nombre duplicado dentro del mismo father (keyword), case-insensitive */
function subcat_exists($name, $key, $father_key, $exclude_id = 0){
    $name = trim($name);
    $key  = trim($key);
    $father_key = trim($father_key);
    if ($name === '' || $father_key === '') return false;
    
    // Check name + father
    $q = SelectQuery('sub_categories')
        ->Condition('sub_category_father =','s',$father_key) // Father is string now
        ->Condition('LOWER(sub_category_name) =','s', mb_strtolower($name,'UTF-8'));
    if ($exclude_id > 0) $q->Condition('sub_category_id <>','i',(int)$exclude_id);
    $r = $q->Limit(1)->Run();
    if (!empty($r)) return 'name_dup';
    
    // Check key
    if ($key !== '') {
        $q2 = SelectQuery('sub_categories')->Condition('sub_category_key_word =','s',$key);
        if ($exclude_id > 0) $q2->Condition('sub_category_id <>','i',(int)$exclude_id);
        $r2 = $q2->Limit(1)->Run();
        if (!empty($r2)) return 'key_dup';
    }

    return false;
}

function count_products_in_subcat($keyword) {
    if (trim($keyword) === '') return 0;
    $rows = SelectQuery('products')->Condition('product_subcategory =', 's', $keyword)->Run();
    return is_array($rows) ? count($rows) : 0;
}

/* ====================== acciones CRUD ====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = trim($_POST['sub_category_name'] ?? '');
        $key = trim($_POST['sub_category_key_word'] ?? '');
        $father = trim($_POST['sub_category_father'] ?? ''); // Keyword string

        if ($name === '' || $father === '' || $key === '') { header('Location: subcategories.php?m=invalid'); exit; }
        if (!cat_exists_by_key($father))   { header('Location: subcategories.php?m=nofather'); exit; }
        
        $dup = subcat_exists($name, $key, $father);
        if ($dup === 'name_dup'){ header('Location: subcategories.php?m=dup'); exit; }
        if ($dup === 'key_dup') { header('Location: subcategories.php?m=dup_key'); exit; }

        InsertQuery('sub_categories')
            ->Value('sub_category_name','s',$name)
            ->Value('sub_category_key_word','s',$key)
            ->Value('sub_category_father','s',$father) // Save as string
            ->Run();

        header('Location: subcategories.php?m=created'); exit;
    }

    if ($action === 'update') {
        $id     = (int)($_POST['sub_category_id'] ?? 0);
        $name   = trim($_POST['sub_category_name'] ?? '');
        $key    = trim($_POST['sub_category_key_word'] ?? '');
        $father = trim($_POST['sub_category_father'] ?? '');

        if ($id <= 0 || $name === '' || $father === '' || $key === '') { header('Location: subcategories.php?m=invalid'); exit; }
        if (!cat_exists_by_key($father))               { header('Location: subcategories.php?m=nofather'); exit; }
        
        $dup = subcat_exists($name, $key, $father, $id);
        if ($dup === 'name_dup')       { header('Location: subcategories.php?m=dup'); exit; }
        if ($dup === 'key_dup')       { header('Location: subcategories.php?m=dup_key'); exit; }

        UpdateQuery('sub_categories')
            ->Value('sub_category_name','s',$name)
            ->Value('sub_category_key_word','s',$key)
            ->Value('sub_category_father','s',$father)
            ->Condition('sub_category_id =','i',$id)
            ->Run();

        header('Location: subcategories.php?m=updated'); exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['sub_category_id'] ?? 0);
        if ($id > 0) {
            $curr = SelectQuery('sub_categories')->Condition('sub_category_id =','i',$id)->Limit(1)->Run();
            if ($curr && is_array($curr)) {
                $row = array_values($curr)[0];
                $key = $row['sub_category_key_word'];
                
                $count = count_products_in_subcat($key);
                if ($count > 0) {
                    header('Location: subcategories.php?m=has_products'); exit;
                }
                
                DeleteQuery('sub_categories')->Condition('sub_category_id =','i',$id)->Run();
            }
        }
        header('Location: subcategories.php?m=deleted'); exit;
    }
}

/* ====================== fetch ====================== */
$sub_rows = SelectQuery('sub_categories')
    ->Order('sub_category_father','ASC')
    ->Order('sub_category_name','ASC')
    ->Limit(1000000)->SetIndex(-1)->Run();
$subcats = is_array($sub_rows) ? array_values($sub_rows) : [];

$cats = fetch_categories();
$cat_map = []; // Key -> Name
foreach ($cats as $c) $cat_map[$c['category_key_word']] = $c['category_name'];
$has_categories = count($cats) > 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <title>Monoplast - Admin Subcategorías</title>
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
            max-width: 1100px;
            margin: 0 auto;
        }
        .inicioAdmin.jumbotron{
            background:#fff;
            padding:20px;
            border-radius:10px;
        }
        .table-wrap{overflow-x:auto;}
        .table td,.table th{vertical-align:middle}
    </style>
</head>
<body>
<?php ShowAdminNavBar('nav-subcategories'); ?>

<div class="area"></div>

<div class="contenidoAdmin">
  <div class="inicioAdmin jumbotron">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Subcategorías</h2>
      <button class="btn btn-primary" type="button" id="btnCreate" <?php if(!$has_categories) echo 'disabled'; ?>>
        <i class="fa fa-plus"></i> Nueva subcategoría
      </button>
    </div>

    <?php if (!$has_categories): ?>
      <div class="alert alert-warning mt-3">
        Primero creá al menos una <strong>categoría</strong> para poder asignarla como padre.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['m'])): ?>
        <?php if ($_GET['m'] === 'created'): ?>
        <div class="alert alert-success mt-3">Subcategoría creada</div>
        <?php elseif ($_GET['m'] === 'updated'): ?>
        <div class="alert alert-info mt-3">Subcategoría actualizada</div>
        <?php elseif ($_GET['m'] === 'deleted'): ?>
        <div class="alert alert-warning mt-3">Subcategoría eliminada</div>
        <?php elseif ($_GET['m'] === 'invalid'): ?>
        <div class="alert alert-danger mt-3">Datos incompletos</div>
        <?php elseif ($_GET['m'] === 'dup'): ?>
        <div class="alert alert-danger mt-3">Ya existe una subcategoría con ese nombre en esa categoría</div>
        <?php elseif ($_GET['m'] === 'dup_key'): ?>
        <div class="alert alert-danger mt-3">Ya existe una subcategoría con esa KEY (identificador)</div>
        <?php elseif ($_GET['m'] === 'nofather'): ?>
        <div class="alert alert-danger mt-3">La categoría padre no existe</div>
        <?php elseif ($_GET['m'] === 'has_products'): ?>
        <div class="alert alert-danger mt-3">No se puede eliminar: Hay productos asociados a esta subcategoría.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="table-wrap mt-3">
      <table class="table table-bordered table-hover mb-0">
        <thead>
          <tr>
            <th style="width:110px">ID</th>
            <th>Subcategoría</th>
            <th>Key (ID único)</th>
            <th style="width:320px">Categoría padre</th>
            <th style="width:230px">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!count($subcats)): ?>
          <tr><td colspan="5">No hay subcategorías cargadas</td></tr>
        <?php else: foreach ($subcats as $s): 
            $f_key = $s['sub_category_father'] ?? '';
            $father_name = $cat_map[$f_key] ?? ('('.$f_key.')');
        ?>
          <tr>
            <td><?= (int)$s['sub_category_id'] ?></td>
            <td><?= htmlspecialchars($s['sub_category_name'] ?? '') ?></td>
            <td><code><?= htmlspecialchars($s['sub_category_key_word'] ?? '') ?></code></td>
            <td><?= htmlspecialchars($father_name) ?></td>
            <td>
              <button
                class="btn btn-sm btn-secondary btn-edit"
                data-id="<?= (int)$s['sub_category_id'] ?>"
                data-name="<?= htmlspecialchars($s['sub_category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                data-key="<?= htmlspecialchars($s['sub_category_key_word'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                data-father-key="<?= htmlspecialchars($s['sub_category_father'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                type="button">
                Editar
              </button>
              <form method="post" action="subcategories.php" style="display:inline" onsubmit="return confirm('¿Eliminar esta subcategoría?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="sub_category_id" value="<?= (int)$s['sub_category_id'] ?>">
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
  <div class="modal-dialog modal-dialog-centered" style="max-width: min(800px, 95vw);">
    <form method="post" action="subcategories.php" class="modal-content" id="createForm">
      <div class="modal-header">
        <h5 class="modal-title">Nueva subcategoría</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="create">
        
        <div class="form-group mb-3">
            <label>Nombre</label>
            <input type="text" name="sub_category_name" id="c_name" class="form-control" placeholder="Nombre de la subcategoría" required>
        </div>
        
        <div class="form-group mb-3">
             <label>Key (Identificador único)</label>
             <!-- Visual only -->
             <input type="text" id="c_key_vis" class="form-control" readonly style="background-color:#e9ecef; border:1px solid #ced4da; color:#6c757d;">
             <input type="hidden" name="sub_category_key_word" id="c_key">
             <small class="text-muted">Se genera automáticamente.</small>
        </div>

        <div class="form-group">
            <label>Categoría padre</label>
            <select name="sub_category_father" id="c_father" class="form-control" required>
                <option value="" disabled selected>Seleccioná una categoría</option>
                <?php foreach ($cats as $c): ?>
                    <option value="<?= htmlspecialchars($c['category_key_word']) ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <?php if (!$has_categories): ?>
          <small class="text-danger d-block mt-2">No hay categorías: no podrás guardar hasta crear alguna.</small>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light cancel-create" data-dismiss="modal" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnCreateSave" <?php if(!$has_categories) echo 'disabled'; ?>>Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: min(800px, 95vw);">
    <form method="post" action="subcategories.php" class="modal-content" id="editForm">
      <div class="modal-header">
        <h5 class="modal-title">Editar subcategoría</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="sub_category_id" id="e_id">
        
        <div class="form-group mb-3">
            <label>Nombre</label>
            <input type="text" name="sub_category_name" id="e_name" class="form-control" required>
        </div>
        
        <div class="form-group mb-3">
            <label>Key</label>
            <input type="text" id="e_key_vis" class="form-control" readonly style="background-color:#e9ecef; border:1px solid #ced4da; color:#6c757d;">
            <input type="hidden" name="sub_category_key_word" id="e_key">
        </div>

        <div class="form-group">
            <label>Categoría padre</label>
            <select name="sub_category_father" id="e_father" class="form-control" required>
                <option value="" disabled>Seleccioná una categoría</option>
                <?php foreach ($cats as $c): ?>
                    <option value="<?= htmlspecialchars($c['category_key_word']) ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light cancel-edit" data-dismiss="modal" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Actualizar</button>
      </div>
    </form>
  </div>
</div>

<script>
$(function(){
  var isBS5 = typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function';
  var createEl = document.getElementById('createModal');
  var editEl   = document.getElementById('editModal');
  var createModal = isBS5 ? new bootstrap.Modal(createEl) : null;
  var editModal   = isBS5 ? new bootstrap.Modal(editEl)   : null;

  $('#btnCreate').on('click', function(){
    $('#createForm')[0].reset();
    if (isBS5) { createModal.show(); } else { $('#createModal').modal('show'); }
  });
  
  $('.btn-edit').on('click', function(){
    var $b = $(this);
    $('#e_id').val($b.data('id'));
    $('#e_name').val($b.data('name'));
    
    var key = $b.data('key');
    $('#e_key').val(key);
    $('#e_key_vis').val(key);
    
    $('#e_father').val($b.data('father-key')); // String key
    
    if (isBS5) { editModal.show(); } else { $('#editModal').modal('show'); }
  });

  // Simple auto-slugger for Create
  $('#c_name').on('input', function(){
     var val = $(this).val();
     var slug = val.toLowerCase()
        .replace(/á/g, 'a').replace(/é/g, 'e').replace(/í/g, 'i').replace(/ó/g, 'o').replace(/ú/g, 'u').replace(/ñ/g, 'n')
        .replace(/[^a-z0-9 ]/g, '')
        .replace(/\s+/g, '-');
     $('#c_key').val(slug);
     $('#c_key_vis').val(slug);
  });
});
</script>
</body>
</html>
