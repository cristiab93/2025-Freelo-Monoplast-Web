<?php

function write($content)
{
    echo $content . "<br>";
}

function print_line($content)
{
    echo $content . "\n";
}

function print_array($array)
{
    echo "<pre>";
    var_dump($array);
    echo "</pre>";
}

function CalculateDateDiff($start, $end)
{
    $date1 = strtotime($start);
    $date2 = strtotime($end);
    $diff = abs($date2 - $date1);
    $days = $diff / (24*60*60);
    return $days;
}

function Script($file)
{
    echo '<script src="'.$file.'?v='.getdate()[0].'"></script>';
}

function CSS($file)
{
    echo '<link href="'.$file.'?v='.getdate()[0].'" rel="stylesheet">';
}

function salir_mant($text = "No comment") 
{
    $random = rand(1, 999999);
    error_log("SALIR MANT:" . $text . "(ID: ". $random . ")" . "(DNI: ". $_SESSION["user_id"] . ")");
    salir("¡Ups! Parece que algo salió mal. Intentá nuevamente más tarde." . "(ID: ". $random . ") - Reason: " . $text);
}

function redirect($page)
{
  header("location: $page");
  die();
}


function salir($mensaje = NULL) 
{
  foreach ($_SESSION as $i => $v) 
  {
    unset($_SESSION[$i]);
  }
  if ($mensaje) die("<big><b>$mensaje</b></big>"); 
  else
  {
    if(file_exists("expired.php")) redirect("expired.php");
    else redirect("../expired.php");
  }
}

function IsExtern()
{
   return $_SESSION["user_id"] == 0;
}

function UploadImage($file_input, $n)
{
    $dir = "img/uploaded_images/test.jpg";
    if($file_input["tmp_name"] != "")
    {
        $compressedImage = compressImage($file_input["tmp_name"], $dir, 800, 80);
        if($compressedImage)
        {
            $file = base64_encode(file_get_contents($compressedImage));
            
            UpdateQuery("users")
            ->Value("user_pic", "s", $file)
            ->Condition("user_id =", "i", $n)
            ->Run();
        
            if (file_exists($dir)) unlink($dir);
        }
        else
        {
            $file = "";
        } 
    }
    else
    {
      $file = "";
    }

    $_SESSION["file"] = $file;
    return true;
}

function UploadImageBanner($file_input)
{
    $dir = "img/uploaded_images/".time().".jpg";
    if($file_input["tmp_name"] != "")
    {
        $compressedImage = compressImage($file_input["tmp_name"], $dir, 2000, 100);
        if($compressedImage)
        {
            $file = base64_encode(file_get_contents($compressedImage));
            
            InsertQuery("home_banners")
            ->Value("banner_pic", "s",$dir)
            ->Value("banner_pos", "i", time())
            ->Run();
        
            //if (file_exists($dir)) unlink($dir);
        }
        else
        {
            $file = "";
        } 
    }
    else
    {
      $file = "";
    }

    $_SESSION["file"] = $file;
    return true;
}

function compressImage($source, $destination, $image_size, $quality) 
{
  list($ancho, $altura, $type, $attr) = getimagesize($source);

  switch ($type) {
      case 1: {
          $image = false;
        break;
      }
      case 2: {
          $image = imagecreatefromjpeg($source);
        break;
      }
      case 3: {
          $image = imagecreatefrompng($source);
          break;
      }
      default: {
          $image = false;
        break;
      }
  }
  if ($image) {
    $image = resize_image($image, $ancho, $altura, $image_size, $image_size);
    imagejpeg($image, $destination, $quality);
  }

  if($image)
  {
      return $destination;
  }
  else
  {
      return "";
  }
}

function resize_image($src, $width, $height, $w, $h) 
{
  $r = $width / $height;
  if ($w/$h > $r) {
      $newwidth = $h*$r;
      $newheight = $h;
  } else {
      $newheight = $w/$r;
      $newwidth = $w;
  }
  $dst = imagecreatetruecolor($newwidth, $newheight);
  imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
  return $dst;
}

function validateDate($date, $format = 'Y-n-j')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}

function getPreviousSemester($str) {
  $year = intval(substr($str, 0, 4));
  $letter = substr($str, 4);

  $prevYear = $year;
  $prevLetter = "";

  if ($letter === "A") {
      $prevYear = $year - 1;
      $prevLetter = "D";
  } else {
      $prevLetter = chr(ord($letter) - 1);
  }

  return $prevYear . $prevLetter;
}

function transformDateToDayAndMonth($dateString) {
  $date = DateTime::createFromFormat('d/m/Y', $dateString);
  $monthNames = [
      1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
      7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
  ];

  $day = $date->format('j');
  $month = $date->format('m');

  $result = $day . ' de ' . $monthNames[(int)$month];
  return $result;
}

function view_date($dateString) {
  if (!$dateString || $dateString == '0000-00-00') return '—';
  
  $date = DateTime::createFromFormat('Y-m-d', $dateString);
  if (!$date) return htmlspecialchars($dateString);

  $monthNames = [
      1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR', 5 => 'MAY', 6 => 'JUN',
      7 => 'JUL', 8 => 'AGO', 9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC'
  ];

  $day = $date->format('d');
  $month = (int)$date->format('m');
  $year = $date->format('Y');

  return $day . ' ' . $monthNames[$month] . ' ' . $year;
}


/**
 * Convert text to title case with Spanish articles/prepositions in lowercase
 * Example: "PIAZZA DE PORCELANA" -> "Piazza de Porcelana"
 */
function title_case($text) {
  if (empty($text)) return $text;
  
  // Articles and prepositions to keep lowercase (unless first or last word)
  $lowercase_words = [
    'de', 'del', 'la', 'el', 'los', 'las', 'un', 'una', 'unos', 'unas',
    'y', 'e', 'o', 'u', 'con', 'sin', 'para', 'por', 'a', 'en', 'sobre',
    'al', 'desde', 'hasta', 'entre', 'bajo', 'ante', 'contra', 'hacia',
    'que', 'u'
  ];
  
  // Convert to lowercase first
  $text = mb_strtolower($text, 'UTF-8');
  
  // Split into words by any whitespace
  $words = preg_split('/(\s+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
  
  $result = [];
  $words_only_indices = [];
  
  // Identify actual words (not whitespace)
  foreach ($words as $i => $word) {
    if (!preg_match('/^\s+$/', $word) && $word !== '') {
      $words_only_indices[] = $i;
    }
  }
  
  $num_words = count($words_only_indices);
  
  foreach ($words as $i => $word) {
    // Preserve whitespace or empty strings
    if (preg_match('/^\s+$/', $word) || $word === '') {
      $result[] = $word;
      continue;
    }
    
    $word_lower = mb_strtolower($word, 'UTF-8');
    $word_idx = array_search($i, $words_only_indices);
    
    // Capitalize if it's the first word, the last word, or NOT in the lowercase list
    if ($word_idx === 0 || $word_idx === $num_words - 1 || !in_array($word_lower, $lowercase_words)) {
      // Capitalize first letter, handle multibyte
      $first_char = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8');
      $rest = mb_substr($word, 1, null, 'UTF-8');
      $result[] = $first_char . $rest;
    } else {
      // Keep lowercase
      $result[] = $word_lower;
    }
  }
  
  return implode('', $result);
}

/**
 * Clean text: remove HTML tags and decode entities
 */
function clean_text($text) {
  if (empty($text)) return $text;
  
  // Replace <br> with newlines to preserve them before stripping tags
  $text = preg_replace('/<br\s*\/?>/i', "\n", $text);

  // Decode entities (handling double encoding if necessary)
  $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  
  // Remove tags
  $text = strip_tags($text);
  
  // Final trim
  return trim($text);
}

/**
 * Capitalize only the very first letter of a string
 */
function capitalize_first($text) {
  if (empty($text)) return $text;
  
  // Find the first alphabetic character and capitalize it
  // This handles cases like "¡hola" -> "¡Hola"
  $len = mb_strlen($text, 'UTF-8');
  for ($i = 0; $i < $len; $i++) {
    $char = mb_substr($text, $i, 1, 'UTF-8');
    if (preg_match('/[a-z]/u', mb_strtolower($char, 'UTF-8'))) {
      $before = mb_substr($text, 0, $i, 'UTF-8');
      $upper = mb_strtoupper($char, 'UTF-8');
      $after = mb_substr($text, $i + 1, null, 'UTF-8');
      return $before . $upper . $after;
    }
  }
  
  return $text;
}

/**
 * Truncate text to a maximum length and append ellipsis if needed
 */
function truncate_text($text, $max_chars = 80) {
  if (empty($text)) return $text;
  
  $cleaned = clean_text($text);
  $cleaned = capitalize_first(trim($cleaned));
  
  if (mb_strlen($cleaned, 'UTF-8') <= $max_chars) return $cleaned;
  
  return mb_substr($cleaned, 0, $max_chars, 'UTF-8') . '...';
}









/**
 * Get product image path/URL.
 * If raw is empty, returns default ariston.png.
 * If raw is already an absolute URL or starts with /, returns it as is.
 * Otherwise, prefixes it with the specified prefix (default uploaded_img/).
 */
function view_product_img($raw, $prefix = 'uploaded_img/') {
  $raw = trim((string)$raw);
  if ($raw === "") return $prefix . "ariston.png";
  
  $isAbs = preg_match('/^https?:\/\//i', $raw) || $raw[0] === "/";
  if ($isAbs) return $raw;
  
  return $prefix . ltrim($raw, "/");
}
