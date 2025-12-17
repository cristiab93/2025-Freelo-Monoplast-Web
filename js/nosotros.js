document.addEventListener("DOMContentLoaded", function () {
  var sw1El = document.querySelector(".mySwiper");
  if (sw1El) {
    new Swiper(".mySwiper", {
      navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" }
    });
  }

  var swProdEl = document.querySelector(".slideProductos");
  if (swProdEl) {
    new Swiper(".slideProductos", {
      direction: "vertical",
      slidesPerView: 1,
      autoHeight: true,
      spaceBetween: 20,
      mousewheel: true,
      pagination: { el: ".swiper-pagination", clickable: true }
    });
  }

  var swServEl = document.querySelector(".slideServicios");
  if (swServEl) {
    new Swiper(".slideServicios", {
      loop: true,
      mousewheel: true,
      slidesPerView: 3,
      spaceBetween: 15,
      pagination: { el: ".swiper-pagination", clickable: true },
      breakpoints: {
        640: { slidesPerView: 2, spaceBetween: 20 },
        768: { slidesPerView: 3, spaceBetween: 20 },
        1024: { slidesPerView: 3, spaceBetween: 15 }
      }
    });
  }
});
