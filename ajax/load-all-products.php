<?php
$ORGANIGRAMA = TRUE;
include "_general.php";

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$subcategory = isset($_GET['subcategory']) ? trim($_GET['subcategory']) : '';
$order = isset($_GET['order']) ? trim($_GET['order']) : 'recent';

if ($limit < 1) $limit = 12;
if ($limit > 60) $limit = 60;

$sel = SelectQuery('products');

if ($q !== '') {
  $like = '%'.$q.'%';
  $sel->Condition("CONCAT(product_name,' ',product_description) LIKE", 's', $like);
}
if ($category !== '') {
  $sel->Condition("product_category =", 's', $category);
}
if ($subcategory !== '') {
  $sel->Condition("product_subcategory =", 's', $subcategory);
}

switch ($order) {
  case 'az':
    $sel->Order('product_name', 'ASC');
    break;
  case 'za':
    $sel->Order('product_name', 'DESC');
    break;
  case 'oldest':
    $sel->Order('product_date', 'ASC');
    break;
  default:
    $sel->Order('product_date', 'DESC');
}

$rows = $sel->Limit($limit)->Run();

$data = [];
foreach ($rows as $r) {
  $raw = isset($r['product_img']) ? trim($r['product_img']) : '';
  $isAbs = preg_match('/^https?:\/\//i', $raw) || (strlen($raw) && $raw[0] === '/');
  $img = $raw !== '' ? ($isAbs ? $raw : ('uploaded_img/' . ltrim($raw, '/'))) : 'img/placeholder.png';

  $subname = isset($r['product_subname']) ? $r['product_subname'] : '';
  if ($subname === '' && isset($r['product_subcategory'])) $subname = $r['product_subcategory'];

  $data[] = [
    'id' => (int)$r['product_id'],
    'enc_id' => sed_encryption($r['product_id']),
    'name' => $r['product_name'],
    'subname' => $subname,
    'category' => $r['product_category'],
    'subcategory' => $r['product_subcategory'],
    'image' => $img,
    'description' => $r['product_description'],
    'date' => $r['product_date']
  ];
}

echo json_encode([
  'success' => 1,
  'params' => ['limit'=>$limit,'q'=>$q,'category'=>$category,'subcategory'=>$subcategory,'order'=>$order],
  'data' => $data
], JSON_UNESCAPED_UNICODE);
