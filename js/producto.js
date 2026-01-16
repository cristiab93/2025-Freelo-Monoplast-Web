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
      '<div class="col-12 col-sm-6 col-lg-6 col-xl-3 mt-3">' +
        '<div class="card bg-light border-0 position-relative rounded-3 px-3 py-4 h-100">' +
          '<div class="bg-blue tag py-1 px-3"><p class="white fw-500 font11 mb-0">Más buscados</p></div>' +
          '<img src="' + img + '" class="img-fluid multiply px-sm-0 px-5 px-md-5 mt-3">' +
          '<p class="blue mb-0 text-uppercase font14 mt-3 fw-700">' + (p.name || '') + '</p>' +
          '<p class="blue mb-0 font12 mt-1">' + (p.subname || '') + '</p>' +
          '<div class="d-flex align-items-center justify-content-between mt-3">' +
            '<a href="' + href + '" class="btn btn-outline-dark font11 blue border-blue rounded-5 px-xxl-4 px-xl-2 px-md-3 px-sm-2 px-4 mt-3">Ver detalles</a>' +
            '<button type="button" class="btn btn-primary font11 blue border-blue rounded-5 px-xxl-4 px-xl-3 px-md-3 px-sm-2 px-4 mt-3">Agregar al presupuesto</button>' +
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
      $('#prod-category').text(p.category || '');
      $('#zoom-img').attr('src', p.image || 'uploaded_img/ariston.png');
      $('#prod-title').html((p.name || '') + (p.subname ? '<br>' + p.subname : ''));
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
