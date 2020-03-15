<?php
require_once "config.php";
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.php'; ?>
</head>
<body class="dashboard">
    <div class="fixed-row">
        <div class="row logged-user text-right">
            <div class="col-12">
                <a href="<?php echo ROOT_URL; ?>edit-profil.php" class="logged-user-profil">
                    <i class="fas fa-user"></i> <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>
                </a>
                <a href="<?php echo ROOT_URL; ?>logout.php" class="logged-user-logout">
                    <i class="far fa-arrow-alt-circle-right"></i>
                </a>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="<?php echo ROOT_URL; ?>dashboard.php">
                <img class="logo-img" src="<?php echo ROOT_URL; ?>img/opg-logo.png">
            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="<?php echo ROOT_URL; ?>dashboard.php">Proizvodi</a>
                    </li>
                    <?php if($_SESSION['role'] == 1){ ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo ROOT_URL; ?>own-products.php">Moji proizvodi</a>
                        </li>
                    <?php } ?>
                    <?php if($_SESSION['role'] == 1){ ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo ROOT_URL; ?>orders-received.php">Narudžbe</a>
                        </li>
                    <?php }else{ ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo ROOT_URL; ?>orders.php">Narudžbe</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </div>

    <div class="row page-content">
        <div class="col-12">
            <div class="row filter-row">
                <div class="col-11 m-auto">
                    <i class="fas fa-search ml-auto"></i>

                    <div class="form-group">
                        <input type="text" class="form-control" id="search-proizvod" placeholder="Proizvod">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="search-prodavac" placeholder="Prodavač">
                    </div>
                </div>
            </div>

            <div class="row content-container">
                <div class="col-11 m-auto">

                    <div class="row product-results">
                        <div class="col-3 products-on-order <?php if(isset($_SESSION["temp_products"]) && count($_SESSION["temp_products"]) != 0) echo 'active'; ?>">
                                <div class="row products-order-list">
                                    <?php
                                    if(isset($_SESSION["temp_products"]) && count($_SESSION["temp_products"]) != 0){
                                        echo '<h3>Proizvodi za narudžbu</h3><hr>';
                                        foreach($_SESSION["temp_products"] as $key => $product){
                                    ?>
                                        <div class="row product-on-order" data-p="<?php echo $product['product_id']; ?>">
                                            <div class="col-10">
                                                <label>Naziv:</label> <?php echo $product['name']; ?>
                                            </div>
                                            <div class="col-2 text-right">
                                                <i class="fas fa-times remove-product-from-session"></i>
                                            </div>
                                            <div class="col-12 product-order-price">
                                                <label>Cijena:</label>
                                                <span data-single-price="<?php echo $product['price']; ?>" class="price-for-update">
                                                    <?php echo $product['price']*$product['amount']; ?>
                                                </span> HRK
                                            </div>
                                            <div class="col-12">
                                                <div class="row">
                                                    <label>Količina:</label>
                                                </div>
                                                <div class="row">
                                                    <input class="form-control" type="number" name="" step="1" min="1" value="<?php echo $product['amount']; ?>">
                                                    <button class="btn btn-primary update_on_order m-auto">Ažuriraj</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="row confirm_order_row text-center">
                                    <a class="btn btn-primary confirm_order m-auto" href="<?php echo ROOT_URL; ?>order-preview.php">Potvrdi narudžbu</a>
                                </div>
                        </div>

                        <div class="col-12 padding0 products-for-order <?php if(isset($_SESSION["temp_products"]) && count($_SESSION["temp_products"]) != 0) echo 'active-order-preview'; ?>">
                            <div class="row">
                                <?php
                                    $sql = "SELECT users.*, products.*, products.id as product_id FROM products INNER JOIN users ON products.proizvodac_id = users.id";
                                    $stmt = $pdo->prepare($sql);
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
                                                <?php } ?>
                                            </div>
                                <?php
                                        }
                                    }else{
                                        echo '<div class="col-12 text-center">Trenutno nema proizvoda</div>';
                                    }

                                    unset($stmt);
                                    unset($pdo);
                                ?>
                            </div>
                        </div>



                    </div>


                </div>

            </div>
        </div>
    </div>

    <?php include 'footer.php';?>
</body>
</html>