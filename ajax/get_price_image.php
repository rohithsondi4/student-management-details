<?php
// filepath: c:\xampp\htdocs\pkptasks\ajax\get_price_image.php
require_once "../config/config.php";

$f_id  = mysqli_real_escape_string($conn, $_POST['f_id']);
$c_id  = mysqli_real_escape_string($conn, $_POST['c_id']);
$s_id  = mysqli_real_escape_string($conn, $_POST['s_id']);
$ta_id = mysqli_real_escape_string($conn, $_POST['ta_id']);
$t_id  = mysqli_real_escape_string($conn, $_POST['t_id']);

$query = "
    SELECT price, img_url
    FROM pricing
    WHERE f_id = '$f_id'
      AND c_id = '$c_id'
      AND s_id = '$s_id'
      AND ta_id = '$ta_id'
      AND t_id = '$t_id'
    LIMIT 1
";

$result = mysqli_query($conn, $query) or die(mysqli_error($conn));
$row = mysqli_fetch_assoc($result);

header('Content-Type: application/json');

if ($row) {
    echo json_encode(array(
        'status' => 1,
        'price' => $row['price'],
        'img_url' => $row['img_url']
    ));
} else {
    echo json_encode(array(
        'status' => 0
    ));
}
?>