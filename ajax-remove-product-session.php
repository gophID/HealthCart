<?php
session_start();

$key = array_search($_POST['p_id'], array_column($_SESSION["temp_products"], 'product_id'));

unset($_SESSION["temp_products"][$key]);

$_SESSION["temp_products"] = array_values(array_filter($_SESSION["temp_products"]));

echo json_encode($key);
?>