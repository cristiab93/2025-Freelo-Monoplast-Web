$(function () {
  var mySwiper = new Swiper(".mySwiper", {
    navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" }
  });

  var slideProductosSwiper = new Swiper(".slideProductos", {
    direction: "vertical",
    slidesPerView: 1,
    autoHeight: true,
    spaceBetween: 20,
    mousewheel: true,
    pagination: { el: ".swiper-pagination", clickable: true }
  });

  var serviciosSwiper = new Swiper(".slideServicios", {
    mousewheel: true,
    slidesPerView: 3,
    spaceBetween: 15,
    pagination: { el: ".swiper-pagination", clickable: true },
    breakpoints: { 350: { slidesPerView: 1, spaceBetween: 20 }, 768: { slidesPerView: 2, spaceBetween: 20 }, 992: { slidesPerView: 3, spaceBetween: 15 } }
  });

  function renderCard(p) {
    var img = p.image || "img/placeholder.png";
    var name = p.name || "";
    var sub = p.subname || p.subcategory || "";
    var id = p.id || "";
    var href = "producto.php?id=" + encodeURIComponent(p.enc_id);
    var html = '' +
      '<div class="col-10 offset-1 offset-sm-0 col-lg-4 col-sm-6 mt-3">' +
        '<div class="card border-0 position-relative px-3 py-4 h-100">' +
          '<div class="bg-blue tag py-1 px-3"><p class="white mb-0">Más buscados</p></div>' +
          '<img src="' + img + '" class="img-fluid px-5">' +
          '<div class="row align-items-center mt-2">' +
            '<div class="col-12 col-md">' +
              '<p class="blue mb-1 fw-700 text-uppercase">' + name + '</p>' +
              '<p class="blue mb-0 font13">' + sub + '</p>' +
            '</div>' +
            '<div class="col-12 col-md-auto mt-2 mt-md-0 d-flex gap-2">' +
              '<a href="' + href + '" class="btn btn-outline-dark font13 blue border-blue rounded-5 px-4">Ver detalles</a>' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
    return html;
  }

  function cargarProductos(params) {
    $("#mas-buscados").html('<div class="col-12 text-center py-5 fw-400">Cargando...</div>');
    $.getJSON("ajax/load-all-products.php", params)
      .done(function (resp) {
        if (!resp || !resp.success || !resp.data || !resp.data.length) {
          $("#mas-buscados").html('<div class="col-md-12 col-10 offset-1 text-center py-5 fw-400">No hay productos para mostrar</div>');
          return;
        }
        var html = resp.data.map(renderCard).join("");
        $("#mas-buscados").html(html);
      })
      .fail(function () {
        $("#mas-buscados").html('<div class="col-12 text-center fw-400 py-5">Error al cargar los productos</div>');
      });
  }

  cargarProductos({ limit: 6, order: "recent" });
});
