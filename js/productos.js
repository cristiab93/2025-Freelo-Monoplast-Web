document.addEventListener("DOMContentLoaded", function () {
  var swiperInstance;
  var swEl = document.querySelector(".slideProdInterna");
  if (swEl) {
    swiperInstance = new Swiper(".slideProdInterna", {
      loop: true,
      slidesPerView: 3,
      spaceBetween: 15,
      navigation: { nextEl: ".custom-next", prevEl: ".custom-prev" },
      breakpoints: { 300: { slidesPerView: 1, spaceBetween: 20 }, 768: { slidesPerView: 2, spaceBetween: 20 }, 1024: { slidesPerView: 3, spaceBetween: 15 } }
    });
  }

  function norm(s) { return (s || "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, " ").trim() }
  function initialCat() { var p = new URLSearchParams(window.location.search); var c = p.get("cat"); if (!c) return ""; c = norm(c); var v = ["calefaccion", "piletas", "artefactos", "construccion", "infraestructura", "riego"]; return v.includes(c) ? c : "" }

  var grid = document.getElementById("grid-productos");
  var pillsUl = document.getElementById("pills-tab");
  var titleEl = document.getElementById("titulo-categoria");
  var pagNav = document.getElementById("nav-paginacion");
  var pagUl = document.getElementById("pagination");

  var ALL = [], MAP = {}, REV = {}, SUBS_BY_CAT = {};
  var currentCategory = initialCat() || "calefaccion";
  var currentSubcat = "";
  var currentPage = 1;
  var perPage = 12;
  var totalPages = 1;
  var allSubcats = [];

  function rebuildReverse() { REV = {}; Object.keys(MAP || {}).forEach(function (k) { var v = MAP[k] || ""; REV[norm(v)] = k }) }
  function keyFromSliderText(text) { var t = norm(text); if (REV[t]) return REV[t]; var best = ""; Object.keys(REV).forEach(function (d) { if (!best && t.indexOf(d) >= 0) best = REV[d] }); if (best) return best; if (t.includes("artefactos") || t.includes("mamparas")) return "artefactos"; if (t.includes("construccion")) return "construccion"; if (t.includes("infraestructura")) return "infraestructura"; if (t.includes("piletas")) return "piletas"; if (t.includes("calefaccion")) return "calefaccion"; if (t.includes("riego")) return "riego"; return "calefaccion" }

  function setTitle() { if (!titleEl) return; var d = (MAP && MAP[currentCategory]) ? MAP[currentCategory] : "Productos"; titleEl.textContent = d }

  function filterProducts() {
    var list = ALL.filter(function (p) { return p.category === currentCategory });
    if (currentSubcat) list = list.filter(function (p) { return (p.subcategory || "") === currentSubcat });
    return list
  }

  function renderCard(p) {
    var href = "producto.php?id=" + encodeURIComponent(p.eid || "");
    var img = p.image || "uploaded_img/ariston.png";
    var name = p.name || "";
    var subn = p.subname || "";
    return '' +
      '<div class="col-12 col-sm-6 col-lg-4 col-xl-3 mt-3">' +
      '<div class="card border-0 position-relative px-3 py-4 h-100">' +
      '<div class="bg-blue tag py-1 px-3"><p class="white mb-0">Más buscados</p></div>' +
      '<img src="' + img + '" class="img-fluid px-5">' +
      '<div class="row align-items-center mt-2">' +
      '<div class="col-12 text-center text-md-start">' +
      '<p class="blue mb-1 fw-700 text-uppercase mt-2 product-title">' + name + '</p>' +
      '<p class="blue mb-0 font13">' + subn + '</p>' +
      '</div>' +
      '<div class="col-12 mt-3 d-flex justify-content-center justify-content-md-start">' +
      '<a href="' + href + '" class="btn btn-outline-dark font13 blue border-blue rounded-5 px-4">Ver detalles</a>' +
      '</div>' +
      '</div>' +
      '</div>' +
      '</div>'
  }

  function renderGrid(list) {
    if (!grid) return;
    if (!list.length) { grid.innerHTML = '<div class="col-12 text-center text-muted my-5 fw-400">No hay productos para mostrar.</div>'; return }
    var start = (currentPage - 1) * perPage, end = start + perPage;
    var pageItems = list.slice(start, end);
    grid.innerHTML = pageItems.map(renderCard).join("")
  }

  function buildSubcatPills() {
    if (!pillsUl) return;
    var html = ''; var base = 'nav-link rounded-pill px-4 py-2';
    html += '<li class="nav-item" role="presentation"><button class="' + base + (currentSubcat === '' ? ' active' : '') + '" type="button" data-subcat="" aria-selected="' + (currentSubcat === '' ? 'true' : 'false') + '">Todos los productos</button></li>';
    allSubcats.forEach(function (s) {
      var act = (s === currentSubcat);
      html += '<li class="nav-item" role="presentation"><button class="' + base + (act ? ' active' : '') + '" type="button" data-subcat="' + s.replace(/"/g, "&quot;") + '" aria-selected="' + (act ? 'true' : 'false') + '">' + s + '</button></li>'
    });
    pillsUl.innerHTML = html
  }

  function markActivePill() {
    if (!pillsUl) return;
    pillsUl.querySelectorAll('.nav-link').forEach(function (b) {
      var sc = b.getAttribute('data-subcat') || '';
      var is = sc === currentSubcat;
      b.classList.toggle('active', is);
      b.setAttribute('aria-selected', is ? 'true' : 'false')
    })
  }

  function buildPagination(totalItems) {
    if (!pagNav || !pagUl) return;
    totalPages = Math.max(1, Math.ceil(totalItems / perPage));
    if (totalPages <= 1) { pagNav.classList.add("d-none"); pagUl.innerHTML = ""; return }
    pagNav.classList.remove("d-none");
    var html = '';
    html += '<li class="page-item ' + (currentPage <= 1 ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '" aria-label="Previous"><span aria-hidden="true"><img src="img/arrow-left.svg" height="13" alt="Anterior"></span></a></li>';
    for (var p = 1; p <= totalPages; p++) { html += '<li class="page-item"><a class="page-link ' + (p === currentPage ? 'active' : '') + '" href="#" data-page="' + p + '">' + p + '</a></li>' }
    html += '<li class="page-item ' + (currentPage >= totalPages ? 'disabled' : '') + '"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '" aria-label="Next"><span aria-hidden="true"><img src="img/arrow-right.svg" height="13" alt="Siguiente"></span></a></li>';
    pagUl.innerHTML = html;
    pagUl.querySelectorAll("a.page-link").forEach(function (a) {
      a.addEventListener("click", function (ev) {
        ev.preventDefault();
        var p = parseInt(a.getAttribute("data-page"), 10);
        if (!isNaN(p) && p >= 1 && p <= totalPages && p !== currentPage) { currentPage = p; render() }
      })
    })
  }

  function render() {
    setTitle();
    var list = filterProducts();
    buildPagination(list.length);
    renderGrid(list);
    buildSubcatPills();
    markActivePill()
  }

  if (pillsUl) {
    pillsUl.addEventListener('click', function (ev) {
      var btn = ev.target.closest('.nav-link'); if (!btn) return;
      currentSubcat = btn.getAttribute('data-subcat') || '';
      currentPage = 1;
      markActivePill();
      render()
    })
  }

  document.querySelectorAll(".slideProdInterna .swiper-slide").forEach(function (slide) {
    slide.addEventListener("click", function () {
      var h2 = slide.querySelector("h2"); var text = h2 ? h2.textContent : "";
      currentCategory = keyFromSliderText(text);
      currentSubcat = "";
      allSubcats = (SUBS_BY_CAT[currentCategory] || []).slice(0);
      currentPage = 1;
      render();

      // Mover el slide clickeado a la primera posición
      var idx = slide.getAttribute("data-swiper-slide-index");
      if (swiperInstance && idx !== null) {
        swiperInstance.slideToLoop(parseInt(idx));
      }
    })
  });

  function selectInitialSlide() {
    if (!swiperInstance) return;
    var slides = document.querySelectorAll(".slideProdInterna .swiper-slide:not(.swiper-slide-duplicate)");
    slides.forEach(function (slide) {
      var h2 = slide.querySelector("h2");
      var text = h2 ? h2.textContent : "";
      var key = keyFromSliderText(text);
      if (key === currentCategory) {
        var idx = slide.getAttribute("data-swiper-slide-index");
        if (idx !== null) {
          swiperInstance.slideToLoop(parseInt(idx), 0);
        }
      }
    });
  }

  $.getJSON("ajax/products-all.php")
    .done(function (resp) {
      if (!resp || !resp.success) { ALL = []; MAP = {}; SUBS_BY_CAT = {}; return }
      ALL = resp.products || [];
      MAP = resp.categories || {};
      SUBS_BY_CAT = resp.subcats_by_category || {};
      rebuildReverse();
      allSubcats = (SUBS_BY_CAT[currentCategory] || []).slice(0);
      render();
      selectInitialSlide();
    })
    .fail(function () {
      ALL = []; MAP = {}; SUBS_BY_CAT = {};
      render();
      selectInitialSlide();
    })

});
