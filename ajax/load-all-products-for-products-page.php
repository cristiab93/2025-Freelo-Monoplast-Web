<?php
$ORGANIGRAMA = TRUE;
include "_general.php";

$category_key = isset($_GET['category']) ? trim($_GET['category']) : '';
$subcat       = isset($_GET['subcat']) ? trim($_GET['subcat']) : '';
$search       = isset($_GET['q']) ? trim($_GET['q']) : '';
$page         = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage      = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 8;

global $CATEGORY_MAP;
$category_text = '';
if ($category_key !== '' && isset($CATEGORY_MAP[$category_key])) {
  $category_text = $CATEGORY_MAP[$category_key];
}

$selAll = SelectQuery("products");
$selAll->SetIndex(-1);

if ($category_text !== '') {
  $selAll->Condition("product_category =", "s", $category_text);
}
if ($subcat !== '') {
  $selAll->Condition("product_subcategory =", "s", $subcat);
}

$selAll->Order("product_date", "DESC");
$selAll->Limit(1000000);
$rowsAll = $selAll->Run();

$total = count($rowsAll);
$totalPages = (int)ceil($total / $perPage);
if ($totalPages < 1) $totalPages = 1;
if ($page > $totalPages) $page = $totalPages;

$offset = ($page - 1) * $perPage;
$rows = array_slice($rowsAll, $offset, $perPage);

$products = [];
$subcategories = [];

// Subcategory mapping from DB
$subcat_map = [];
$sub_res = SelectQuery("sub_categories")->Run();
if (is_array($sub_res)) {
  foreach ($sub_res as $sr) {
    if (isset($sr['sub_category_key_word']) && isset($sr['sub_category_name'])) {
      $subcat_map[$sr['sub_category_key_word']] = $sr['sub_category_name'];
    }
  }
}

foreach ($rows as $r) {
  $raw = isset($r["product_img"]) ? trim($r["product_img"]) : "";
  $isAbs = preg_match('/^https?:\/\//i', $raw) || (strlen($raw) && $raw[0] === "/");
  $img = $raw !== "" ? ($isAbs ? $raw : ("uploaded_img/" . ltrim($raw, "/"))) : "uploaded_img/ariston.png";

  $cat_text = $r["product_category"] ?? '';
  $cat_key  = array_search($cat_text, $CATEGORY_MAP, true);
  if ($cat_key === false) $cat_key = '';

  $subc_raw = $r["product_subcategory"] ?? '';
  $display_subc = isset($subcat_map[$subc_raw]) ? $subcat_map[$subc_raw] : $subc_raw;

  $subname = isset($r["product_subname"]) ? trim((string)$r["product_subname"]) : "";
  if ($subname === "" && $subc_raw !== "") {
    $subname = $display_subc;
  }

  $products[] = [
    "id"            => (int)$r["product_id"],
    "eid"           => sed_encryption((string)$r["product_id"]),
    "name"          => title_case(clean_text($r["product_name"] ?? '')),
    "subname"       => truncate_text(title_case(clean_text($subname)), 80),
    "category"      => $cat_key,
    "category_text" => $cat_text,
    "subcategory"   => title_case(clean_text($display_subc)),
    "image"         => $img,
    "date"          => $r["product_date"] ?? null
  ];
}

$seen = [];
foreach ($rowsAll as $r) {
  $s_raw = $r["product_subcategory"] ?? '';
  if ($s_raw !== '') {
    $display_s = isset($subcat_map[$s_raw]) ? $subcat_map[$s_raw] : $s_raw;
    $s = title_case(clean_text($display_s));
    if (!isset($seen[$s])) {
      $seen[$s] = true;
      $subcategories[] = $s;
    }
  }
}

// Sort subcategories alphabetically
sort($subcategories, SORT_STRING | SORT_FLAG_CASE);

echo json_encode([
  "success"        => 1,
  "category_used"  => $category_key,
  "category_text"  => $category_text,
  "page"           => $page,
  "per_page"       => $perPage,
  "total"          => $total,
  "total_pages"    => $totalPages,
  "has_prev"       => $page > 1 ? 1 : 0,
  "has_next"       => $page < $totalPages ? 1 : 0,
  "products"       => $products,
  "subcategories"  => $subcategories,
  "categories"     => $CATEGORY_MAP
], JSON_UNESCAPED_UNICODE);
