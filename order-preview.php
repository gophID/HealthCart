<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION['role'] != 2){
    header("location: index.php");
    exit;
}

require_once "config.php";

$sql = "SELECT address FROM users WHERE id = :id";
if($stmt = $pdo->prepare($sql)){

    $stmt->bindParam(":id", $param_user_id, PDO::PARAM_STR);

    $param_user_id = $_SESSION["id"];

    $stmt->execute();

    $user = $stmt->fetch();
}



$order_insert_message = '';

if($_POST){
    $sql_order = "INSERT INTO orders(kupac_id, address) VALUES (:kupac_id, :address)";
    if($stmt = $pdo->prepare($sql_order)){

        $stmt->bindParam(":kupac_id", $param_kupac_id, PDO::PARAM_STR);
        $stmt->bindParam(":address", $param_address, PDO::PARAM_STR);

        $param_kupac_id = $_SESSION["id"];
        $param_address = $_POST['address'];

        $stmt->execute();

        $order_id = $pdo->lastInsertId();

        foreach($_POST['pid'] as $key => $value){
            $sql_order = "INSERT INTO order_products(order_id, product_id, amount, price) VALUES (:order_id, :product_id, :amount, :price)";
            if($stmt = $pdo->prepare($sql_order)){

                $stmt->bindParam(":order_id", $param_order_id, PDO::PARAM_STR);
                $stmt->bindParam(":product_id", $param_product_id, PDO::PARAM_STR);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt->bindParam(":price", $param_price, PDO::PARAM_STR);

                $param_order_id = $order_id;
                $param_product_id = $value;
                $param_amount = $_POST['amount'][$key];
                $param_price = $_POST['price'][$key];

                $stmt->execute();
            }
        }

        $order_insert_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">Uspješno dodana narudžba
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';

        $_SESSION["temp_products"] = [];
    }
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
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo ROOT_URL; ?>dashboard.php">Proizvodi</a>
                    </li>
                    <?php if($_SESSION['role'] == 1){ ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo ROOT_URL; ?>own-products.php">Moji proizvodi</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo ROOT_URL; ?>orders.php">Narudžbe</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="row page-content">
        <div class="col-12">

            <div class="row content-container">
                <div class="col-9 m-auto page-title">
                    <h4>Narudžba</h4>
                </div>
                <div class="col-11 m-auto">
                    <div class="product-update-message col-12 col-md-10 m-auto padding0"><?php echo $order_insert_message; ?></div>

                    <div class="row product-results edit-form">
                        <div class="col-12 col-md-10 col-lg-10 m-auto">
                            <?php 
                            if(isset($_SESSION["temp_products"]) && count($_SESSION["temp_products"]) != 0){
                            ?>
                            <!-- <pre><?php print_r($_SESSION); ?></pre> -->

                                <form id="order-preview-form" method="POST" action="<?php echo ROOT_URL; ?>order-preview.php">

                                    <table width="100%">
                                        <thead>
                                            <th>Naziv</th>
                                            <th>Prodavač</th>
                                            <th>Količina</th>
                                            <th>Cijena</th>
                                            <th>Ukupno</th>
                                            <th width="5%">Obriši</th>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $sum = 0;
                                            foreach($_SESSION["temp_products"] as $product){
                                            ?>
                                                <tr class="product-tr">
                                                    <td class="product-name">
                                                        <input type="hidden" name="pid[]" value="<?php echo $product['product_id'] ?>">
                                                        <span>
                                                            <?php echo $product['name']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="product-prodavac">
                                                        <input type="hidden" name="prodid[]" value="<?php echo $product['prodavac_id'] ?>">
                                                        <span data-prodavac-id="<?php echo $product['prodavac_id']; ?>">
                                                            <?php echo $product['prodavac_name']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="update-amount">
                                                            <input class="form-control" type="number" name="amount[]" step="1" min="1" value="<?php echo $product['amount']; ?>" required>
                                                            <button class="btn btn-primary update_on_order">Ažuriraj</button>
                                                        </div>
                                                    </td>
                                                    <td class="product-single-price">
                                                        <input type="hidden" name="price[]" value="<?php echo $product['price'] ?>">
                                                        <span data-price="<?php echo $product['price']; ?>">
                                                            <?php echo $product['price']; ?> HRK
                                                        </span>
                                                    </td>
                                                    <td class="product-total-price">
                                                        <span data-price="<?php echo $product['price']; ?>">
                                                            <?php
                                                                $sum += $product['price']*$product['amount'];
                                                                echo number_format((float)$product['price']*$product['amount'], 2, '.', ''); 
                                                            ?> HRK
                                                        </span>
                                                    </td>
                                                    <td class="product-remove">
                                                        <span data-pid="<?php echo $product['product_id']; ?>">
                                                            <i class="fas fa-times remove-product-from-session"></i>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="3"></td>
                                                <td class="sum-title"><strong>Ukupno</strong></td>
                                                <td class="sum-number"><strong><?php echo number_format((float)$sum, 2, '.', ''); ?> HRK</strong></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="form-group margin-top-20 text-right">
                                        <label for="address">Adresa</label>
                                        <input id="address" class="form-control" type="text" name="address" value="<?php echo $user['address']; ?>">
                                    </div>

                                    <div class="form-group margin-top-20 text-right">
                                        <button class="btn btn-primary store-order">Pošalji</button>
                                    </div>
                                </form>
                            <?php 
                            }else{
                            ?>
                                <p class="text-center" style="margin-bottom: 0;">Ne postoje proizvodi za narudžbu. Povratak na <a href="<?php echo ROOT_URL; ?>dashboard.php">proizvode.</a></p>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include 'footer.php';?>
</body>
</html>