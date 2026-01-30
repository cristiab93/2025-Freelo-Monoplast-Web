<?php
$ORGANIGRAMA = TRUE;
include "../_general.php";

header('Content-Type: application/json');

if (!isset($_POST['id']) || !isset($_POST['direction'])) {
  echo json_encode(['success' => 0, 'message' => 'Datos incompletos']);
  exit;
}

$id = (int)$_POST['id'];
$direction = $_POST['direction']; // 'up' or 'down'

// Get current product
$current = SelectQuery('products')->Condition('product_id =', 'i', $id)->Limit(1)->Run();
if (!$current) {
  echo json_encode(['success' => 0, 'message' => 'Producto no encontrado']);
  exit;
}
$current = array_values($current)[0];
$current_order = (int)$current['product_most_search_order'];

// Get all featured products sorted by order
$all = SelectQuery('products')
  ->Condition('product_most_search =', 'i', 1)
  ->Order('product_most_search_order', 'ASC')
  ->SetIndex(-1)
  ->Run();

$target_id = null;
$target_order = null;

for ($i = 0; $i < count($all); $i++) {
  if ((int)$all[$i]['product_id'] === $id) {
    if ($direction === 'up' && $i > 0) {
      $target_id = (int)$all[$i-1]['product_id'];
      $target_order = (int)$all[$i-1]['product_most_search_order'];
    } elseif ($direction === 'down' && $i < count($all) - 1) {
      $target_id = (int)$all[$i+1]['product_id'];
      $target_order = (int)$all[$i+1]['product_most_search_order'];
    }
    break;
  }
}

if ($target_id !== null) {
  // Swap orders
  UpdateQuery('products')
    ->Value('product_most_search_order', 'i', $target_order)
    ->Condition('product_id =', 'i', $id)
    ->Run();

  UpdateQuery('products')
    ->Value('product_most_search_order', 'i', $current_order)
    ->Condition('product_id =', 'i', $target_id)
    ->Run();

  echo json_encode(['success' => 1]);
} else {
  echo json_encode(['success' => 0, 'message' => 'No es posible mover en esa dirección']);
}
