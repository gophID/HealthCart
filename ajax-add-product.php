<?php
session_start();
// print_r($_POST);
// print_r($_POST['p_id']);
// print_r($_POST['o_id']);
// print_r($_POST['amount']);

$return_data = $_POST;
$_SESSION["temp_products"][] = [ 
	'product_id' => $_POST['p_id'],
	'name' => $_POST['name'],
	'amount' => $_POST['amount'],
	'price' => $_POST['price'],
	'prodavac_id' => $_POST['prodavac_id'],
	'prodavac_name' => $_POST['prodavac_name'],
];

// ["msg" => "Uspješno dodan proizvod u narudžbu",
// 		"order_id" => 2];

echo json_encode($return_data);
?>