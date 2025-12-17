<?php

$PAGE = 'admin-logout';
include('../_general.php');

$_SESSION['admin_user'] = null;

header("location: login.php");

?>