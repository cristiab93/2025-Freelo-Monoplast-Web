<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Google Tag Manager Injection
 * ID: GTM-N4T3GNPG
 */
function inject_gtm_buffer($buffer) {
    global $PAGE;
    
    // Evitar inyección en páginas de administración
    $is_admin = (isset($PAGE) && strncmp($PAGE, 'admin-', 6) === 0);
    if ($is_admin) {
        return $buffer;
    }

    $gtm_id = 'GTM-N4T3GNPG';

    $gtm_head = "<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','" . $gtm_id . "');</script>
<!-- End Google Tag Manager -->";

    $gtm_body = "<!-- Google Tag Manager (noscript) -->
<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=" . $gtm_id . "\"
height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->";

    // Inyectar en <head> (después de la apertura)
    $buffer = preg_replace('/<head>/i', "<head>\n" . $gtm_head, $buffer);
    
    // Inyectar después de <body> (incluyendo posibles atributos)
    $buffer = preg_replace('/<body([^>]*)>/i', "<body$1>\n" . $gtm_body, $buffer);

    return $buffer;
}
ob_start("inject_gtm_buffer");

require_once("conn/cfg.php");
require_once("conn/sql_latest.php");
require_once("conn/functions.php");
require_once("conn/load-globals.php");
require_once("conn/sed.php");
require_once("conn/get-time.php");

mysqli_report(MYSQLI_REPORT_ERROR);
($conn = mysqli_connect(DBSERVERNAME, DBUSERNAME, DBPASSWORD, DBNAME)) || salir_mant("DB_CONNECT_FAIL: " . mysqli_connect_error());
mysqli_set_charset($conn, 'utf8mb4');
mysqli_query($conn, "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
$_SESSION["conn"] = $conn;

$admin_username = 'admin';
$admin_password = 'monoplast2025';

function ShowAdminNavBar($selected)
{
    echo '
    <nav class="main-menu">
      <ul>
        <li>
          <a id="nav-index" href="index.php">
            <i class="fa fa-home fa-2x"></i>
            <span class="nav-text">Inicio</span>
          </a>
        </li>

        <li>
          <a id="nav-products" href="productos.php">
            <i class="fa fa-box fa-2x"></i>
            <span class="nav-text">Productos</span>
          </a>
        </li>

        <li>
          <a id="nav-categories" href="categories.php">
            <i class="fa fa-list fa-2x"></i>
            <span class="nav-text">Categorías</span>
          </a>
        </li>

        <li>
          <a id="nav-subcategories" href="subcategories.php">
            <i class="fa fa-sitemap fa-2x"></i>
            <span class="nav-text">Subcategorías</span>
          </a>
        </li>

        <li>
          <a id="nav-most-searched" href="most_searched.php">
            <i class="fa fa-star fa-2x"></i>
            <span class="nav-text">Más buscados</span>
          </a>
        </li>
        <li>
          <a id="nav-banners" href="banners.php">
            <i class="fa fa-images fa-2x"></i>
            <span class="nav-text">Banners</span>
          </a>
        </li>
      </ul>

      <ul class="logout">
        <li>
          <a id="nav-logout" href="logout.php">
            <i class="fa fa-power-off fa-2x"></i>
            <span class="nav-text">Salir</span>
          </a>
        </li>
      </ul>
    </nav>

    <script>
      (function () {
        try {
          var el = document.getElementById("'.htmlspecialchars($selected, ENT_QUOTES, "UTF-8").'");
          if (el) el.classList.add("btactivo");
        } catch(e){}
      })();
    </script>
    ';
}

function banners_json_path()
{
    return __DIR__ . '/data/banners.json';
}

function default_home_banners()
{
    return [
        [
            'title' => "Llegó la\ntemporada de pileta",
            'image_url' => 'img/bg-slide-1.jpg',
            'button_text' => 'Ver productos',
            'button_url' => 'productos.php?cat=piletas',
        ],
        [
            'title' => "Más de 40 años\nacompañando\n tus obras",
            'image_url' => 'img/bg-slide-2.jpg',
            'button_text' => 'Conocé más',
            'button_url' => 'nosotros.php',
        ],
        [
            'title' => "Conseguí\ntu mampara",
            'image_url' => 'img/bg-slide-3.jpg',
            'button_text' => 'Ver productos',
            'button_url' => 'productos.php?cat=artefactos',
        ],
    ];
}

function normalize_home_banner($banner)
{
    return [
        'title' => trim((string)($banner['title'] ?? '')),
        'image_url' => trim((string)($banner['image_url'] ?? '')),
        'button_text' => trim((string)($banner['button_text'] ?? '')),
        'button_url' => trim((string)($banner['button_url'] ?? '')),
    ];
}

function load_home_banners($use_defaults = true)
{
    $path = banners_json_path();
    if (!file_exists($path) || trim((string)file_get_contents($path)) === '') {
        return $use_defaults ? default_home_banners() : [];
    }

    $decoded = json_decode((string)file_get_contents($path), true);
    if (!is_array($decoded)) {
        return $use_defaults ? default_home_banners() : [];
    }

    $banners = [];
    foreach ($decoded as $banner) {
        if (!is_array($banner)) continue;
        $normalized = normalize_home_banner($banner);
        if ($normalized['title'] === '' && $normalized['image_url'] === '') continue;
        $banners[] = $normalized;
    }

    if (!$banners && $use_defaults) {
        return default_home_banners();
    }

    return $banners;
}

function save_home_banners($banners)
{
    $path = banners_json_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $clean = [];
    foreach ($banners as $banner) {
        if (!is_array($banner)) continue;
        $normalized = normalize_home_banner($banner);
        if ($normalized['title'] === '' && $normalized['image_url'] === '') continue;
        $clean[] = $normalized;
    }

    return file_put_contents($path, json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
}

if (!isset($PAGE)) {
    $PAGE = '';
}

$is_admin_page = strncmp($PAGE, 'admin-', 6) === 0;
if ($is_admin_page && $PAGE !== 'admin-login' && empty($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

$dynamic_cats = SelectQuery("categories")->SetIndex(-1)->Run();
if (is_array($dynamic_cats) && count($dynamic_cats) > 0) {
  $CATEGORY_MAP = [];
  foreach ($dynamic_cats as $c) {
    if (isset($c['category_key_word']) && isset($c['category_name'])) {
      $CATEGORY_MAP[$c['category_key_word']] = $c['category_name'];
    }
  }
} else {
  $CATEGORY_MAP = [
    'calefaccion'    => 'Calefacción',
    'piletas'        => 'Piletas',
    'artefactos'     => 'Artefactos y mamparas',
    'construccion'   => 'Construcción',
    'infraestructura'=> 'Infraestructura',
    'riego'          => 'Sistemas de riego',
  ];
}
