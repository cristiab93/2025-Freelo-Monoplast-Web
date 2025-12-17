<?php
$PAGE = 'admin-login';
include('../_general.php');

if (isset($_SESSION['admin_user']) && $_SESSION['admin_user'] !== '') {
  header('Location: index.php');
  exit;
}

$username = '';
$password = '';
$message  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = isset($_POST['username']) ? trim($_POST['username']) : '';
  $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

  if ($username !== '' && $password !== '') {
    if ($username === $admin_username && $password === $admin_password) {
      session_regenerate_id(true);
      $_SESSION['admin_user'] = $username;
      header('Location: index.php');
      exit;
    } else {
      $message = 'La información ingresada es incorrecta.';
    }
  } else {
    $message = 'Completá usuario y contraseña.';
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width">
  <title>Monoplast - Admin</title>
  <link rel="icon" type="image/png" href="../img/favicon.png">

  <link href="../css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="../js/jquery-3.5.1.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>

  <link href="css/login.css" rel="stylesheet">
  <style>
    body{background:#fff;}
    #formContent{
      border-radius:16px;
      box-shadow:0 10px 25px rgba(0,0,0,.08);
      border:1px solid #eef1f5;
    }
    .fadeIn.first img{max-height:54px}
    .btn-primary, .fadeIn.fourth{
      background:#0A0338; border:0;
    }
    .btn-primary:hover, .fadeIn.fourth:hover{
      background:#1b0a6b;
    }
    .brand-sub{font-size:12px;color:#8d8d8d;margin-top:6px}
    .alert-msg{color:#dc3545;margin:8px 0 0;font-size:14px}
  </style>
</head>
<body>
  <div class="wrapper fadeInDown">
    <div id="formContent">
      <div class="fadeIn first" style="margin-top:12px">
        <img style="margin:15px" src="../img/logo-color.svg" id="icon" alt="Monoplast Admin"/>
        <div class="brand-sub">Panel de administración</div>
      </div>

      <form method="post" action="login.php" autocomplete="off" novalidate>
        <input type="text" class="fadeIn second" id="username" name="username" placeholder="Usuario" value="<?= htmlspecialchars($username ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        <input type="password" class="fadeIn third" id="password" name="password" placeholder="Contraseña" required>
        <?php if ($message !== ''): ?>
          <div class="alert-msg"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <input style="cursor:pointer;" type="submit" class="fadeIn fourth" value="Ingresar">
      </form>

      <div style="height:10px"></div>
    </div>
  </div>
</body>
</html>
