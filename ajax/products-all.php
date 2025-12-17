<?php
$ORGANIGRAMA = TRUE;
include "_general.php";

global $CATEGORY_MAP;

function norm_txt($s) {
  $s = (string)$s;
  $s = mb_strtolower($s, 'UTF-8');
  $s = iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$s);
  $s = preg_replace('/[^a-z0-9\- ]/',' ', $s);
  $s = preg_replace('/\s+/',' ', trim($s));
  return $s;
}

$rev = [];
foreach ($CATEGORY_MAP as $k => $v) {
  $rev[norm_txt($v)] = $k;
  $rev[norm_txt($k)] = $k;
}

$sel = SelectQuery("products");
$sel->SetIndex(-1);
$sel->Order("product_date", "DESC");
$sel->Limit(1000000);
$rows = $sel->Run();

$products = [];
$subcats_by_category = [];

foreach ($rows as $r) {
  $raw = isset($r["product_img"]) ? trim((string)$r["product_img"]) : "";
  $isAbs = preg_match('/^https?:\/\//i', $raw) || (strlen($raw) && $raw[0] === "/");
  $img = $raw !== "" ? ($isAbs ? $raw : ("uploaded_img/" . ltrim($raw, "/"))) : "uploaded_img/ariston.png";

  $cat_text = $r["product_category"] ?? '';
  $key = '';
  if ($cat_text !== '') {
    $n = norm_txt($cat_text);
    if (isset($rev[$n])) $key = $rev[$n];
  }

  $subc = $r["product_subcategory"] ?? '';

  $products[] = [
    "id"            => (int)$r["product_id"],
    "eid"           => sed_encryption((string)$r["product_id"]),
    "name"          => $r["product_name"] ?? '',
    "subname"       => $r["product_subname"] ?? '',
    "category"      => $key,
    "category_text" => $cat_text,
    "subcategory"   => $subc,
    "image"         => $img,
    "date"          => $r["product_date"] ?? null
  ];

  if ($key !== '') {
    if (!isset($subcats_by_category[$key])) $subcats_by_category[$key] = [];
    if ($subc !== '' && !in_array($subc, $subcats_by_category[$key], true)) {
      $subcats_by_category[$key][] = $subc;
    }
  }
}

echo json_encode([
  "success"            => 1,
  "products"           => $products,
  "categories"         => $CATEGORY_MAP,
  "subcats_by_category"=> $subcats_by_category
], JSON_UNESCAPED_UNICODE);
