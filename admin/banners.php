<?php
$PAGE = 'admin-banners';
include('../_general.php');

function banner_admin_h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function banner_admin_image_src($value)
{
    $value = trim((string)$value);
    if ($value === '') return '';
    if (preg_match('/^(https?:)?\/\//i', $value) || strpos($value, '/') === 0 || strpos($value, 'data:') === 0) {
        return $value;
    }
    return '../' . $value;
}

function banner_admin_random_filename($ext)
{
    return 'uploaded_banner_' . bin2hex(random_bytes(16)) . '.' . $ext;
}

function banner_admin_upload_image($field, $index, &$error)
{
    $error = '';
    if (!isset($_FILES[$field]['error'][$index]) || $_FILES[$field]['error'][$index] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES[$field]['error'][$index] !== UPLOAD_ERR_OK) {
        $error = 'No se pudo subir una de las imagenes.';
        return null;
    }

    $tmp = $_FILES[$field]['tmp_name'][$index];
    $orig = $_FILES[$field]['name'][$index];
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $allowed = ['png', 'jpg', 'jpeg', 'webp'];
    if (!in_array($ext, $allowed, true)) {
        $error = 'Las imagenes deben ser PNG, JPG, JPEG o WEBP.';
        return null;
    }

    $check = @getimagesize($tmp);
    if (!$check) {
        $error = 'El archivo subido no parece ser una imagen valida.';
        return null;
    }

    $dir = __DIR__ . '/../uploaded_img';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    do {
        $final = banner_admin_random_filename($ext);
        $abs = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $final;
    } while (file_exists($abs));

    if (!move_uploaded_file($tmp, $abs)) {
        $error = 'No se pudo guardar una de las imagenes.';
        return null;
    }

    return 'uploaded_img/' . $final;
}

$message = '';
$message_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_ajax_save = isset($_POST['ajax_save']) && $_POST['ajax_save'] === '1';
    $titles = $_POST['title'] ?? [];
    $images = $_POST['image_url'] ?? [];
    $button_texts = $_POST['button_text'] ?? [];
    $button_urls = $_POST['button_url'] ?? [];

    $posted_banners = [];
    $count = max(count($titles), count($images), count($button_texts), count($button_urls));

    for ($i = 0; $i < $count; $i++) {
        $upload_error = '';
        $uploaded_image = banner_admin_upload_image('image_file', $i, $upload_error);
        if ($upload_error !== '') {
            $message = $upload_error;
            $message_type = 'danger';
            break;
        }

        $final_image = $uploaded_image ?? ($images[$i] ?? '');
        if (trim((string)$final_image) === '') {
            if ($is_ajax_save) {
                continue;
            }

            $message = 'Cada banner debe tener una imagen cargada.';
            $message_type = 'danger';
            break;
        }

        $posted_banners[] = [
            'title' => $titles[$i] ?? '',
            'image_url' => $final_image,
            'button_text' => $button_texts[$i] ?? '',
            'button_url' => $button_urls[$i] ?? '',
        ];
    }

    if ($message === '' && save_home_banners($posted_banners)) {
        $message = 'Banners guardados correctamente.';
        $message_type = 'success';
    } elseif ($message === '') {
        $message = 'No se pudo guardar el archivo data/banners.json.';
        $message_type = 'danger';
    }

    if ($is_ajax_save) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $message_type === 'success',
            'message' => $message,
            'banners' => load_home_banners(false),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

$banners = load_home_banners(false);
if (!$banners) {
    $banners = default_home_banners();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <title>Monoplast - Admin Banners</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">

    <link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
    <script src="../js/jquery-3.5.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/admin.css" type="text/css">

    <style>
        .contenidoAdmin{
            padding: 20px 16px 60px 84px;
            max-width: 1100px;
            margin: 0 auto;
        }
        .inicioAdmin{
            background:#fff;
            padding:20px;
            border-radius:10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .banner-card{
            border:1px solid #e9ecef;
            border-radius:10px;
            padding:18px;
            background:#fff;
        }
        .banner-preview{
            width:100%;
            min-height:160px;
            border-radius:8px;
            background:#f1f3f5;
            object-fit:cover;
            border:1px solid #e9ecef;
        }
        .banner-summary-title{
            white-space:pre-line;
            font-size:20px;
            font-weight:700;
            line-height:1.25;
        }
        .banner-summary{
            display:flex;
        }
        .banner-fields{
            display:none;
        }
        .banner-card.is-editing .banner-summary{
            display:none;
        }
        .banner-card.is-editing .banner-fields{
            display:flex;
        }
        .banner-pending{
            display:none;
        }
        .banner-card.needs-image .banner-pending{
            display:block;
        }
        @media (max-width: 576px){
            .contenidoAdmin{padding-left:72px}
        }
    </style>
</head>
<body>
<?php ShowAdminNavBar('nav-banners'); ?>

<div class="area"></div>

<div class="contenidoAdmin">
  <div class="inicioAdmin">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
      <div>
        <h2>Banners del inicio</h2>
        <p class="text-muted mb-0">Gestiona los slides que aparecen en el home.</p>
      </div>
      <button type="button" class="btn btn-outline-primary align-self-md-start" id="addBanner">
        <i class="fas fa-plus me-1"></i> Agregar banner
      </button>
    </div>
    <div id="autosaveStatus" class="text-muted small mb-3"></div>

    <?php if ($message !== ''): ?>
      <div class="alert alert-<?= banner_admin_h($message_type) ?>"><?= banner_admin_h($message) ?></div>
    <?php endif; ?>

    <form method="post" id="bannersForm" enctype="multipart/form-data">
      <div id="bannersList" class="d-flex flex-column gap-3">
        <?php foreach ($banners as $index => $banner): ?>
          <div class="banner-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Banner <?= (int)$index + 1 ?></h5>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm move-banner" data-dir="up" title="Subir">
                  <i class="fas fa-arrow-up"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm move-banner" data-dir="down" title="Bajar">
                  <i class="fas fa-arrow-down"></i>
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm edit-banner">
                  <i class="fas fa-pen"></i> Editar
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm remove-banner">
                  <i class="fas fa-trash"></i> Eliminar
                </button>
              </div>
            </div>
            <div class="banner-pending alert alert-warning py-2 mb-3">Carga una imagen para guardar este banner.</div>
            <div class="row g-3 banner-summary">
              <div class="col-12 col-lg-4">
                <img class="banner-preview" src="<?= banner_admin_h(banner_admin_image_src($banner['image_url'] ?? '')) ?>" alt="">
              </div>
              <div class="col-12 col-lg-8">
                <div class="banner-summary-title mb-3"><?= banner_admin_h($banner['title'] ?? '') ?></div>
                <p class="mb-2"><strong>Imagen:</strong> <span class="summary-image-url"><?= banner_admin_h($banner['image_url'] ?? '') ?></span></p>
                <p class="mb-2"><strong>Boton:</strong> <span class="summary-button-text"><?= banner_admin_h($banner['button_text'] ?? '') ?></span></p>
                <p class="mb-0"><strong>Link:</strong> <span class="summary-button-url"><?= banner_admin_h($banner['button_url'] ?? '') ?></span></p>
              </div>
            </div>
            <div class="row g-3 banner-fields">
              <div class="col-12 col-lg-4">
                <img class="banner-preview" src="<?= banner_admin_h(banner_admin_image_src($banner['image_url'] ?? '')) ?>" alt="">
              </div>
              <div class="col-12 col-lg-8">
                <div class="mb-3">
                  <label class="form-label fw-bold">Titulo</label>
                  <textarea name="title[]" class="form-control" rows="3" placeholder="Titulo del banner"><?= banner_admin_h($banner['title'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-bold">Imagen del banner</label>
                  <input type="hidden" name="image_url[]" class="banner-image-input" value="<?= banner_admin_h($banner['image_url'] ?? '') ?>">
                  <input type="file" name="image_file[]" class="form-control banner-file-input" accept="image/png,image/jpeg,image/webp">
                  <small class="text-muted banner-image-status">
                    <?php if (trim((string)($banner['image_url'] ?? '')) !== ''): ?>
                      Imagen cargada: <?= banner_admin_h(basename((string)$banner['image_url'])) ?>. Si no elegis una nueva, se mantiene la actual.
                    <?php else: ?>
                      Todavia no hay imagen cargada.
                    <?php endif; ?>
                  </small>
                </div>
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <label class="form-label fw-bold">Nombre del boton</label>
                    <input type="text" name="button_text[]" class="form-control" value="<?= banner_admin_h($banner['button_text'] ?? '') ?>" placeholder="Ver productos">
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label fw-bold">Link del boton</label>
                    <input type="text" name="button_url[]" class="form-control" value="<?= banner_admin_h($banner['button_url'] ?? '') ?>" placeholder="productos.php">
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </form>
  </div>
</div>

<template id="bannerTemplate">
  <div class="banner-card is-editing">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Nuevo banner</h5>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm move-banner" data-dir="up" title="Subir">
          <i class="fas fa-arrow-up"></i>
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm move-banner" data-dir="down" title="Bajar">
          <i class="fas fa-arrow-down"></i>
        </button>
        <button type="button" class="btn btn-primary btn-sm save-banner">
          <i class="fas fa-save"></i> Guardar
        </button>
      </div>
    </div>
    <div class="banner-pending alert alert-warning py-2 mb-3">Carga una imagen para guardar este banner.</div>
    <div class="row g-3 banner-summary">
      <div class="col-12 col-lg-4">
        <img class="banner-preview" src="" alt="">
      </div>
      <div class="col-12 col-lg-8">
        <div class="banner-summary-title mb-3"></div>
        <p class="mb-2"><strong>Imagen:</strong> <span class="summary-image-url"></span></p>
        <p class="mb-2"><strong>Boton:</strong> <span class="summary-button-text"></span></p>
        <p class="mb-0"><strong>Link:</strong> <span class="summary-button-url"></span></p>
      </div>
    </div>
    <div class="row g-3 banner-fields">
      <div class="col-12 col-lg-4">
        <img class="banner-preview" src="" alt="">
      </div>
      <div class="col-12 col-lg-8">
        <div class="mb-3">
          <label class="form-label fw-bold">Titulo</label>
          <textarea name="title[]" class="form-control" rows="3" placeholder="Titulo del banner"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Imagen del banner</label>
          <input type="hidden" name="image_url[]" class="banner-image-input">
          <input type="file" name="image_file[]" class="form-control banner-file-input" accept="image/png,image/jpeg,image/webp">
          <small class="text-muted banner-image-status">Todavia no hay imagen cargada.</small>
        </div>
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label fw-bold">Nombre del boton</label>
            <input type="text" name="button_text[]" class="form-control" placeholder="Ver productos">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label fw-bold">Link del boton</label>
            <input type="text" name="button_url[]" class="form-control" placeholder="productos.php">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
$(function(){
  var $list = $('#bannersList');
  var autosaveTimer = null;
  var autosaveRequest = null;
  var $autosaveStatus = $('#autosaveStatus');

  $('#addBanner').on('click', function(){
    var html = $('#bannerTemplate').html();
    $list.append(html);
    renumberBanners();
    var $newCard = $list.find('.banner-card').last();
    markImageState($newCard);
    $newCard.find('textarea[name="title[]"]').trigger('focus');
  });

  $list.on('click', '.edit-banner', function(){
    var $card = $(this).closest('.banner-card');
    $card.addClass('is-editing');
    $(this)
      .removeClass('btn-outline-primary edit-banner')
      .addClass('btn-primary save-banner')
      .html('<i class="fas fa-save"></i> Guardar');
  });

  $list.on('click', '.save-banner', function(){
    var $card = $(this).closest('.banner-card');
    if (!bannerHasImage($card)) {
      $card.addClass('border-danger needs-image');
      alert('Cada banner debe tener una imagen cargada antes de guardar.');
      return;
    }

    updateSummary($card);
    autosaveNow();
    $card.removeClass('is-editing border-danger');
    $(this)
      .removeClass('btn-primary save-banner')
      .addClass('btn-outline-primary edit-banner')
      .html('<i class="fas fa-pen"></i> Editar');
  });

  $list.on('click', '.remove-banner', function(){
    if ($list.find('.banner-card').length <= 1) {
      alert('Tiene que quedar al menos un banner.');
      return;
    }
    if (!confirm('¿Estas seguro de eliminar este banner?')) {
      return;
    }
    $(this).closest('.banner-card').remove();
    renumberBanners();
    autosaveNow();
  });

  $list.on('click', '.move-banner', function(){
    var $card = $(this).closest('.banner-card');
    var dir = $(this).data('dir');

    if (dir === 'up') {
      var $prev = $card.prev('.banner-card');
      if ($prev.length) {
        $card.insertBefore($prev);
      }
    } else {
      var $next = $card.next('.banner-card');
      if ($next.length) {
        $card.insertAfter($next);
      }
    }

    renumberBanners();
    updateMoveButtons();
    autosaveNow();
  });

  $list.on('change', '.banner-file-input', function(){
    var file = this.files && this.files[0] ? this.files[0] : null;
    var $card = $(this).closest('.banner-card');
    if (!file) {
      updateSummary($card);
      markImageState($card);
      return;
    }

    var previewUrl = URL.createObjectURL(file);
    $card.find('.banner-preview').attr('src', previewUrl);
    $card.find('.summary-image-url').text('Nueva imagen seleccionada: ' + file.name);
    $card.find('.banner-image-status').text('Nueva imagen seleccionada: ' + file.name + '. Se guardara al presionar Guardar.');
    markImageState($card);
    autosaveNow();
  });

  $list.on('input', 'textarea[name="title[]"], input[name="button_text[]"], input[name="button_url[]"]', function(){
    var $card = $(this).closest('.banner-card');
    updateSummary($card);
    markImageState($card);
    scheduleAutosave();
  });

  $('#bannersForm').on('submit', function(e){
    e.preventDefault();
    var valid = true;
    $list.find('.banner-card').each(function(){
      var $card = $(this);
      var hasSavedImage = $.trim($card.find('input[name="image_url[]"]').val()) !== '';
      var fileInput = $card.find('input[name="image_file[]"]')[0];
      var hasNewImage = fileInput && fileInput.files && fileInput.files.length > 0;

      $card.removeClass('border-danger');
      if (!hasSavedImage && !hasNewImage) {
        valid = false;
        $card.addClass('border-danger');
      }
    });

    if (!valid) {
      alert('Cada banner debe tener una imagen cargada antes de guardar.');
      return;
    }

    autosaveNow();
  });

  function renumberBanners() {
    $list.find('.banner-card h5').each(function(index){
      $(this).text('Banner ' + (index + 1));
    });
    updateMoveButtons();
  }

  function updateSummary($card) {
    $card.find('.banner-summary-title').text($card.find('textarea[name="title[]"]').val());
    var imagePath = $card.find('input[name="image_url[]"]').val();
    var fileInput = $card.find('input[name="image_file[]"]')[0];
    var fileName = fileInput && fileInput.files && fileInput.files[0] ? 'Nueva imagen seleccionada: ' + fileInput.files[0].name : imagePath;
    $card.find('.summary-image-url').text(fileName);
    $card.find('.summary-button-text').text($card.find('input[name="button_text[]"]').val());
    $card.find('.summary-button-url').text($card.find('input[name="button_url[]"]').val());
  }

  function scheduleAutosave() {
    clearTimeout(autosaveTimer);
    autosaveTimer = setTimeout(autosaveNow, 650);
  }

  function autosaveNow() {
    clearTimeout(autosaveTimer);

    markAllImageStates();

    if (autosaveRequest && autosaveRequest.readyState !== 4) {
      autosaveRequest.abort();
    }

    var formData = new FormData($('#bannersForm')[0]);
    formData.append('ajax_save', '1');
    $autosaveStatus.text('Guardando cambios...');

    autosaveRequest = $.ajax({
      url: 'banners.php',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json'
    })
    .done(function(resp){
      if (!resp || !resp.success) {
        $autosaveStatus.text((resp && resp.message) ? resp.message : 'No se pudo guardar automaticamente.');
        return;
      }

      syncSavedImages(resp.banners || []);
      $autosaveStatus.text(hasPendingImages() ? 'Hay banners pendientes: carga una imagen para guardarlos.' : 'Cambios guardados automaticamente.');
    })
    .fail(function(xhr, status){
      if (status !== 'abort') {
        $autosaveStatus.text('No se pudo guardar automaticamente.');
      }
    });
  }

  function hasPendingImages() {
    var pending = false;
    $list.find('.banner-card').each(function(){
      if ($(this).hasClass('needs-image')) {
        pending = true;
      }
    });
    return pending;
  }

  function syncSavedImages(banners) {
    $list.find('.banner-card').each(function(index){
      if (!banners[index] || !banners[index].image_url) return;

      var $card = $(this);
      var imageUrl = banners[index].image_url;
      $card.find('input[name="image_url[]"]').val(imageUrl);
      $card.find('input[name="image_file[]"]').val('');
      $card.find('.summary-image-url').text(imageUrl);
      $card.find('.banner-image-status').text('Imagen cargada: ' + imageUrl.split('/').pop() + '. Si no elegis una nueva, se mantiene la actual.');

      var isAbsolute = /^(https?:)?\/\//i.test(imageUrl) || imageUrl.indexOf('/') === 0 || imageUrl.indexOf('data:') === 0;
      var src = isAbsolute ? imageUrl : '../' + imageUrl;
      $card.find('.banner-preview').attr('src', src);
      markImageState($card);
    });
  }

  function markAllImageStates() {
    $list.find('.banner-card').each(function(){
      markImageState($(this));
    });
  }

  function markImageState($card) {
    $card.toggleClass('needs-image', !bannerHasImage($card));
  }

  function bannerHasImage($card) {
    var hasSavedImage = $.trim($card.find('input[name="image_url[]"]').val()) !== '';
    var fileInput = $card.find('input[name="image_file[]"]')[0];
    var hasNewImage = fileInput && fileInput.files && fileInput.files.length > 0;
    return hasSavedImage || hasNewImage;
  }

  function updateMoveButtons() {
    var $cards = $list.find('.banner-card');
    $cards.find('.move-banner').prop('disabled', false);
    $cards.first().find('.move-banner[data-dir="up"]').prop('disabled', true);
    $cards.last().find('.move-banner[data-dir="down"]').prop('disabled', true);
  }

  markAllImageStates();
  updateMoveButtons();
});
</script>
</body>
</html>
