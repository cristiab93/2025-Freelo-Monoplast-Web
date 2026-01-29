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

$sel = SelectQuery('products')->SetIndex(-1);

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

// Subcategory mapping from DB
$subcat_map = [];
$sub_res = SelectQuery("sub_categories")->SetIndex(-1)->Run();
if (is_array($sub_res)) {
  foreach ($sub_res as $sr) {
    if (isset($sr['sub_category_key_word']) && isset($sr['sub_category_name'])) {
      $subcat_map[$sr['sub_category_key_word']] = $sr['sub_category_name'];
    }
  }
}

$data = [];
foreach ($rows as $r) {
  $raw = isset($r['product_img']) ? trim($r['product_img']) : '';
  $isAbs = preg_match('/^https?:\/\//i', $raw) || (strlen($raw) && $raw[0] === '/');
  $img = $raw !== '' ? ($isAbs ? $raw : ('uploaded_img/' . ltrim($raw, '/'))) : 'img/placeholder.png';

  $subname_raw = isset($r['product_subname']) ? $r['product_subname'] : '';
  $subc_raw = $r['product_subcategory'] ?? '';
  $display_subc = isset($subcat_map[$subc_raw]) ? $subcat_map[$subc_raw] : $subc_raw;

  if ($subname_raw === '' && $subc_raw !== '') {
    $subname_raw = $display_subc;
  }

  $data[] = [
    'id' => (int)$r['product_id'],
    'enc_id' => sed_encryption($r['product_id']),
    'name' => title_case(clean_text($r['product_name'])),
    'subname' => truncate_text(title_case(clean_text($subname_raw)), 80),
    'category' => $r['product_category'],
    'subcategory' => title_case(clean_text($display_subc)),
    'image' => $img,
    'description' => html_entity_decode($r['product_description'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
    'date' => $r['product_date']
  ];
}

echo json_encode([
  'success' => 1,
  'params' => ['limit'=>$limit,'q'=>$q,'category'=>$category,'subcategory'=>$subcategory,'order'=>$order],
  'data' => $data
], JSON_UNESCAPED_UNICODE);
