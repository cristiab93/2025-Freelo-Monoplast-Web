<?php
$PAGE = 'admin-categories';
include('../_general.php');

/* ------------ helpers ------------ */
function category_exists($name, $keyword, $exclude_id = 0){
    $name = trim($name);
    $keyword = trim($keyword);
    
    // Check name
    $q = SelectQuery('categories')->Condition('LOWER(category_name) =', 's', mb_strtolower($name, 'UTF-8'));
    if ($exclude_id > 0) $q->Condition('category_id <>', 'i', (int)$exclude_id);
    $r = $q->Limit(1)->Run();
    if (!empty($r)) return 'name_dup';

    // Check keyword
    if ($keyword !== '') {
        $q2 = SelectQuery('categories')->Condition('category_key_word =', 's', $keyword);
        if ($exclude_id > 0) $q2->Condition('category_id <>', 'i', (int)$exclude_id);
        $r2 = $q2->Limit(1)->Run();
        if (!empty($r2)) return 'key_dup';
    }

    return false;
}

function count_products_in_category($keyword) {
    if (trim($keyword) === '') return 0;
    $rows = SelectQuery('products')->Condition('product_category =', 's', $keyword)->Run();
    return is_array($rows) ? count($rows) : 0;
}

/* ------------ acciones CRUD ------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = trim($_POST['category_name'] ?? '');
        $key  = trim($_POST['category_key_word'] ?? '');
        
        if ($name === '' || $key === '') { header('Location: categories.php?m=invalid'); exit; }
        
        $dup = category_exists($name, $key);
        if ($dup === 'name_dup') { header('Location: categories.php?m=dup_name'); exit; }
        if ($dup === 'key_dup')  { header('Location: categories.php?m=dup_key'); exit; }

        InsertQuery('categories')
            ->Value('category_name', 's', $name)
            ->Value('category_key_word', 's', $key)
            ->Run();

        header('Location: categories.php?m=created'); exit;
    }

    if ($action === 'update') {
        $id   = (int)($_POST['category_id'] ?? 0);
        $name = trim($_POST['category_name'] ?? '');
        $key  = trim($_POST['category_key_word'] ?? '');

        if ($id <= 0 || $name === '' || $key === '') { header('Location: categories.php?m=invalid'); exit; }

        $dup = category_exists($name, $key, $id);
        if ($dup === 'name_dup') { header('Location: categories.php?m=dup_name'); exit; }
        if ($dup === 'key_dup')  { header('Location: categories.php?m=dup_key'); exit; }

        UpdateQuery('categories')
            ->Value('category_name', 's', $name)
            ->Value('category_key_word', 's', $key)
            ->Condition('category_id =', 'i', $id)
            ->Run();

        header('Location: categories.php?m=updated'); exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['category_id'] ?? 0);
        // Get keyword first to check products
        if ($id > 0) {
            $curr = SelectQuery('categories')->Condition('category_id =','i',$id)->Limit(1)->Run();
            if ($curr && is_array($curr)) {
                $row = array_values($curr)[0];
                $key = $row['category_key_word'];
                
                $count = count_products_in_category($key);
                if ($count > 0) {
                    header('Location: categories.php?m=has_products'); exit;
                }

                DeleteQuery('categories')->Condition('category_id =','i',$id)->Run();
            }
        }
        header('Location: categories.php?m=deleted'); exit;
    }
}

/* ------------ fetch ------------ */
$rows = SelectQuery('categories')->Order('category_name','ASC')->Limit(1000000)->SetIndex(-1)->Run();
$categories = is_array($rows) ? array_values($rows) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <title>Monoplast - Admin Categorías</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/v4-shims.min.css">

    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/admin.css" type="text/css">

    <style>
        .contenidoAdmin{
            padding: 20px 16px 60px 84px;
            max-width: 1000px;
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
<?php ShowAdminNavBar('nav-categories'); ?>

<div class="area"></div>

<div class="contenidoAdmin">
  <div class="inicioAdmin jumbotron">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Categorías</h2>
      <button class="btn btn-primary" type="button" id="btnCreate">
        <i class="fa fa-plus"></i> Nueva categoría
      </button>
    </div>

    <?php if (isset($_GET['m'])): ?>
        <?php if ($_GET['m'] === 'created'): ?>
          <div class="alert alert-success mt-3">Categoría creada</div>
        <?php elseif ($_GET['m'] === 'updated'): ?>
          <div class="alert alert-info mt-3">Categoría actualizada</div>
        <?php elseif ($_GET['m'] === 'deleted'): ?>
          <div class="alert alert-warning mt-3">Categoría eliminada</div>
        <?php elseif ($_GET['m'] === 'invalid'): ?>
          <div class="alert alert-danger mt-3">Datos incompletos</div>
        <?php elseif ($_GET['m'] === 'dup_name'): ?>
          <div class="alert alert-danger mt-3">Ya existe una categoría con ese nombre</div>
        <?php elseif ($_GET['m'] === 'dup_key'): ?>
          <div class="alert alert-danger mt-3">Ya existe una categoría con esa KEY (identificador)</div>
        <?php elseif ($_GET['m'] === 'has_products'): ?>
          <div class="alert alert-danger mt-3">No se puede eliminar: Hay productos asociados a esta categoría.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="table-wrap mt-3">
      <table class="table table-bordered table-hover mb-0">
        <thead>
          <tr>
            <th style="width:80px">ID</th>
            <th>Nombre</th>
            <th>Key (Identificador)</th>
            <th style="width:200px">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!count($categories)): ?>
          <tr><td colspan="4">No hay categorías cargadas</td></tr>
        <?php else: foreach ($categories as $c): ?>
          <tr>
            <td><?= (int)$c['category_id'] ?></td>
            <td><?= htmlspecialchars($c['category_name'] ?? '') ?></td>
            <td><code><?= htmlspecialchars($c['category_key_word'] ?? '') ?></code></td>
            <td>
              <button
                class="btn btn-sm btn-secondary btn-edit"
                data-id="<?= (int)$c['category_id'] ?>"
                data-name="<?= htmlspecialchars($c['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                data-key="<?= htmlspecialchars($c['category_key_word'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                type="button">
                Editar
              </button>
              <form method="post" action="categories.php" style="display:inline" onsubmit="return confirm('¿Eliminar esta categoría?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="category_id" value="<?= (int)$c['category_id'] ?>">
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
  <div class="modal-dialog modal-dialog-centered" style="max-width: min(700px, 95vw);">
    <form method="post" action="categories.php" class="modal-content" id="createForm">
      <div class="modal-header">
        <h5 class="modal-title">Nueva categoría</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="create">
        <div class="form-group mb-3">
            <label>Nombre</label>
            <input type="text" name="category_name" id="c_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Key (Identificador único)</label>
            <!-- Visual only -->
            <input type="text" id="c_key_vis" class="form-control" readonly style="background-color:#e9ecef; border:1px solid #ced4da; color:#6c757d;">
            <!-- Hidden value sent to server -->
            <input type="hidden" name="category_key_word" id="c_key">
            <small class="text-muted">Se genera automáticamente.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light cancel-create" data-dismiss="modal" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: min(700px, 95vw);">
    <form method="post" action="categories.php" class="modal-content" id="editForm">
      <div class="modal-header">
        <h5 class="modal-title">Editar categoría</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="category_id" id="e_id">
        <div class="form-group mb-3">
            <label>Nombre</label>
            <input type="text" name="category_name" id="e_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Key</label>
            <input type="text" id="e_key_vis" class="form-control" readonly style="background-color:#e9ecef; border:1px solid #ced4da; color:#6c757d;">
            <input type="hidden" name="category_key_word" id="e_key">
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
    
    if (isBS5) { editModal.show(); } else { $('#editModal').modal('show'); }
  });

  // Auto-slugger for Create
  $('#c_name').on('input', function(){
     var val = $(this).val();
     // Slugify: lowercase, remove special chars, replace spaces with -
     // Keep accents handling simple or matching PHP make_slug
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
