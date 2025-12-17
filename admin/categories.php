<?php
$PAGE = 'admin-categories';
include('../_general.php');

/* ------------ helpers ------------ */
function category_exists($name, $exclude_id = 0){
    $name = trim($name);
    if ($name === '') return false;
    $q = SelectQuery('categories')->Condition('LOWER(category_name) =', 's', mb_strtolower($name, 'UTF-8'));
    if ($exclude_id > 0) $q->Condition('category_id <>', 'i', (int)$exclude_id);
    $r = $q->Limit(1)->Run();
    return !empty($r);
}

/* ------------ acciones CRUD ------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = trim($_POST['category_name'] ?? '');
        if ($name === '') { header('Location: categories.php?m=invalid'); exit; }
        if (category_exists($name)) { header('Location: categories.php?m=dup'); exit; }

        InsertQuery('categories')
            ->Value('category_name', 's', $name)
            ->Run();

        header('Location: categories.php?m=created'); exit;
    }

    if ($action === 'update') {
        $id   = (int)($_POST['category_id'] ?? 0);
        $name = trim($_POST['category_name'] ?? '');
        if ($id <= 0 || $name === '') { header('Location: categories.php?m=invalid'); exit; }
        if (category_exists($name, $id)) { header('Location: categories.php?m=dup'); exit; }

        UpdateQuery('categories')
            ->Value('category_name', 's', $name)
            ->Condition('category_id =', 'i', $id)
            ->Run();

        header('Location: categories.php?m=updated'); exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['category_id'] ?? 0);
        if ($id > 0) {
            DeleteQuery('categories')->Condition('category_id =','i',$id)->Run();
        }
        header('Location: categories.php?m=deleted'); exit;
    }
}

/* ------------ fetch ------------ */
$rows = SelectQuery('categories')->Order('category_name','ASC')->Limit(1000000)->Run();
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

    <!-- Font Awesome 5 + v4-shims (sin integrity) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/v4-shims.min.css">

    <!-- Admin styles existentes -->
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/admin.css" type="text/css">

    <style>
        /* Margen para no chocar con el sidebar fijo */
        .contenidoAdmin{
            padding: 20px 16px 60px 84px; /* 84px a izquierda = ancho del menú + aire */
            max-width: 1000px;            /* más angosto que productos, por simplicidad */
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
<?php ShowAdminNavBar('nav-categories'); /* usamos el mismo nav simple (Inicio / Productos) */ ?>

<div class="area"></div>

<div class="contenidoAdmin">
  <div class="inicioAdmin jumbotron">
    <div class="d-flex justify-content-between align-items-center">
      <h2>Categorías</h2>
      <button class="btn btn-primary" type="button" id="btnCreate">
        <i class="fa fa-plus"></i> Nueva categoría
      </button>
    </div>

    <?php if (isset($_GET['m']) && $_GET['m'] === 'created'): ?>
      <div class="alert alert-success mt-3">Categoría creada</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'updated'): ?>
      <div class="alert alert-info mt-3">Categoría actualizada</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'deleted'): ?>
      <div class="alert alert-warning mt-3">Categoría eliminada</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'invalid'): ?>
      <div class="alert alert-danger mt-3">Datos incompletos</div>
    <?php elseif (isset($_GET['m']) && $_GET['m'] === 'dup'): ?>
      <div class="alert alert-danger mt-3">Ya existe una categoría con ese nombre</div>
    <?php endif; ?>

    <div class="table-wrap mt-3">
      <table class="table table-bordered table-hover mb-0">
        <thead>
          <tr>
            <th style="width:100px">ID</th>
            <th>Nombre</th>
            <th style="width:230px">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!count($categories)): ?>
          <tr><td colspan="3">No hay categorías cargadas</td></tr>
        <?php else: foreach ($categories as $c): ?>
          <tr>
            <td><?= (int)$c['category_id'] ?></td>
            <td><?= htmlspecialchars($c['category_name'] ?? '') ?></td>
            <td>
              <button
                class="btn btn-sm btn-secondary btn-edit"
                data-id="<?= (int)$c['category_id'] ?>"
                data-name="<?= htmlspecialchars($c['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
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
      <style>
        #createModal .modal-header{padding:18px 22px}
        #createModal .modal-body{padding:16px 22px}
        #createModal .modal-footer{padding:12px 22px}
      </style>
      <div class="modal-header">
        <h5 class="modal-title">Nueva categoría</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="create">
        <div class="form-group">
          <label>Nombre</label>
          <input type="text" name="category_name" id="c_name" class="form-control" placeholder="Nombre de la categoría" required>
          <small class="text-muted">Debe ser único.</small>
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
  <div class="modal-dialog modal-dialog-centered" style="max-width: min(700px, 95vw);">
    <form method="post" action="categories.php" class="modal-content" id="editForm">
      <style>
        #editModal .modal-header{padding:18px 22px}
        #editModal .modal-body{padding:16px 22px}
        #editModal .modal-footer{padding:12px 22px}
      </style>
      <div class="modal-header">
        <h5 class="modal-title">Editar categoría</h5>
        <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="category_id" id="e_id">
        <div class="form-group">
          <label>Nombre</label>
          <input type="text" name="category_name" id="e_name" class="form-control" required>
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
    if (isBS5) { editModal.show(); } else { $('#editModal').modal('show'); }
  });
  $('.cancel-edit, #editModal .btn-close').on('click', function(){
    if (isBS5) { editModal.hide(); } else { $('#editModal').modal('hide'); }
  });
});
</script>
</body>
</html>
