<?php
$PAGE = 'admin-most-searched';
include('../_general.php');

/* ===== Listado de productos destacados ===== */
$rows = SelectQuery('products')
  ->Condition('product_most_search =', 'i', 1)
  ->Order('product_most_search_order', 'ASC')
  ->SetIndex(-1)
  ->Run();
$featured = is_array($rows) ? $rows : [];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <title>Monoplast - Admin Más Buscados</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Admin styles -->
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/admin.css" type="text/css">

    <style>
        .contenidoAdmin{
            padding: 20px 16px 60px 84px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .inicioAdmin{
            background:#fff;
            padding:20px;
            border-radius:10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .mini-thumb{
            max-width:50px; max-height:50px; object-fit:contain; border-radius:4px;
        }
        
        #searchResults {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            max-height: 300px;
            overflow-y: auto;
            display: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .search-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .search-item:hover {
            background: #f8f9fa;
        }
        .search-item .info {
            flex-grow: 1;
        }
        .search-item .name {
            font-weight: bold;
            display: block;
        }
        .search-item .sub {
            font-size: 0.8rem;
            color: #666;
        }
    </style>
</head>
<body>
<?php ShowAdminNavBar('nav-most-searched'); ?>

<div class="area"></div>

<div class="contenidoAdmin">
  <div class="inicioAdmin">
    <div class="mb-4">
      <h2>Productos más buscados</h2>
      <p class="text-muted">Gestioná los productos que aparecen en la sección "Más buscados" del inicio.</p>
    </div>

    <!-- Buscador para agregar -->
    <div class="mb-5 position-relative">
      <label class="form-label fw-bold">Agregar un producto:</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
        <input type="text" id="productSearch" class="form-control" placeholder="Escribí para buscar productos...">
      </div>
      <div id="searchResults"></div>
    </div>

    <hr>

    <div class="mt-4">
      <h5 class="mb-3">Lista actual (<?= count($featured) ?>)</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th style="width: 70px;">ID</th>
              <th style="width: 80px;">Imagen</th>
              <th>Producto</th>
              <th style="width: 100px;" class="text-center">Orden</th>
              <th style="width: 100px;" class="text-center">Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!count($featured)): ?>
              <tr><td colspan="5" class="text-center py-4 text-muted">No hay productos destacados. Usá el buscador de arriba para agregar.</td></tr>
            <?php else: foreach ($featured as $p): ?>
              <tr>
                <td><?= (int)$p['product_id'] ?></td>
                <td>
                    <img class="mini-thumb" src="<?= view_product_img($p['product_img'] ?? '', '../uploaded_img/') ?>" alt="">
                </td>
                <td>
                  <span class="d-block fw-bold"><?= htmlspecialchars($p['product_name']) ?></span>
                  <span class="text-muted small"><?= htmlspecialchars($p['product_subname']) ?></span>
                </td>
                <td class="text-nowrap text-center" style="width: 100px;">
                  <button class="btn btn-outline-secondary btn-sm btn-move" data-id="<?= (int)$p['product_id'] ?>" data-dir="up" title="Subir">
                    <i class="fas fa-arrow-up"></i>
                  </button>
                  <button class="btn btn-outline-secondary btn-sm btn-move" data-id="<?= (int)$p['product_id'] ?>" data-dir="down" title="Bajar">
                    <i class="fas fa-arrow-down"></i>
                  </button>
                </td>
                <td class="text-center">
                  <button class="btn btn-outline-danger btn-sm btn-remove" data-id="<?= (int)$p['product_id'] ?>" title="Quitar de destacados">
                    <i class="fas fa-times"></i> Quitar
                  </button>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(function(){
  var $search = $('#productSearch');
  var $results = $('#searchResults');
  var timer;

  $search.on('input', function(){
    clearTimeout(timer);
    var q = $(this).val().trim();
    if (q.length < 2) {
      $results.hide();
      return;
    }

    timer = setTimeout(function(){
      $.getJSON('../ajax/load-all-products.php', { q: q, limit: 10 })
        .done(function(resp){
          if (resp.success && resp.data.length) {
            var html = '';
            resp.data.forEach(function(p){
              html += '<div class="search-item" data-id="'+ p.id +'">' +
                        '<img src="../'+ p.image +'" class="mini-thumb">' +
                        '<div class="info">' +
                          '<span class="name">'+ p.name +'</span>' +
                          '<span class="sub">'+ p.subname +'</span>' +
                        '</div>' +
                        '<i class="fas fa-plus text-success"></i>' +
                      '</div>';
            });
            $results.html(html).show();
          } else {
            $results.html('<div class="p-3 text-center text-muted">No se encontraron productos</div>').show();
          }
        });
    }, 300);
  });

  // Cerrar resultados al hacer click afuera
  $(document).on('click', function(e){
    if (!$(e.target).closest('.position-relative').length) {
      $results.hide();
    }
  });

  // Agregar producto (desde resultados)
  $results.on('click', '.search-item', function(){
    var id = $(this).data('id');
    updateStatus(id, 1);
  });

  // Quitar producto
  $('.btn-remove').on('click', function(){
    var id = $(this).data('id');
    if (confirm('¿Quitar este producto de la lista?')) {
      updateStatus(id, 0);
    }
  });

  // Reordenar
  $('.btn-move').on('click', function(){
    var id = $(this).data('id');
    var dir = $(this).data('dir');
    $.post('../ajax/reorder-most-search.php', { id: id, direction: dir })
      .done(function(resp){
        if (resp.success) {
          location.reload();
        } else if (resp.message) {
          console.log(resp.message);
        }
      });
  });

  function updateStatus(id, status) {
    $.post('../ajax/update-most-search.php', { id: id, status: status })
      .done(function(resp){
        if (resp.success) {
          location.reload();
        } else {
          alert(resp.message || 'Error al actualizar');
        }
      })
      .fail(function(){
        alert('Error de conexión');
      });
  }
});
</script>
</body>
</html>
