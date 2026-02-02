$(function () {
  var v = new Date().getTime();
  function getParam(name) {
    var url = new URL(window.location.href);
    return url.searchParams.get(name);
  }

  function li(str) {
    return '<li>' + str + '</li>';
  }

  function cleanText(text) {
    if (!text) return '';
    return text.replace(/&nbsp;/g, ' ').replace(/&amp;nbsp;/g, ' ').replace(/\u00A0/g, ' ');
  }

  function renderBullets(text) {
    if (!text) return '';
    var cleaned = cleanText(text);

    // Split by newlines or " - " (dash with spaces)
    var parts = cleaned.split(/\r?\n| - /).map(function (t) {
      return t.trim().replace(/^-+/, '').trim();
    });

    if (!parts.length) return li(cleaned);

    return parts.map(function (t) {
      if (t === '') return '<li style="min-height: 1.2em; list-style: none;">&nbsp;</li>';
      return li(t);
    }).join('');
  }

  function renderRelacionado(p) {
    var href = 'producto.php?id=' + encodeURIComponent(p.eid || '');
    var img = (p.image || 'uploaded_img/ariston.png') + "?v=" + v;
    return '' +
      '<div class="col-12 col-sm-6 col-lg-4 col-xl-3 mt-4">' +
      '<div class="card bg-grey justify-content-between rounded-4 border-0 position-relative px-3 py-4 h-100 d-flex flex-column">' +
      '<div class="text-center" style="height: 180px; display: flex; align-items: center; justify-content: center;">' +
      '<img src="' + img + '" class="img-fluid multiply" style="max-height: 100%; object-fit: contain;">' +
      '</div>' +
      '<div class="mt-2">' +
      '<div class="justify-content-md-start">' +
      '<p class="blue mb-1 font14 fw-700 uppercase text-center text-md-start leading-tight">' + (p.name || '') + '</p>' +
      '</div>' +
      '<p class="blue mb-0 font12 text-center text-md-start">' + (p.subname || '') + '</p>' +
      '</div>' +
      '<div class="mt-4 d-flex justify-content-between gap-2">' +
      '<a href="' + href + '" class="btn btn-outline-dark font11 blue border-blue rounded-5 px-2 py-2 d-flex align-items-center justify-content-center w-50">Ver detalles</a>' +
      '<button type="button" class="btn btn-primary font11 text-white border-blue rounded-5 px-1 py-2 add-to-budget d-flex align-items-center justify-content-center w-50" data-id="' + (p.eid || '') + '" data-name="' + (p.name || '') + '" data-subname="' + (p.subname || '') + '" data-img="' + img + '">Agregar presupuesto</button>' +
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
        $('#zoom-img').attr('src', 'uploaded_img/ariston.png?v=' + v);
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
      $('#zoom-img').attr('src', (p.image || 'uploaded_img/ariston.png') + "?v=" + v);
      $('#prod-title').text(p.name || '');
      $('#prod-subtitle').text(cleanText(p.subname || ''));
      $('#prod-features').html(renderBullets(p.description || ''));
      if (resp.related && resp.related.length) {
        $('#relacionados').html(resp.related.map(renderRelacionado).join(''));
      } else {
        $('#relacionados').html('<div class="col-12 text-muted mt-3 fw-400">No hay productos relacionados para mostrar.</div>');
      }
    })
    .fail(function () {
      $('#prod-title').text('Error cargando el producto');
      $('#zoom-img').attr('src', 'uploaded_img/ariston.png?v=' + v);
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
