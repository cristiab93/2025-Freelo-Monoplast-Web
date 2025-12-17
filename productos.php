<?php
$ORGANIGRAMA = TRUE;
include "_general.php";
?>
<?php include_once("templates/head-info.php"); ?>
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="index.php"><img src="img/logo-color.svg" alt="Monoplast"></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mx-auto nav-interna mb-2 mb-lg-0 px-4">
          <li class="nav-item"><a class="nav-link" aria-current="page" href="productos.php">Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="nosotros.php">Nosotros</a></li>
          <li class="nav-item"><a class="nav-link" href="https://sanitariosmunro.mercadoshops.com.ar/" target="_blank">Mercado Shops</a></li>
          <li class="nav-item"><a class="nav-link" href="contacto.php">Contacto</a></li>
        </ul>
        <form class="d-flex align-items-center" role="search">
          <div class="search-box me-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 23 23" fill="none">
              <g clip-path="url(#clip0_203_1356)">
                <path d="M8.55 0.03C16.75 -0.61 21.40 9.12 16.09 15.28L22.84 22.02C23.07 22.24 23.06 22.91 22.76 23L22.23 22.78L15.39 15.99C11.46 19.29 5.58 19.02 2.20 15.08C-2.70 9.37 1.09 0.61 8.55 0.03ZM3.38 15.03C10.95 22.39 22.41 11.46 15.34 3.64C7.82 -4.66 -4.60 7.27 3.38 15.03Z" fill="#8D8D8D"/>
              </g>
              <defs><clipPath id="clip0_203_1356"><rect width="23" height="23" fill="white"/></clipPath></defs>
            </svg>
            <input class="form-control" type="search" placeholder="Buscar" aria-label="Search" />
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 47 47" fill="none">
            <path d="M23.5 0C36.48 0 47 10.52 47 23.5C47 36.48 36.48 47 23.5 47C10.52 47 0 36.48 0 23.5C0 10.52 10.52 0 23.5 0Z" fill="#0A0338"/>
          </svg>
        </form>
      </div>
    </div>
  </nav>

  <section class="py-5 buscados">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="swiper slideProdInterna">
            <div class="swiper-wrapper h-auto mb-5 align-items-end">
              <div class="swiper-slide text-start bg-product bg-producto1">
                <div class="d-flex align-items-start justify-content-start h-100 ps-4 pt-4 w-100"><h2>Calefacción</h2></div>
              </div>
              <div class="swiper-slide text-start bg-product bg-producto2">
                <div class="d-flex align-items-start justify-content-start h-100 ps-4 pt-4 w-100"><h2>Piletas</h2></div>
              </div>
              <div class="swiper-slide text-start bg-product bg-producto3">
                <div class="d-flex align-items-start justify-content-start h-100 ps-4 pt-4 w-100"><h2>Artefactos y mamparas</h2></div>
              </div>
              <div class="swiper-slide text-start bg-product bg-producto4">
                <div class="d-flex align-items-start justify-content-start h-100 ps-4 pt-4 w-100"><h2>Construcción</h2></div>
              </div>
              <div class="swiper-slide text-start bg-product bg-producto5">
                <div class="d-flex align-items-start justify-content-start h-100 ps-4 pt-4 w-100"><h2>Infraestructura</h2></div>
              </div>
              <div class="swiper-slide text-start bg-product bg-producto6">
                <div class="d-flex align-items-start justify-content-start h-100 ps-4 pt-4 w-100"><h2>Sistemas de riego</h2></div>
              </div>
            </div>
            <div class="swiper-button-prev custom-prev"></div>
            <div class="swiper-button-next custom-next"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="pb-5">
    <div class="container">
      <div class="row">
        <div class="col-12 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3 text-center">
          <h2 class="text-black" id="titulo-categoria">Calefacción</h2>
          <p class="fw-400 mt-3">
            Soluciones eficientes para que tu hogar y tus proyectos estén siempre a la temperatura ideal.
            Calidad y respaldo en cada producto.
          </p>
        </div>

        <div class="col-12 d-md-flex align-items-center justify-content-between mt-5">
          <ul class="nav nav-pills justify-content-center order-1" id="pills-tab" role="tablist"></ul>

          <div class="dropdown mt-3 mt-md-0 text-center order-2">
            <a class="btn px-lg-4 py-2 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Más reciente <svg class="ms-3" xmlns="http://www.w3.org/2000/svg" width="14" height="7" viewBox="0 0 17 9" fill="none">
                <path d="M15.811 1L8.40548 8.40555L0.999925 0.999999" stroke="#8D8D8D" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Action</a></li>
              <li><a class="dropdown-item" href="#">Another action</a></li>
              <li><a class="dropdown-item" href="#">Something else here</a></li>
            </ul>
          </div>
        </div>

        <div class="col-12 mt-4">
          <div class="row gx-3" id="grid-productos"></div>

          <nav id="nav-paginacion" class="d-none">
            <ul class="pagination justify-content-center mt-5" id="pagination"></ul>
          </nav>
        </div>
      </div>
    </div>
  </section>

  <?php include_once("templates/footer.php"); ?>

  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/productos.js"></script>
</body>
</html>
