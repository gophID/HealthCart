<?php
session_start();

$key = array_search($_POST['p_id'], array_column($_SESSION["temp_products"], 'product_id'));


$_SESSION["temp_products"][$key]['amount'] = $_POST['amount'];

echo json_encode($key);
?>