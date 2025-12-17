<?php
$PAGE = 'admin-subcategories';
include('../_general.php');

/* ====================== helpers ====================== */
function fetch_categories(){
    $rows = SelectQuery('categories')->Order('category_name','ASC')->Limit(1000000)->Run();
    return is_array($rows) ? array_values($rows) : [];
}
function cat_exists_by_id($id){
    if ($id <= 0) return false;
    $r = SelectQuery('categories')->Condition('category_id =','i',(int)$id)->Limit(1)->Run();
    return !empty($r);
}
/* nombre duplicado dentro del mismo father, case-insensitive */
function subcat_exists($name, $father_id, $exclude_id = 0){
    $name = trim($name);
    $father_id = (int)$father_id;
    if ($name === '' || $father_id <= 0) return false;
    $q = SelectQuery('sub_categories')
        ->Condition('sub_category_father =','i',$father_id)
        ->Condition('LOWER(sub_category_name) =','s', mb_strtolower($name,'UTF-8'));
    if ($exclude_id > 0) $q->Condition('sub_category_id <>','i',(int)$exclude_id);
    $r = $q->Limit(1)->Run();
    return !empty($r);
}

/* ====================== acciones CRUD ====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = trim($_POST['sub_category_name'] ?? '');
        $father = (int)($_POST['sub_category_father'] ?? 0);

        if ($name === '' || $father <= 0) { header('Location: subcategories.php?m=invalid'); exit; }
        if (!cat_exists_by_id($father))   { header('Location: subcategories.php?m=nofather'); exit; }
        if (subcat_exists($name, $father)){ header('Location: subcategories.php?m=dup'); exit; }

        InsertQuery('sub_categories')
            ->Value('sub_category_name','s',$name)
            ->Value('sub_category_father','i',$father)
            ->Run();

        header('Location: subcategories.php?m=created'); exit;
    }

    if ($action === 'update') {
        $id     = (int)($_POST['sub_category_id'] ?? 0);
        $name   = trim($_POST['sub_category_name'] ?? '');
        $father = (int)($_POST['sub_category_father'] ?? 0);

        if ($id <= 0 || $name === '' || $father <= 0) { header('Location: subcategories.php?m=invalid'); exit; }
        if (!cat_exists_by_id($father))               { header('Location: subcategories.php?m=nofather'); exit; }
        if (subcat_exists($name, $father, $id))       { header('Location: subcategories.php?m=dup'); exit; }

        UpdateQuery('sub_categories')
            ->Value('sub_category_name','s',$name)
            ->Value('sub_category_father','i',$father)
            ->Condition('sub_category_id =','i',$id)
            ->Run();

        header('Location: subcategories.php?m=updated'); exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['sub_category_id'] ?? 0);
        if ($id > 0) {
            DeleteQuery('sub_categories')->Condition('sub_category_id =','i',$id)->Run();
        }
        header('Location: subcategories.php?m=deleted'); exit;
    }
}

/* ====================== fetch ====================== */
$sub_rows = SelectQuery('sub_categories')
    ->Order('sub_category_father','ASC')
    ->Order('sub_category_name','ASC')
    ->Limit(1000000)->Run();
$subcats = is_array($sub_rows) ? array_values($sub_rows) : [];

$cats = fetch_categories();
$cat_map = [];
foreach ($cats as $c) $cat_map[(int)$c['category_id']] = $c['category_name'];
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

    <?php if (isset($_GET['m']) && $_GET['m'] === 'created'): ?>
      <div class="alert alert-success mt-3">Subcategoría creada</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'updated'): ?>
      <div class="alert alert-info mt-3">Subcategoría actualizada</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'deleted'): ?>
      <div class="alert alert-warning mt-3">Subcategoría eliminada</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'invalid'): ?>
      <div class="alert alert-danger mt-3">Datos incompletos</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'dup'): ?>
      <div class="alert alert-danger mt-3">Ya existe una subcategoría con ese nombre en esa categoría</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'nofather'): ?>
      <div class="alert alert-danger mt-3">La categoría padre no existe</div>
    <?php endif; ?>

    <div class="table-wrap mt-3">
      <table class="table table-bordered table-hover mb-0">
        <thead>
          <tr>
            <th style="width:110px">ID</th>
            <th>Subcategoría</th>
            <th style="width:320px">Categoría padre</th>
            <th style="width:230px">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!count($subcats)): ?>
          <tr><td colspan="4">No hay subcategorías cargadas</td></tr>
        <?php else: foreach ($subcats as $s): 
            $fid = (int)($s['sub_category_father'] ?? 0);
            $father_name = $cat_map[$fid] ?? ('ID '.$fid);
        ?>
          <tr>
            <td><?= (int)$s['sub_category_id'] ?></td>
            <td><?= htmlspecialchars($s['sub_category_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($father_name) ?></td>
            <td>
              <button
                class="btn btn-sm btn-secondary btn-edit"
                data-id="<?= (int)$s['sub_category_id'] ?>"
                data-name="<?= htmlspecialchars($s['sub_category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                data-father="<?= (int)$s['sub_category_father'] ?>"
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
      <style>
        #createModal .modal-header{padding:18px 22px}
        #createModal .modal-body{padding:16px 22px}
        #createModal .modal-footer{padding:12px 22px}
        #createModal .form-grid{display:grid; grid-template-columns: 1fr 1fr; gap:14px;}
        @media (max-width: 768px){
          #createModal .form-grid{grid-template-columns: 1fr;}
        }
      </style>
      <div class="modal-header">
        <h5 class="modal-title">Nueva subcategoría</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="create">
        <div class="form-grid">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="sub_category_name" id="c_name" class="form-control" placeholder="Nombre de la subcategoría" required>
          </div>
          <div class="form-group">
            <label>Categoría padre</label>
            <select name="sub_category_father" id="c_father" class="form-control" required>
                <option value="" disabled selected>Seleccioná una categoría</option>
                <?php foreach ($cats as $c): ?>
                  <option value="<?= (int)$c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                <?php endforeach; ?>
            </select>
          </div>
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
      <style>
        #editModal .modal-header{padding:18px 22px}
        #editModal .modal-body{padding:16px 22px}
        #editModal .modal-footer{padding:12px 22px}
        #editModal .form-grid{display:grid; grid-template-columns: 1fr 1fr; gap:14px;}
        @media (max-width: 768px){
          #editModal .form-grid{grid-template-columns: 1fr;}
        }
      </style>
      <div class="modal-header">
        <h5 class="modal-title">Editar subcategoría</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="sub_category_id" id="e_id">
        <div class="form-grid">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="sub_category_name" id="e_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Categoría padre</label>
            <select name="sub_category_father" id="e_father" class="form-control" required>
                <option value="" disabled>Seleccioná una categoría</option>
                <?php foreach ($cats as $c): ?>
                  <option value="<?= (int)$c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                <?php endforeach; ?>
            </select>
          </div>
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
  $('.cancel-create, #createModal .btn-close').on('click', function(){
    if (isBS5) { createModal.hide(); } else { $('#createModal').modal('hide'); }
  });

  $('.btn-edit').on('click', function(){
    var $b = $(this);
    $('#e_id').val($b.data('id'));
    $('#e_name').val($b.data('name'));
    $('#e_father').val(String($b.data('father')));
    if (isBS5) { editModal.show(); } else { $('#editModal').modal('show'); }
  });
  $('.cancel-edit, #editModal .btn-close').on('click', function(){
    if (isBS5) { editModal.hide(); } else { $('#editModal').modal('hide'); }
  });
});
</script>
</body>
</html>
