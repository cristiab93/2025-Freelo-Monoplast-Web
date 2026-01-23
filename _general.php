<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

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



if (!isset($PAGE)) {
    $PAGE = '';
}

$is_admin_page = strncmp($PAGE, 'admin-', 6) === 0;
if ($is_admin_page && $PAGE !== 'admin-login' && empty($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

$CATEGORY_MAP = [
  'calefaccion'    => 'Calefacción',
  'piletas'        => 'Piletas',
  'artefactos'     => 'Artefactos y mamparas',
  'construccion'   => 'Construcción',
  'infraestructura'=> 'Infraestructura',
  'riego'          => 'Sistemas de riego',
];
