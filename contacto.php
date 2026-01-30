<?php
include_once("_general.php");
?>
<?php include_once("templates/head-info.php"); ?>
</head>
<body class="bg-blue-light">
   <a href="https://web.whatsapp.com/send?phone=541161358093" target="_blank">
        <div class="wpp-button"><svg xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 448 512"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.-->
                <path fill="#ffffff"
                    d="M380.9 97.1c-41.9-42-97.7-65.1-157-65.1-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480 117.7 449.1c32.4 17.7 68.9 27 106.1 27l.1 0c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3 18.6-68.1-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1s56.2 81.2 56.1 130.5c0 101.8-84.9 184.6-186.6 184.6zM325.1 300.5c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8s-14.3 18-17.6 21.8c-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7s-12.5-30.1-17.1-41.2c-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2s-9.7 1.4-14.8 6.9c-5.1 5.6-19.4 19-19.4 46.3s19.9 53.7 22.6 57.4c2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4s4.6-24.1 3.2-26.4c-1.3-2.5-5-3.9-10.5-6.6z" />
            </svg></div>
    </a>
  <!-- HEADER -->
<header id="main-header" class="position-fixed index-9 top-0 start-0 end-0">
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      
      <!-- LOGO -->
      <a class="navbar-brand" href="index.php">
        <img src="img/logo.svg" alt="Monoplast" width="120">
      </a>

      <!-- ----------------------------------------------------------- -->
      <!-- ZONA DERECHA (MÓVIL): CARRITO + HAMBURGUESA -->
      <!-- ----------------------------------------------------------- -->
      <div class="d-flex align-items-center gap-2 order-lg-last">
        
        <!-- CARRITO MÓVIL (Solo visible en pantallas chicas 'd-lg-none') -->
        <!-- Lo ponemos aquí afuera para que quede al lado de la hamburguesa -->
        <a class="position-relative d-inline-block d-lg-none me-2" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" style="cursor: pointer;">
            <svg class="icon-menu-mobile" xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 47 47" fill="none">
                 <!-- NOTA: Usamos un fill blanco o invertido si el fondo es azul, o oscuro si es blanco. -->
                 <!-- Aquí copio tu SVG exacto -->
                 <path d="M23.5 0C36.4787 0 47 10.5213 47 23.5C47 36.4787 36.4787 47 23.5 47C10.5213 47 0 36.4787 0 23.5C0 10.5213 10.5213 0 23.5 0ZM18.0762 32.9434C16.808 32.9434 15.7795 33.8757 15.7793 35.0254C15.7793 36.1753 16.8078 37.1074 18.0762 37.1074C19.3443 37.1072 20.3721 36.1751 20.3721 35.0254C20.3718 33.8758 19.3442 32.9436 18.0762 32.9434ZM29.6133 32.9434C28.3453 32.9436 27.3176 33.8758 27.3174 35.0254C27.3174 36.1751 28.3451 37.1072 29.6133 37.1074C30.8816 37.1074 31.9102 36.1753 31.9102 35.0254C31.9099 33.8757 30.8815 32.9434 29.6133 32.9434ZM12.6846 12.2461C12.0519 12.0202 10.981 11.8217 10.4102 12.2627C9.83942 12.7037 9.8635 13.5017 10.4863 13.8955C10.99 14.2136 11.5298 14.0075 12.001 14.2393C12.5411 14.5054 12.5639 15.0843 12.6396 15.5713C13.2658 19.6195 13.5793 23.7155 14.2314 27.7617C14.7581 29.9897 16.8896 31.6412 19.3818 31.8691L32.041 31.8721C33.2954 31.7159 33.4462 30.1296 32.1777 29.832C27.8663 29.7407 23.5361 29.9062 19.2334 29.748C18.0604 29.5613 16.9743 28.7431 16.6504 27.7021L30.6895 27.7041C33.2633 27.543 35.3555 25.9326 35.9795 23.6729C36.1831 22.0229 36.8688 20.1928 36.9912 18.5586C37.1285 16.7223 35.7065 15.3164 33.7178 15.1475L14.8652 15.1494V15.1484C14.8836 13.8747 13.979 12.7097 12.6846 12.2461ZM33.8799 17.2578C34.3152 17.3277 34.7809 17.9587 34.7217 18.3613C33.8908 20.9089 34.6205 25.2048 30.6904 25.5957L16.2178 25.5938L15.1895 17.2578H33.8799Z" fill="#FFFFFF"/>
            </svg>
            <span class="cart-badge" style="display: none;">0</span>
        </a>

        <!-- BOTÓN HAMBURGUESA -->
        <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span> 
        </button>

        <!-- CARRITO DESKTOP (Solo visible en pantallas grandes 'd-none d-lg-block') -->
        <!-- Lo mantenemos aquí para que en desktop se vea a la derecha de todo -->
        <a class="position-relative d-none d-lg-block ms-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" style="cursor: pointer;">
            <svg class="icon-menu" xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 47 47" fill="none">
                 <path d="M23.5 0C36.4787 0 47 10.5213 47 23.5C47 36.4787 36.4787 47 23.5 47C10.5213 47 0 36.4787 0 23.5C0 10.5213 10.5213 0 23.5 0ZM18.0762 32.9434C16.808 32.9434 15.7795 33.8757 15.7793 35.0254C15.7793 36.1753 16.8078 37.1074 18.0762 37.1074C19.3443 37.1072 20.3721 36.1751 20.3721 35.0254C20.3718 33.8758 19.3442 32.9436 18.0762 32.9434ZM29.6133 32.9434C28.3453 32.9436 27.3176 33.8758 27.3174 35.0254C27.3174 36.1751 28.3451 37.1072 29.6133 37.1074C30.8816 37.1074 31.9102 36.1753 31.9102 35.0254C31.9099 33.8757 30.8815 32.9434 29.6133 32.9434ZM12.6846 12.2461C12.0519 12.0202 10.981 11.8217 10.4102 12.2627C9.83942 12.7037 9.8635 13.5017 10.4863 13.8955C10.99 14.2136 11.5298 14.0075 12.001 14.2393C12.5411 14.5054 12.5639 15.0843 12.6396 15.5713C13.2658 19.6195 13.5793 23.7155 14.2314 27.7617C14.7581 29.9897 16.8896 31.6412 19.3818 31.8691L32.041 31.8721C33.2954 31.7159 33.4462 30.1296 32.1777 29.832C27.8663 29.7407 23.5361 29.9062 19.2334 29.748C18.0604 29.5613 16.9743 28.7431 16.6504 27.7021L30.6895 27.7041C33.2633 27.543 35.3555 25.9326 35.9795 23.6729C36.1831 22.0229 36.8688 20.1928 36.9912 18.5586C37.1285 16.7223 35.7065 15.3164 33.7178 15.1475L14.8652 15.1494V15.1484C14.8836 13.8747 13.979 12.7097 12.6846 12.2461ZM33.8799 17.2578C34.3152 17.3277 34.7809 17.9587 34.7217 18.3613C33.8908 20.9089 34.6205 25.2048 30.6904 25.5957L16.2178 25.5938L15.1895 17.2578H33.8799Z" fill="#0A0338"/>
            </svg>
            <span class="cart-badge" style="display: none;">0</span>
        </a>
      </div>

      <!-- ----------------------------------------------------------- -->
      <!-- ZONA COLAPSABLE (LINKS + BUSCADOR) -->
      <!-- ----------------------------------------------------------- -->
      <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
        
        <!-- LINKS -->
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">
          <li class="nav-item"><a class="nav-link" href="productos.php">Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="nosotros.php">Nosotros</a></li>
          <li class="nav-item"><a class="nav-link" href="https://sanitariosmunro.mercadoshops.com.ar/" target="_blank">Mercado Shops</a></li>
          <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
        </ul>

        <!-- BUSCADOR (Siempre dentro del collapse ahora) -->
        <div class="search-box position-relative d-flex align-items-center rounded-pill px-2 py-1 mx-auto mx-lg-0 mt-5 mt-md-0">
             <svg id="globalSearchBtn" style="cursor:pointer;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 23 23" fill="none"><path d="M8.54993 0.0299315C16.7458 -0.606757 21.3989 9.12442 16.0931 15.2836L22.8424 22.0211C23.0675 22.2378 23.0615 22.9089 22.7554 23L22.2287 22.7833L15.3878 15.9875C11.4624 19.289 5.57889 19.0215 2.20122 15.0818C-2.69648 9.36953 1.08633 0.609826 8.54993 0.0299315ZM3.38063 15.028C10.9493 22.3873 22.4057 11.4574 15.3368 3.64232C7.82067 -4.66453 -4.60365 7.26517 3.38063 15.028Z" fill="#FFFFFF"/></svg>
             <input id="globalSearchInput" class="form-control shadow-none bg-transparent" type="search" placeholder="Buscar" aria-label="Search" >
        </div>

      </div>
    </div>
  </nav>
</header>

<!-- PRODUCTOS CARRITO -->

  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
  <div class="offcanvas-header mb-4 border-bottom">
    <h5 class="offcanvas-title blue" id="offcanvasWithBothOptionsLabel">Presupuesto</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
  </div>
</div>

<main>
  <section class="bg-contacto bg-blue-light">
    <div class="container">
      <div class="row align-items-center justify-content-center">
      <div class="col-10 col-lg-6">
      <h2 class="kento fw-400">CONTACTATE <br>CON NOSOTROS  </h2>
<p class="text-white font20 mt-3">Envianos tu consulta y en breve nos <br class="d-none d-lg-block">pondremos en contacto.</p>
        <div class="mt-4 text-white">
          <p class="mb-1"><strong class="uppercase font14">Local:</strong> Av. Mitre 2415, Munro</p>
          <p class="mb-0"><strong class="uppercase font14">Teléfono:</strong> 011 4756-2345</p>
        </div>
      </div>
      <div class="col-10 col-lg-5 mt-4 mt-lg-0">
      <form id="contact-form">

      <div class="form-floating mb-3">
  <input type="text" class="form-control" id="contact-nombre" placeholder="Nombre" required>
  <label for="contact-nombre">Nombre</label>
</div>

      <div class="form-floating mb-3">
  <input type="email" class="form-control" id="contact-email" placeholder="Email" required>
  <label for="contact-email">Email</label>
</div>

  <div class="form-floating">
  <textarea class="form-control" placeholder="Mensaje" id="contact-mensaje" style="min-height: 120px" required></textarea>
  <label for="contact-mensaje">Mensaje</label>
</div>
<div class="text-end mt-4">
<button type="submit" id="contact-submit" class="btn btn-light fw-500 font14 blue-light py-2 rounded-3 px-5">Enviar</button>
</div>
      </form>

      </div>
      </div>
    </div>
  </section>

</main>
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
  <?php include_once("templates/footer.php"); ?>
  <script src="js/contacto.js"></script>
  <script>
  window.addEventListener('scroll', function () {
  const header = document.getElementById('main-header');
  if (!header) return;

  if (window.scrollY > 50) {
    header.classList.add('header-scrolled');
  } else {
    header.classList.remove('header-scrolled');
  }
});
</script>
</body>
</html>
