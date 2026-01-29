$(function () {
  function getParam(name) {
    var url = new URL(window.location.href);
    return url.searchParams.get(name);
  }

  function li(str) {
    return '<li>' + str + '</li>';
  }

  function renderBullets(text) {
    if (!text) return '';
    var parts = text.split(/\r?\n/).map(function (t) { return t.trim(); }).filter(function (t) { return t.length; });
    if (!parts.length) parts = [text];
    return parts.map(li).join('');
  }

  function renderRelacionado(p) {
    var href = 'producto.php?id=' + encodeURIComponent(p.eid || '');
    var img = p.image || 'uploaded_img/ariston.png';
    return '' +
      '<div class="col-12 col-sm-6 col-lg-4 col-xl-3 mt-3">' +
      '<div class="card border-0 position-relative px-3 py-4 h-100 d-flex flex-column">' +
      '<div class="bg-blue tag py-1 px-3"><p class="white fw-500 font11 mb-0 uppercase">Más buscados</p></div>' +
      '<div class="text-center mt-3" style="height: 160px; display: flex; align-items: center; justify-content: center;">' +
      '<img src="' + img + '" class="img-fluid multiply" style="max-height: 100%; object-fit: contain;">' +
      '</div>' +
      '<div class="flex-grow-1 mt-2">' +
      '<div style="height: 52px; display: flex; align-items: center; justify-content: center;" class="justify-content-md-start">' +
      '<p class="blue mb-0 font14 fw-700 text-center text-md-start leading-tight">' + (p.name || '') + '</p>' +
      '</div>' +
      '<p class="blue mb-0 font12 text-center text-md-start">' + (p.subname || '') + '</p>' +
      '</div>' +
      '<div class="mt-3 d-flex justify-content-center justify-content-md-start gap-2">' +
      '<a href="' + href + '" class="btn btn-outline-dark font11 blue border-blue rounded-5 px-4 d-flex align-items-center">Ver detalles</a>' +
      '<button type="button" class="btn btn-primary font11 text-white border-blue rounded-5 px-3 add-to-budget d-flex align-items-center" style="white-space: nowrap;" data-id="' + (p.eid || '') + '" data-name="' + (p.name || '') + '" data-subname="' + (p.subname || '') + '" data-img="' + img + '">Agregar al presupuesto</button>' +
      '</div>' +
      '</div>' +
      '</div>';
  }

  var eid = getParam('id');
  var url = 'ajax/product-detail.php';
  var params = { id: eid, debug: 0 };

  $.getJSON(url, params)
    .done(function (resp) {
      if (!resp || !resp.success || !resp.product) {
        $('#prod-title').text('Producto no encontrado');
        $('#zoom-img').attr('src', 'uploaded_img/ariston.png');
        return;
      }
      var p = resp.product;
      var cat = p.category || '';
      var subcat = p.subcategory || '';
      var catKey = p.category_key || '';
      var subcatKey = p.subcategory_key || '';

      var breadHtml = '<a href="productos.php?cat=' + encodeURIComponent(catKey) + '" class="breadcrumb-link">' + cat + '</a>';
      if (subcat) {
        breadHtml += '<i class="mdi mdi-chevron-right font14"></i>';
        breadHtml += '<a href="productos.php?cat=' + encodeURIComponent(catKey) + '&subc=' + encodeURIComponent(subcat) + '" class="breadcrumb-link">' + subcat + '</a>';
      }
      $('#prod-breadcrumbs').html(breadHtml);
      $('#zoom-img').attr('src', p.image || 'uploaded_img/ariston.png');
      $('#prod-title').text(p.name || '');
      $('#prod-subtitle').text(p.subname || '');
      $('#prod-features').html(renderBullets(p.description || ''));
      if (resp.related && resp.related.length) {
        $('#relacionados').html(resp.related.map(renderRelacionado).join(''));
      } else {
        $('#relacionados').html('<div class="col-12 text-muted mt-3 fw-400">No hay productos relacionados para mostrar.</div>');
      }
    })
    .fail(function () {
      $('#prod-title').text('Error cargando el producto');
      $('#zoom-img').attr('src', 'uploaded_img/ariston.png');
    });

  var zoomContainer = document.querySelector('.zoom-container');
  var zoomImg = document.querySelector('#zoom-img');
  if (zoomContainer && zoomImg) {
    zoomContainer.addEventListener('mousemove', function (e) {
      var rect = zoomContainer.getBoundingClientRect();
      var x = (e.clientX - rect.left) / rect.width * 100;
      var y = (e.clientY - rect.top) / rect.height * 100;
      zoomImg.style.transformOrigin = x + '% ' + y + '%';
      zoomImg.style.transform = 'scale(2)';
    });
    zoomContainer.addEventListener('mouseleave', function () {
      zoomImg.style.transform = 'scale(1)';
      zoomImg.style.transformOrigin = 'center center';
    });
  }
});
