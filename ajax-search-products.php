<?php
// Initialize the session
session_start();

require_once "config.php";

    $sql = "SELECT users.*, products.*, products.id as product_id FROM products INNER JOIN users ON products.proizvodac_id = users.id WHERE users.username LIKE :username AND products.naziv LIKE :product_naziv";
    if($stmt = $pdo->prepare($sql)){
    	
    	$stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
    	$stmt->bindParam(":product_naziv", $param_product_naziv, PDO::PARAM_STR);

    	if(trim($_GET['prodavac']) == '')
	    	$param_username = '%';
	    else
	    	$param_username = '%'.trim($_GET['prodavac']).'%';
    	
    	if(trim($_GET['proizvod']) == '')
	    	$param_product_naziv = '%';
	    else
	    	$param_product_naziv = '%'.trim($_GET['proizvod']).'%';


	    $stmt->execute();
	    
	    $products = $stmt->fetchAll();

	    if(count($products) != 0){
	        //echo '<pre>'; print_r($products); echo '</pre>';
	        foreach($products as $key => $product) {
?>

                <div class="product col-12 col-sm-3 text-center" data-p="<?php echo $product['id']; ?>">
                    <div class="row product-img">
                        <img src="<?php echo ROOT_URL.'/img/products/'.$product['slika'] ?>" alt="<?php echo htmlspecialchars($product['naziv']); ?>" title="<?php echo htmlspecialchars($product['naziv']); ?>">
                    </div>
                    <div class="row product-title">
                        <p><?php echo htmlspecialchars($product['naziv']); ?></p>
                    </div>
                    <div class="row product-price">
                        <label>Cijena: &nbsp;</label>
                        <span data-price="<?php echo $product['cijena']; ?>"><?php echo $product['cijena']; ?> HRK</span>
                    </div>
                    <div class="row product-prodavac">
                        <label>Prodavač: &nbsp;</label>
                        <span data-prodavac-id="<?php echo $product['proizvodac_id']; ?>" data-prodavac="<?php echo $product['username']; ?>"><?php echo $product['username']; ?></span>
                    </div>
                    <?php if($_SESSION['role'] != 1){ ?>
                        <?php 
                        $display = '';
                        if(isset($_SESSION["temp_products"]) && count($_SESSION["temp_products"]) != 0){
                            $key = array_search($product['id'], array_column($_SESSION["temp_products"], 'product_id'));
                            if(array_search($product['id'], array_column($_SESSION["temp_products"], 'product_id')) === 0 || !empty(array_search($product['id'], array_column($_SESSION["temp_products"], 'product_id'))))
                                $display = 'style="display: none"';
                        }
                        ?>
                        <div <?php echo $display; ?> class="row product-order">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <input class="form-control ml-auto" type="number" name="make_order_amount">
                            <button class="btn btn-primary add_on_order mr-auto">Dodaj</button>
                        </div>
                    <?php }else{ ?>
                        <div class="product-edit-delete">
                            <a href="<?php echo ROOT_URL; ?>edit-product.php?pid=<?php echo $product['product_id']; ?>" class="btn btn-primary product-edit" data-pid="<?php echo $product['product_id']; ?>">Uredi</a>
                            <button class="btn btn-danger product-remove" data-pid="<?php echo $product['product_id']; ?>">Obriši</button>
                        </div>
                    <?php } ?>
                </div>
<?php
	        }
	    }else{
	        echo '<div class="col-12 text-center">Trenutno nema proizvoda</div>';
	    }
	}

    unset($stmt);
    unset($pdo);
?>