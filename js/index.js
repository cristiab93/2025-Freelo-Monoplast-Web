

$(function () {
  var mySwiper = new Swiper(".mySwiper", {
    autoplay: {
      delay: 3000, // Tiempo de espera entre slides (en milisegundos)
      disableOnInteraction: false, // Para que no se detenga al tocar el slider
    },
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
      '<div class="col-10 offset-1 offset-sm-0 col-sm-6 col-lg-4 col-xl-3 mt-4">' +
      '<div class="card bg-grey justify-content-between rounded-4 border-0 position-relative px-3 py-4 h-100 d-flex flex-column">' +
      '<div class="text-center" style="height: 180px; display: flex; align-items: center; justify-content: center;">' +
      '<img src="' + img + '" class="img-fluid multiply" style="max-height: 100%; object-fit: contain;">' +
      '</div>' +
      '<div class="mt-2">' +
      '<div class="justify-content-md-start">' +
      '<p class="blue mb-1 fw-700 uppercase product-title text-center text-md-start leading-tight">' + name + '</p>' +
      '</div>' +
      '<p class="blue mb-0 font12 text-center text-md-start">' + sub + '</p>' +
      '</div>' +
      '<div class="mt-4 d-md-flex justify-content-center justify-content-md-between gap-2">' +
      '<a href="' + href + '" class="btn btn-outline-dark font11 blue border-blue rounded-5 px-4 py-2 d-flex align-items-center">Ver detalles</a>' +
      '<button type="button" class="btn btn-primary font11 text-white border-blue rounded-5 px-3 py-2 add-to-budget d-flex align-items-center" style="white-space: nowrap;" data-id="' + p.enc_id + '" data-name="' + name + '" data-subname="' + sub + '" data-img="' + img + '">Agregar al presupuesto</button>' +
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

  cargarProductos({ limit: 4, most_searched: 1 });
});

window.addEventListener('scroll', function () {
  const header = document.getElementById('main-header');
  if (!header) return;

  if (window.scrollY > 50) {
    header.classList.add('header-scrolled');
  } else {
    header.classList.remove('header-scrolled');
  }
});

