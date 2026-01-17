<?php
include_once ("_general.php");
?>
<?php include_once ("templates/head-info.php"); ?>

</head>
<body>

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
                 <path d="M23.5 0C36.4787 0 47 10.5213 47 23.5C47 36.4787 36.4787 47 23.5 47C10.5213 47 0 36.4787 0 23.5C0 10.5213 10.5213 0 23.5 0ZM18.0762 32.9434C16.808 32.9434 15.7795 33.8757 15.7793 35.0254C15.7793 36.1753 16.8078 37.1074 18.0762 37.1074C19.3443 37.1072 20.3721 36.1751 20.3721 35.0254C20.3718 33.8758 19.3442 32.9436 18.0762 32.9434ZM29.6133 32.9434C28.3453 32.9436 27.3176 33.8758 27.3174 35.0254C27.3174 36.1751 28.3451 37.1072 29.6133 37.1074C30.8816 37.1074 31.9102 36.1753 31.9102 35.0254C31.9099 33.8757 30.8815 32.9434 29.6133 32.9434ZM12.6846 12.2461C12.0519 12.0202 10.981 11.8217 10.4102 12.2627C9.83942 12.7037 9.8635 13.5017 10.4863 13.8955C10.99 14.2136 11.5298 14.0075 12.001 14.2393C12.5411 14.5054 12.5639 15.0843 12.6396 15.5713C13.2658 19.6195 13.5793 23.7155 14.2314 27.7617C14.7581 29.9897 16.8896 31.6412 19.3818 31.8691L32.041 31.8721C33.2954 31.7159 33.4462 30.1296 32.1777 29.832C27.8663 29.7407 23.5361 29.9062 19.2334 29.748C18.0604 29.5613 16.9743 28.7431 16.6504 27.7021L30.6895 27.7041C33.2633 27.543 35.3555 25.9326 35.9795 23.6729C36.1831 22.0229 36.8688 20.1928 36.9912 18.5586C37.1285 16.7223 35.7065 15.3164 33.7178 15.1475L14.8652 15.1494V15.1484C14.8836 13.8747 13.979 12.7097 12.6846 12.2461ZM33.8799 17.2578C34.3152 17.3277 34.7809 17.9587 34.7217 18.3613C33.8908 20.9089 34.6205 25.2048 30.6904 25.5957L16.2178 25.5938L15.1895 17.2578H33.8799Z" fill="#0A0338"/>
            </svg>
            <span class="cart-badge">2</span>
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
            <span class="cart-badge">2</span>
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
             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 23 23" fill="none"><path d="M8.54993 0.0299315C16.7458 -0.606757 21.3989 9.12442 16.0931 15.2836L22.8424 22.0211C23.0675 22.2378 23.0615 22.9089 22.7554 23L22.2287 22.7833L15.3878 15.9875C11.4624 19.289 5.57889 19.0215 2.20122 15.0818C-2.69648 9.36953 1.08633 0.609826 8.54993 0.0299315ZM3.38063 15.028C10.9493 22.3873 22.4057 11.4574 15.3368 3.64232C7.82067 -4.66453 -4.60365 7.26517 3.38063 15.028Z" fill="#FFFFFF"/></svg>
             <input class="form-control shadow-none bg-transparent" type="search" placeholder="Buscar" aria-label="Search" >
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
  <p hidden>El carrito de compras está vacío.</p>
  
  <div class="d-flex align-items-center mb-3">
  <div class="offcanvas-product rounded-3" style="background-image: url(img/termotanque.png)"></div>
  <div class="ms-3">
  <p class="blue fw-700 mb-0 font14">Ariston Termotanque Eléctrico </p>
  <p class="blue font12 mb-0">Pro Eco 100 lts</p>
  </div>
  <div class="d-flex flex-column align-items-center justify-content-between qty-container ms-4">
    
    <!-- Botón Sumar -->
    <button class="qty-btn" type="button" aria-label="Aumentar cantidad">
    + <!-- O usa simplemente "+" -->
    </button>

    <!-- Número (Visual) -->
    <div class="qty-display">
      1
    </div>
    
    <!-- Input real (para enviar en formulario) -->
    <input type="number" class="qty-input-hidden" value="1" min="1" name="cantidad">

    <!-- Botón Restar -->
    <button class="qty-btn" type="button" aria-label="Disminuir cantidad">
    - <!-- O usa simplemente "-" -->
    </button>
    
  </div>
  </div>

  <div class="d-flex align-items-center mb-3">
  <div class="offcanvas-product rounded-3" style="background-image: url(img/termotanque.png)"></div>
  <div class="ms-3">
  <p class="blue fw-700 mb-0 font14">Ariston Termotanque Eléctrico </p>
  <p class="blue font12 mb-0">Pro Eco 100 lts</p>
  </div>
  <div class="d-flex flex-column align-items-center justify-content-between qty-container ms-4">
    
    <!-- Botón Sumar -->
    <button class="qty-btn" type="button" aria-label="Aumentar cantidad">
    + <!-- O usa simplemente "+" -->
    </button>

    <!-- Número (Visual) -->
    <div class="qty-display">
      1
    </div>
    
    <!-- Input real (para enviar en formulario) -->
    <input type="number" class="qty-input-hidden" value="1" min="1" name="cantidad">

    <!-- Botón Restar -->
    <button class="qty-btn" type="button" aria-label="Disminuir cantidad">
    - <!-- O usa simplemente "-" -->
    </button>
    
  </div>
  </div>

  <div class="d-flex align-items-center mb-3">
  <div class="offcanvas-product rounded-3" style="background-image: url(img/termotanque.png)"></div>
  <div class="ms-3">
  <p class="blue fw-700 mb-0 font14">Ariston Termotanque Eléctrico </p>
  <p class="blue font12 mb-0">Pro Eco 100 lts</p>
  </div>
  <div class="d-flex flex-column align-items-center justify-content-between qty-container ms-4">
    
    <!-- Botón Sumar -->
    <button class="qty-btn" type="button" aria-label="Aumentar cantidad">
    + <!-- O usa simplemente "+" -->
    </button>

    <!-- Número (Visual) -->
    <div class="qty-display">
      1
    </div>
    
    <!-- Input real (para enviar en formulario) -->
    <input type="number" class="qty-input-hidden" value="1" min="1" name="cantidad">

    <!-- Botón Restar -->
    <button class="qty-btn" type="button" aria-label="Disminuir cantidad">
    - <!-- O usa simplemente "-" -->
    </button>
    
  </div>
  </div>

  <!-- PRECIO -->
  <div class="border-top mt-4 pt-4">
  <!--<div class="d-flex align-items-center justify-content-between">
  <p class="mb-0 fw-700 font14 text-grey">Total presupuesto</p>
  <h5 class="mb-0 text-grey fw-300">$384.344 <span class="font12">ARS</span></h5>
  </div>-->

  <div class="d-flex align-items-center justify-content-between">
  <p class="mb-0 fw-700 font14 blue">Total carrito</p>
  <h5 class="mb-0 blue fw-300">$1.384.344 <span class="font12">ARS</span></h5>
  </div>
  </div>
  <div class="text-center mt-4">
  <button class="btn btn-primary font14 blue border-blue rounded-5 px-4 mt-3">Enviar presupuesto</button>
  </div>
  </div>
</div>

  <section>
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide bg-slide-1">
          <div class="content container h-100 d-flex flex-column align-items-start justify-content-center text-start">
            <h1 class="mb-0 kento">Llegó la <br>temporada de pileta</h1>
          </div>
        </div>
        <div class="swiper-slide bg-slide-2">
          <div class="content container h-100 d-flex flex-column align-items-start justify-content-center text-start">
            <h1 class="mb-0 kento">Más de 40 años <br>acompañando tus obras</h1>
          </div>
        </div>
        <div class="swiper-slide bg-slide-3">
          <div class="content container h-100 d-flex flex-column align-items-start justify-content-center text-start">
            <h1 class="mb-0 kento">Nuestras <br>mamparas</h1>
          </div>
        </div>
      </div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
  </section>

  <section class="py-5 buscados">
    <div class="container">
      <div class="row">
        <div class="col-md-12 offset-md-0 col-10 offset-1 border-bottom border-blue mb-4">
          <p class="blue mb-2 fw-600">Más buscados</p>
        </div>
      </div>
      <div id="mas-buscados" class="row"></div>
    </div>
  </section>

  <section class="bg-blue-light">
    <div class="container">
      <div class="row gx-5">
        <div class="col-10 offset-1 offset-md-0 col-md-6">
          <div class="swiper slideProductos py-5">
            <div class="swiper-wrapper">
              <div class="swiper-slide bg-producto1"><div class="tag-producto white bg-blue2 rounded-5 font14 fit-content border px-4 py-2 me-3 mt-3 ms-auto ls-05">Calefacción</div></div>
              <div class="swiper-slide bg-producto2"><div class="tag-producto white bg-blue2 rounded-5 font14 fit-content border px-4 py-2 me-3 mt-3 ms-auto ls-05">Piletas</div></div>
              <div class="swiper-slide bg-producto3"><div class="tag-producto white bg-blue2 rounded-5 font14 fit-content border px-4 py-2 me-3 mt-3 ms-auto ls-05">Artefactos y mamparas</div></div>
              <div class="swiper-slide bg-producto4"><div class="tag-producto white bg-blue2 rounded-5 font14 fit-content border px-4 py-2 me-3 mt-3 ms-auto ls-05">Construcción</div></div>
              <div class="swiper-slide bg-producto5"><div class="tag-producto white bg-blue2 rounded-5 font14 fit-content border px-4 py-2 me-3 mt-3 ms-auto ls-05">Infraestructura</div></div>
              <div class="swiper-slide bg-producto6"><div class="tag-producto white bg-blue2 rounded-5 font14 fit-content border px-4 py-2 me-3 mt-3 ms-auto ls-05">Sistemas de riego</div></div>
            </div>
            <div class="swiper-pagination"></div>
          </div>
        </div>
        <div class="col-10 offset-1 offset-md-0 col-md-6 py-5">
          <p class="white mb-0 pb-2 border-bottom border-light">Nuestros productos</p>
          <h2 class="mb-md-5 mb-4 mt-4">Conocé todo lo que <br class="d-none d-md-block">tenemos para tu proyecto</h2>
          <p class="white mb-4 font13">La mejor atención al cliente y el mejor servicio post-venta del mercado. Nos adaptamos a las necesidades del cliente, ya sean: Empresas comerciales o industriales, consumidor final, arquitectos, instaladores o plomeros.</p>
          <button type="button" class="btn btn-light fw-500 font14 blue rounded-5 px-4" onclick="window.location.href='productos.php';">Ver todos los productos</button>
        </div>
      </div>
    </div>
  </section>

  <section class="py-5 servicios">
    <div class="container">
      <div class="row">
        <div class="col-10 offset-1 col-md-12 offset-md-0 border-bottom border-blue mb-4">
          <p class="blue mb-2 fw-600">Nuestros servicios</p>
        </div>
        <div class="col-10 offset-1 col-md-12 offset-md-0">
          <div class="swiper slideServicios">
            <div class="swiper-wrapper mb-5">
              <div class="swiper-slide text-start">
                <div class="bg-servicio bg-producto6"><div class="d-flex align-items-center justify-content-center h-100 position-absolute w-100 index-9"><img src="img/icono-riego.svg" height="100"></div></div>
                <div class="pe-5"><h3 class="blue fw-700 mt-3">Instalacion de riego por aspersión</h3><p class="blue font13 fw-400">Sistemas de riego para canchas de Fútbol, canchas de Golf, Jardines, etc</p></div>
              </div>
              <div class="swiper-slide text-start">
                <div class="bg-servicio bg-servicio3"><div class="d-flex align-items-center justify-content-center h-100 position-absolute w-100 index-9"><img src="img/icono-pileta.svg" height="95"></div></div>
                <div class="pe-5"><h3 class="blue fw-700 mt-3">Construcción de <br>piletas</h3><p class="blue font13 fw-400">Servicio de construcción de piletas, tanto de natación como piletas residenciales.</p></div>
              </div>
              <div class="swiper-slide text-start">
                <div class="bg-servicio bg-servicio2"><div class="d-flex align-items-center justify-content-center h-100 position-absolute w-100 index-9"><img src="img/icono-reparacion.svg" height="95"></div></div>
                <div class="pe-5"><h3 class="blue fw-700 mt-3">Reparación de <br>bombas y filtros</h3><p class="blue font13 fw-400">Servicio especializado en reparación e instalación de bombas de agua y filtros.</p></div>
              </div>
              <div class="swiper-slide text-start">
                <div class="bg-servicio bg-servicio4"><div class="d-flex align-items-center justify-content-center h-100 position-absolute w-100 index-9"><img src="img/icono-pieza.svg" height="80"></div></div>
                <div class="pe-5"><h3 class="blue fw-700 mt-3">Piezas <br>especiales</h3><p class="blue font13 fw-400">Se realizan piezas y tubos especiales a medida (de 40mm a 50mm) y en distintos materiales.</p></div>
              </div>
            </div>
            <div class="swiper-pagination"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-light py-5">
    <div class="container">
      <div class="row">
        <div class="col-10 offset-1 col-md-12 offset-md-0 text-center">
          <h4 class="blue-light fw-600 mb-5">La mejor atención al <br class="d-none d-md-block">cliente y servicio <br class="d-none d-md-block"> post-venta del mercado</h4>
          <script src="https://elfsightcdn.com/platform.js" async></script>
          <!-- Elfsight Google Reviews | Monoplast -->
<div class="elfsight-app-cfa65342-c921-4095-90bd-0db03d20cb77" data-elfsight-app-lazy></div>
        </div>
      </div> 
    </div>
  </section>

  <?php include_once ("templates/footer.php") ?>

  <script src="js/jquery-3.5.1.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
  <script src="js/index.js"></script>



</body>
</html>
