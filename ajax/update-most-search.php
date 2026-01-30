<?php
$ORGANIGRAMA = TRUE;
include "../_general.php";

header('Content-Type: application/json');

if (!isset($_POST['id']) || !isset($_POST['status'])) {
  echo json_encode(['success' => 0, 'message' => 'Datos incompletos']);
  exit;
}

$id = (int)$_POST['id'];
$status = (int)$_POST['status'];

if ($status === 1) {
  // Get max order
  $max_res = SelectQuery('products')->SetIndex(-1)->Run(); // Not ideal but query.php doesn't seem to have MAX()
  $max_order = 0;
  foreach ($max_res as $row) {
    if ((int)$row['product_most_search_order'] > $max_order) {
      $max_order = (int)$row['product_most_search_order'];
    }
  }
  $new_order = $max_order + 1;

  $res = UpdateQuery('products')
    ->Value('product_most_search', 'i', 1)
    ->Value('product_most_search_order', 'i', $new_order)
    ->Condition('product_id =', 'i', $id)
    ->Run();
} else {
  $res = UpdateQuery('products')
    ->Value('product_most_search', 'i', 0)
    ->Value('product_most_search_order', 'i', 0)
    ->Condition('product_id =', 'i', $id)
    ->Run();
}

if ($res) {
  echo json_encode(['success' => 1, 'message' => 'Producto actualizado']);
} else {
  echo json_encode(['success' => 0, 'message' => 'No se pudo actualizar el producto']);
}
