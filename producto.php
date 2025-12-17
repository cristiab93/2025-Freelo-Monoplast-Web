<?php
include_once("_general.php");
?>
<?php include_once("templates/head-info.php"); ?>
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="index.php"><img src="img/logo-color.svg" alt="Monoplast"></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mx-auto nav-interna mb-2 mb-lg-0 px-4">
          <li class="nav-item"><a class="nav-link" aria-current="page" href="#">Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="nosotros.php">Nosotros</a></li>
          <li class="nav-item"><a class="nav-link" href="https://sanitariosmunro.mercadoshops.com.ar/" target="_blank">Mercado Shops</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
        </ul>
        <form class="d-flex align-items-center" role="search">
          <div class="search-box me-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 23 23" fill="none"><g clip-path="url(#clip0_203_1356)"><path d="M8.54993 0.0299315C16.7458 -0.606757 21.3989 9.12442 16.0931 15.2836L22.8424 22.0211C23.0675 22.2378 23.0615 22.9089 22.7554 23L22.2287 22.7833L15.3878 15.9875C11.4624 19.289 5.57889 19.0215 2.20122 15.0818C-2.69648 9.36953 1.08633 0.609826 8.54993 0.0299315ZM3.38063 15.028C10.9493 22.3873 22.4057 11.4574 15.3368 3.64232C7.82067 -4.66453 -4.60365 7.26517 3.38063 15.028Z" fill="#8D8D8D"/></g><defs><clipPath id="clip0_203_1356"><rect width="23" height="23" fill="white"/></clipPath></defs></svg>
            <input class="form-control" type="search" placeholder="Buscar" aria-label="Search">
          </div>
          <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 47 47" fill="none"><path d="M23.5 0C36.4787 0 47 10.5213 47 23.5C47 36.4787 36.4787 47 23.5 47C10.5213 47 0 36.4787 0 23.5C0 10.5213 10.5213 0 23.5 0ZM18.0762 32.9434C16.808 32.9434 15.7795 33.8757 15.7793 35.0254C15.7793 36.1753 16.8078 37.1074 18.0762 37.1074C19.3443 37.1072 20.3721 36.1751 20.3721 35.0254C20.3718 33.8758 19.3442 32.9436 18.0762 32.9434ZM29.6133 32.9434C28.3453 32.9436 27.3176 33.8758 27.3174 35.0254C27.3174 36.1751 28.3451 37.1072 29.6133 37.1074C30.8816 37.1074 31.9102 36.1753 31.9102 35.0254C31.9099 33.8757 30.8815 32.9434 29.6133 32.9434ZM12.6846 12.2461C12.0519 12.0202 10.981 11.8217 10.4102 12.2627C9.83942 12.7037 9.8635 13.5017 10.4863 13.8955C10.99 14.2136 11.5298 14.0075 12.001 14.2393C12.5411 14.5054 12.5639 15.0843 12.6396 15.5713C13.2658 19.6195 13.5793 23.7155 14.2314 27.7617C14.7581 29.9897 16.8896 31.6412 19.3818 31.8691L32.041 31.8721C33.2954 31.7159 33.4462 30.1296 32.1777 29.832C27.8663 29.7407 23.5361 29.9062 19.2334 29.748C18.0604 29.5613 16.9743 28.7431 16.6504 27.7021L30.6895 27.7041C33.2633 27.543 35.3555 25.9326 35.9795 23.6729C36.1831 22.0229 36.8688 20.1928 36.9912 18.5586C37.1285 16.7223 35.7065 15.3164 33.7178 15.1475L14.8652 15.1494V15.1484C14.8836 13.8747 13.979 12.7097 12.6846 12.2461Z" fill="#0A0338"/></svg>
        </form>
      </div>
    </div>
  </nav>

  <section class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-auto mb-4">
          <div class="bg-blue-light px-4 fw-500 py-2 rounded-5">
            <p id="prod-category" class="mb-0 font12 text-white"></p>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-md-6 col-lg-5 zoom-container">
          <img src="img/placeholder.png" id="zoom-img" class="img-fluid">
        </div>
        <div class="col-12 col-md-6 col-lg-6 offset-lg-1">
          <h3 id="prod-title" class="blue mb-5 font38"></h3>
          <ul id="prod-features" class="blue fw-400 ps-3"></ul>
          <form>
            <div class="d-flex mt-5">
              <input type="number" value="1" min="1" class="form-control rounded-0 border-blue text-center" style="max-width: 80px;">
              <button class="btn btn-primary border-0 rounded-0 font13 ms-2 px-4" type="button">Agregar al presupuesto</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

  <section class="pb-5">
    <div class="container">
      <div class="row">
        <div class="col-12 border-bottom border-blue">
          <p class="blue mb-2 fw-400 ls-0">Otras personas también llevaron</p>
        </div>
        <div class="col-12 mt-4">
          <div id="relacionados" class="row gx-3"></div>
        </div>
      </div>
    </div>
  </section>

  <?php include_once("templates/footer.php"); ?>

  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
  <script src="js/producto.js"></script>
</body>
</html>
