document.addEventListener("DOMContentLoaded", function () {
  var swiperInstance;
  var v = new Date().getTime();
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
  function highlightText(text, query) {
    if (!query) return text;
    var nText = norm(text);
    var nQuery = norm(query);
    if (!nQuery || !nText.includes(nQuery)) return text;

    var result = "";
    var lastIndex = 0;
    var index = nText.indexOf(nQuery);

    while (index !== -1) {
      result += text.substring(lastIndex, index);
      // We use nQuery.length because norm() preserves character count for these accents/ñ
      var match = text.substring(index, index + nQuery.length);
      result += '<mark class="highlight">' + match + '</mark>';
      lastIndex = index + nQuery.length;
      index = nText.indexOf(nQuery, lastIndex);
    }
    result += text.substring(lastIndex);
    return result;
  }
  function initialCat() { var p = new URLSearchParams(window.location.search); var c = p.get("cat"); if (!c) return ""; c = norm(c); var v = ["calefaccion", "piletas", "artefactos", "construccion", "infraestructura", "riego"]; return v.includes(c) ? c : "" }
  function initialSubcat() { var p = new URLSearchParams(window.location.search); return p.get("subc") || ""; }
  function initialSearch() { var p = new URLSearchParams(window.location.search); return p.get("search") || ""; }

  var grid = document.getElementById("grid-productos");
  var pillsUl = document.getElementById("pills-tab");
  var titleEl = document.getElementById("titulo-categoria");
  var descEl = document.getElementById("descripcion-categoria");
  var swiperSection = document.querySelector(".buscados");
  var pagNav = document.getElementById("nav-paginacion");
  var pagUl = document.getElementById("pagination");

  var ALL = [], MAP = {}, REV = {}, SUBS_BY_CAT = {};
  var currentCategory = initialCat() || "calefaccion";
  var currentSubcat = initialSubcat();
  var currentSearch = initialSearch();
  var currentPage = 1;
  var perPage = 50;
  var totalPages = 1;
  var allSubcats = [];

  function rebuildReverse() { REV = {}; Object.keys(MAP || {}).forEach(function (k) { var v = MAP[k] || ""; REV[norm(v)] = k }) }
  function keyFromSliderText(text) { var t = norm(text); if (REV[t]) return REV[t]; var best = ""; Object.keys(REV).forEach(function (d) { if (!best && t.indexOf(d) >= 0) best = REV[d] }); if (best) return best; if (t.includes("artefactos") || t.includes("mamparas")) return "artefactos"; if (t.includes("construccion")) return "construccion"; if (t.includes("infraestructura")) return "infraestructura"; if (t.includes("piletas")) return "piletas"; if (t.includes("calefaccion")) return "calefaccion"; if (t.includes("riego")) return "riego"; return "calefaccion" }

  function setTitle() {
    if (!titleEl) return;
    const parentSection = titleEl.closest('section');
    if (currentSearch) {
      titleEl.textContent = 'Búsqueda: ' + currentSearch;
      if (descEl) descEl.style.display = 'none';
      if (swiperSection) swiperSection.style.display = 'none';
      if (parentSection) {
        parentSection.classList.add('mt-6');
        parentSection.classList.add('pt-5');
      }
    } else {
      var d = (MAP && MAP[currentCategory]) ? MAP[currentCategory] : "Productos";
      titleEl.textContent = d;
      if (descEl) descEl.style.display = 'block';
      if (swiperSection) swiperSection.style.display = 'block';
      if (parentSection) {
        parentSection.classList.remove('mt-6');
        parentSection.classList.remove('pt-5');
      }
    }
  }

  function filterProducts() {
    var searchQuery = norm(currentSearch);
    var list = ALL;

    if (searchQuery) {
      list = list.filter(function (p) {
        var n = norm(p.name || "");
        var c = norm(p.category_text || "");
        var s = norm(p.subcategory || "");

        return n.includes(searchQuery) || c.includes(searchQuery) || s.includes(searchQuery) || norm(p.category || "").includes(searchQuery);
      });
    } else {
      list = list.filter(function (p) { return p.category === currentCategory });
      if (currentSubcat) list = list.filter(function (p) { return (p.subcategory || "") === currentSubcat });
    }
    return list
  }

  function renderCard(p) {
    var href = "producto.php?id=" + encodeURIComponent(p.eid || "");
    var img = (p.image || "uploaded_img/ariston.png") + "?v=" + v;
    var name = p.name || "";
    var subn = p.subname || "";

    var displayName = currentSearch ? highlightText(name, currentSearch) : name;
    var displaySubname = currentSearch ? highlightText(subn, currentSearch) : subn;

    return '' +
      '<div class="col-12 col-sm-6 col-lg-4 col-xl-3 mt-3">' +
      '<div class="card bg-grey justify-content-between rounded-4 border-0 position-relative px-3 py-4 h-100 d-flex flex-column">' +
      '<div class="text-center" style="height: 180px; display: flex; align-items: center; justify-content: center;">' +
      '<img src="' + img + '" class="img-fluid multiply" style="max-height: 100%; object-fit: contain;">' +
      '</div>' +
      '<div class="mt-2">' +
      '<div class="justify-content-md-start">' +
      '<p class="blue mb-1 fw-700 uppercase product-title text-center text-md-start">' + displayName + '</p>' +
      '</div>' +
      '<p class="blue mb-0 font12 text-center text-md-start">' + displaySubname + '</p>' +
      '</div>' +
      '<div class="mt-4 d-flex justify-content-between gap-2">' +
      '<a href="' + href + '" class="btn btn-outline-dark font11 blue border-blue rounded-5 px-2 py-2 d-flex align-items-center justify-content-center w-50">Ver detalles</a>' +
      '<button type="button" class="btn btn-primary font11 text-white border-blue rounded-5 px-1 py-2 add-to-budget d-flex align-items-center justify-content-center w-50" data-id="' + (p.eid || "") + '" data-name="' + name + '" data-subname="' + subn + '" data-img="' + img + '">Agregar presupuesto</button>' +
      '</div>' +
      '</div>' +
      '</div>';
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

    // Check if search matches a category or subcategory
    var matchedCatKey = "";
    var matchedSubcatName = "";

    if (currentSearch) {
      var nSearch = norm(currentSearch);
      if (REV[nSearch]) {
        matchedCatKey = REV[nSearch];
      } else {
        if (MAP[nSearch]) matchedCatKey = nSearch;

        // If not a category, check subcategories
        if (!matchedCatKey) {
          Object.keys(SUBS_BY_CAT).forEach(function (k) {
            if (matchedCatKey) return;
            var subs = SUBS_BY_CAT[k] || [];
            subs.forEach(function (s) {
              if (norm(s) === nSearch) {
                matchedCatKey = k;
                matchedSubcatName = s;
              }
            });
          });
        }
      }
    }

    if (matchedCatKey) {
      var catName = MAP[matchedCatKey];
      // Breadcrumb Start
      html += '<li class="nav-item d-flex align-items-center" role="presentation"><button class="' + base + '" type="button" onclick="window.location.href=\'productos.php\'">Todos los productos</button></li>';

      // Category Part
      html += '<li class="nav-item d-flex align-items-center px-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#8D8D8D" class="bi bi-chevron-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg></li>';

      if (matchedSubcatName) {
        // Category is a link
        html += '<li class="nav-item" role="presentation"><button class="' + base + '" type="button" onclick="window.location.href=\'productos.php?cat=' + matchedCatKey + '\'">' + highlightText(catName, currentSearch) + '</button></li>';

        // Subcategory Part
        html += '<li class="nav-item d-flex align-items-center px-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#8D8D8D" class="bi bi-chevron-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg></li>';
        html += '<li class="nav-item" role="presentation"><button class="' + base + ' active" type="button" style="cursor:default">' + highlightText(matchedSubcatName, currentSearch) + '</button></li>';
      } else {
        // Just Category
        html += '<li class="nav-item" role="presentation"><button class="' + base + ' active" type="button" style="cursor:default">' + highlightText(catName, currentSearch) + '</button></li>';
      }

      // Only show subcats if it's a category match, not subcategory match
      if (!matchedSubcatName) {
        var catSubcats = (SUBS_BY_CAT[matchedCatKey] || []).slice(0);
        if (catSubcats.length > 0) {
          // Optionally render them?
        }
      }

    } else {
      html += '<li class="nav-item" role="presentation"><button class="' + base + (currentSubcat === '' ? ' active' : '') + '" type="button" data-subcat="" aria-selected="' + (currentSubcat === '' ? 'true' : 'false') + '">Todos los productos</button></li>';
      allSubcats.forEach(function (s) {
        var act = (s === currentSubcat);
        html += '<li class="nav-item" role="presentation"><button class="' + base + (act ? ' active' : '') + '" type="button" data-subcat="' + s.replace(/"/g, "&quot;") + '" aria-selected="' + (act ? 'true' : 'false') + '">' + s + '</button></li>'
      });
    }
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

  if (swEl) {
    swEl.addEventListener("click", function (ev) {
      var slide = ev.target.closest(".swiper-slide");
      if (!slide) return;

      var h2 = slide.querySelector("h2");
      var text = h2 ? h2.textContent : "";
      currentSearch = "";
      currentCategory = keyFromSliderText(text);
      currentSubcat = "";
      allSubcats = (SUBS_BY_CAT[currentCategory] || []).slice(0);
      currentPage = 1;

      // Update browser URL without reload to clear search
      var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?cat=' + currentCategory;
      window.history.pushState({ path: newUrl }, '', newUrl);

      render();

      // Mover el slide clickeado a la primera posición
      var idx = slide.getAttribute("data-swiper-slide-index");
      if (swiperInstance && idx !== null) {
        swiperInstance.slideToLoop(parseInt(idx));
      }

      scrollToResults();
    });
  }

  function scrollToResults() {
    const target = document.getElementById('titulo-categoria');
    if (target) {
      const offset = 180; // Increased framing offset
      const elementPosition = target.getBoundingClientRect().top;
      const offsetPosition = elementPosition + window.pageYOffset - offset;

      window.scrollTo({
        top: offsetPosition,
        behavior: "smooth"
      });
    }
  }

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
      if (currentSearch) {
        allSubcats = [];
      } else {
        allSubcats = (SUBS_BY_CAT[currentCategory] || []).slice(0);
      }
      render();
      selectInitialSlide();

      // Auto-scroll to results if there is a search active
      if (currentSearch) {
        scrollToResults();
      }
    })
    .fail(function () {
      ALL = []; MAP = {}; SUBS_BY_CAT = {};
      render();
      selectInitialSlide();
    })

});
