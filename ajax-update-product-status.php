<?php
// Initialize the session
session_start();

require_once "config.php";

$sql_order = "UPDATE order_products SET status = :status, comment = :comment WHERE order_id = :order_id AND product_id = :product_id";
if($stmt = $pdo->prepare($sql_order)){

    $stmt->bindParam(":order_id", $param_order_id, PDO::PARAM_STR);
    $stmt->bindParam(":product_id", $param_product_id, PDO::PARAM_STR);
    $stmt->bindParam(":status", $param_status, PDO::PARAM_STR);
    $stmt->bindParam(":comment", $param_comment, PDO::PARAM_STR);

    $param_order_id = $_POST['oid'];
    $param_product_id = $_POST['pid'];
    $param_status = $_POST['status'];
    $param_comment = $_POST['comment'];

    $stmt->execute();


    //Update order status if
    $sql_order = "SELECT id, product_id, status FROM order_products WHERE order_id = :id";
	if($stmt = $pdo->prepare($sql_order)){

	    $stmt->bindParam(":id", $param_order_id, PDO::PARAM_STR);

	    $param_order_id = $_POST['oid'];

	    $stmt->execute();

	    $products = $stmt->fetchAll();

	    //if status 11 does not exist check if there is 0 else set to 1
	    if(!(array_search(11, array_column($products, 'status')) === 0 || !empty(array_search(11, array_column($products, 'status'))))){
	    	echo 'ne postoji 11';
	    	$order_status_update = 0;
	    	if(!(array_search(0, array_column($products, 'status')) === 0 || !empty(array_search(0, array_column($products, 'status'))))){
		    	echo 'ne postoji 0';
		    	$order_status_update = 1;
		    }

		    $sql_order = "UPDATE orders SET status = :status WHERE id = :order_id";
			if($stmt = $pdo->prepare($sql_order)){

			    $stmt->bindParam(":order_id", $param_order_id, PDO::PARAM_STR);
			    $stmt->bindParam(":status", $param_status, PDO::PARAM_STR);

			    $param_order_id = $_POST['oid'];
			    $param_status = $order_status_update;

			    $stmt->execute();
			}

	    }else{
	    	echo 'postoji 11';
	    }


	    print_r($products);
	}
}

?>