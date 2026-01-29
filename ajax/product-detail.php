<?php
$ORGANIGRAMA = TRUE;
include "_general.php";

$eid = isset($_GET["id"]) ? trim($_GET["id"]) : "";
$eid = urldecode($eid);
$eid = str_replace(' ', '+', $eid);

$decrypted = $eid !== "" ? sed_decryption($eid) : "";
$product_id = (ctype_digit((string)$decrypted) && (int)$decrypted > 0) ? (int)$decrypted : 0;

$debug_mode = isset($_GET['debug']) ? (int)$_GET['debug'] : 0;
$debug = [];
if ($debug_mode) {
  $debug['eid_raw'] = isset($_GET["id"]) ? $_GET["id"] : '';
  $debug['eid_norm'] = $eid;
  $debug['decrypted'] = $decrypted;
  $debug['product_id_int'] = $product_id;
}

if ($product_id <= 0) {
  echo json_encode([
    "success" => 0,
    "error" => "BAD_OR_EMPTY_ID",
    "product" => null,
    "related" => [],
    "debug" => $debug_mode ? $debug : null
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$sel = SelectQuery("products");
$sel->Condition("product_id =", "i", $product_id);
$rows = $sel->SetIndex(-1)->Limit(1)->Run();

// Mappings from DB
global $CATEGORY_MAP;
$cat_map = $CATEGORY_MAP;

$subcat_map = [];
$sub_res = SelectQuery("sub_categories")->Run();
if (is_array($sub_res)) {
  foreach ($sub_res as $sr) {
    if (isset($sr['sub_category_key_word']) && isset($sr['sub_category_name'])) {
      $subcat_map[$sr['sub_category_key_word']] = $sr['sub_category_name'];
    }
  }
}

if (!count($rows)) {
  echo json_encode([
    "success" => 0,
    "error" => "NOT_FOUND",
    "product" => null,
    "related" => [],
    "debug" => $debug_mode ? $debug : null
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

$r = $rows[0];
$raw = isset($r["product_img"]) ? trim($r["product_img"]) : "";
$isAbs = preg_match('/^https?:\/\//i', $raw) || (strlen($raw) && $raw[0] === "/");
$img = $raw !== "" ? ($isAbs ? $raw : ("uploaded_img/" . ltrim($raw, "/"))) : "uploaded_img/ariston.png";

$subname_raw = isset($r["product_subname"]) ? trim((string)$r["product_subname"]) : "";
$subc_raw = $r["product_subcategory"] ?? '';
$display_subc = isset($subcat_map[$subc_raw]) ? $subcat_map[$subc_raw] : $subc_raw;

if ($subname_raw === "" && $subc_raw !== "") {
  $subname_raw = $display_subc;
}

$cat_raw = $r["product_category"] ?? '';
$display_cat = $cat_raw;
$cat_key = $cat_raw; // Fallback
foreach($cat_map as $ck => $cv) {
  if (mb_strtolower($cat_raw) === mb_strtolower($ck) || mb_strtolower($cat_raw) === mb_strtolower($cv)) {
    $display_cat = $cv;
    $cat_key = $ck;
    break;
  }
}

$product = [
  "id" => (int)$r["product_id"],
  "eid" => sed_encryption((string)$r["product_id"]),
  "name" => title_case(clean_text($r["product_name"])),
  "subname" => title_case(clean_text($subname_raw)),
  "category" => title_case(clean_text($display_cat)),
  "category_key" => $cat_key,
  "subcategory" => title_case(clean_text($display_subc)),
  "subcategory_key" => $subc_raw,
  "image" => $img,
  "description" => clean_text($r["product_description"]),
  "date" => $r["product_date"]
];

$related = [];
$seen = [];
$take = 4;

if (!empty($product["category"])) {
  $q1 = SelectQuery("products");
  $q1->Condition("product_category =", "s", $product["category"]);
  $q1->Condition("product_id <>", "i", (int)$product["id"]);
  $q1->Order("product_date", "DESC");
  $rows1 = $q1->SetIndex(-1)->Limit($take)->Run();
  foreach ($rows1 as $rr) {
    if (isset($seen[$rr["product_id"]])) continue;
    $seen[$rr["product_id"]] = 1;
    $raw2 = isset($rr["product_img"]) ? trim($rr["product_img"]) : "";
    $isAbs2 = preg_match('/^https?:\/\//i', $raw2) || (strlen($raw2) && $raw2[0] === "/");
    $img2 = $raw2 !== "" ? ($isAbs2 ? $raw2 : ("uploaded_img/" . ltrim($raw2, "/"))) : "uploaded_img/ariston.png";
    $subc_raw2 = $rr["product_subcategory"] ?? '';
    $display_subc2 = isset($subcat_map[$subc_raw2]) ? $subcat_map[$subc_raw2] : $subc_raw2;
    $sn_raw2 = isset($rr["product_subname"]) ? trim((string)$rr["product_subname"]) : "";
    if ($sn_raw2 === "" && $subc_raw2 !== "") {
      $sn_raw2 = $display_subc2;
    }

    $related[] = [
      "id" => (int)$rr["product_id"],
      "eid" => sed_encryption((string)$rr["product_id"]),
      "name" => title_case(clean_text($rr["product_name"])),
      "subname" => truncate_text(title_case(clean_text($sn_raw2)), 80),
      "category" => $rr["product_category"],
      "subcategory" => title_case(clean_text($display_subc2)),
      "image" => $img2
    ];
    if (count($related) >= $take) break;
  }
}

if (count($related) < $take && !empty($product["subcategory"])) {
  $left = $take - count($related);
  $q2 = SelectQuery("products");
  $q2->Condition("product_subcategory =", "s", $product["subcategory"]);
  $q2->Condition("product_id <>", "i", (int)$product["id"]);
  $q2->Order("product_date", "DESC");
  $rows2 = $q2->SetIndex(-1)->Limit($left)->Run();
  foreach ($rows2 as $rr) {
    if (isset($seen[$rr["product_id"]])) continue;
    $seen[$rr["product_id"]] = 1;
    $raw2 = isset($rr["product_img"]) ? trim($rr["product_img"]) : "";
    $isAbs2 = preg_match('/^https?:\/\//i', $raw2) || (strlen($raw2) && $raw2[0] === "/");
    $img2 = $raw2 !== "" ? ($isAbs2 ? $raw2 : ("uploaded_img/" . ltrim($raw2, "/"))) : "uploaded_img/ariston.png";
    $subc_raw2 = $rr["product_subcategory"] ?? '';
    $display_subc2 = isset($subcat_map[$subc_raw2]) ? $subcat_map[$subc_raw2] : $subc_raw2;
    $sn_raw2 = isset($rr["product_subname"]) ? trim((string)$rr["product_subname"]) : "";
    if ($sn_raw2 === "" && $subc_raw2 !== "") {
      $sn_raw2 = $display_subc2;
    }

    $related[] = [
      "id" => (int)$rr["product_id"],
      "eid" => sed_encryption((string)$rr["product_id"]),
      "name" => title_case(clean_text($rr["product_name"])),
      "subname" => truncate_text(title_case(clean_text($sn_raw2)), 80),
      "category" => $rr["product_category"],
      "subcategory" => title_case(clean_text($display_subc2)),
      "image" => $img2
    ];
    if (count($related) >= $take) break;
  }
}

if (count($related) < $take) {
  $left = $take - count($related);
  $q3 = SelectQuery("products");
  $q3->Condition("product_id <>", "i", (int)$product["id"]);
  $q3->Order("product_date", "DESC");
  $rows3 = $q3->SetIndex(-1)->Limit($left)->Run();
  foreach ($rows3 as $rr) {
    if (isset($seen[$rr["product_id"]])) continue;
    $seen[$rr["product_id"]] = 1;
    $raw2 = isset($rr["product_img"]) ? trim($rr["product_img"]) : "";
    $isAbs2 = preg_match('/^https?:\/\//i', $raw2) || (strlen($raw2) && $raw2[0] === "/");
    $img2 = $raw2 !== "" ? ($isAbs2 ? $raw2 : ("uploaded_img/" . ltrim($raw2, "/"))) : "uploaded_img/ariston.png";
    $subc_raw2 = $rr["product_subcategory"] ?? '';
    $display_subc2 = isset($subcat_map[$subc_raw2]) ? $subcat_map[$subc_raw2] : $subc_raw2;
    $sn_raw2 = isset($rr["product_subname"]) ? trim((string)$rr["product_subname"]) : "";
    if ($sn_raw2 === "" && $subc_raw2 !== "") {
      $sn_raw2 = $display_subc2;
    }

    $related[] = [
      "id" => (int)$rr["product_id"],
      "eid" => sed_encryption((string)$rr["product_id"]),
      "name" => title_case(clean_text($rr["product_name"])),
      "subname" => truncate_text(title_case(clean_text($sn_raw2)), 80),
      "category" => $rr["product_category"],
      "subcategory" => title_case(clean_text($display_subc2)),
      "image" => $img2
    ];
    if (count($related) >= $take) break;
  }
}

echo json_encode([
  "success" => 1,
  "product" => $product,
  "related" => $related,
  "debug" => $debug_mode ? $debug : null
], JSON_UNESCAPED_UNICODE);
