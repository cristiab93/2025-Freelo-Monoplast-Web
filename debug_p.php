<?php
include "_general.php";
$query = "SELECT * FROM p LIMIT 1";
$result = mysqli_query($conn, $query);
if (!$result) { die(mysqli_error($conn)); }
$fields = mysqli_fetch_fields($result);
foreach ($fields as $field) {
    echo $field->name . "\n";
}
print_r(mysqli_fetch_assoc($result));
