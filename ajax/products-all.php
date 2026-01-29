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

$sel = SelectQuery("products");
$sel->SetIndex(-1);
$sel->Order("product_date", "DESC");
$sel->Limit(1000000);
$rows = $sel->Run();

$products = [];
$subcats_by_category = [];

foreach ($rows as $r) {
  $img = view_product_img($r["product_img"] ?? '', "uploaded_img/");

  $cat_text = $r["product_category"] ?? '';
  $key = '';
  if ($cat_text !== '') {
    $n = norm_txt($cat_text);
    if (isset($rev[$n])) $key = $rev[$n];
  }

  $subc_raw = $r["product_subcategory"] ?? '';
  // Get real name from map or use raw key if not found
  $display_subc = isset($subcat_map[$subc_raw]) ? $subcat_map[$subc_raw] : $subc_raw;
  $subc = title_case(clean_text($display_subc));

  $subname = isset($r["product_subname"]) ? trim((string)$r["product_subname"]) : "";
  if ($subname === "" && $subc_raw !== "") {
    // If subname is missing, use the real name of the subcategory
    $subname = $display_subc;
  }

  $products[] = [
    "id"            => (int)$r["product_id"],
    "eid"           => sed_encryption((string)$r["product_id"]),
    "name"          => title_case(clean_text($r["product_name"] ?? '')),
    "subname"       => truncate_text(title_case(clean_text($subname)), 80),
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

// Sort subcategories alphabetically for each category
foreach ($subcats_by_category as $key => &$list) {
  sort($list, SORT_STRING | SORT_FLAG_CASE);
}
unset($list);

echo json_encode([
  "success"            => 1,
  "products"           => $products,
  "categories"         => $CATEGORY_MAP,
  "subcats_by_category"=> $subcats_by_category
], JSON_UNESCAPED_UNICODE);
