<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION['role'] != 2){
    header("location: index.php");
    exit;
}

require_once "config.php";

if($_GET && $_GET['oid']){

    function getOrderProducts($pdo){
        $sql_order_products = "SELECT order_products.*, products.naziv as name, users.username as prodavac_name, users.id as prodavac_id FROM order_products INNER JOIN products ON order_products.product_id = products.id INNER JOIN users ON products.proizvodac_id = users.id WHERE order_id = :id";
        if($stmt = $pdo->prepare($sql_order_products)){

            $stmt->bindParam(":id", $param_order_id, PDO::PARAM_STR);

            $param_order_id = $_GET['oid'];

            $stmt->execute();

            return $stmt->fetchAll();
        }
    }

    function order($pdo){
        $sql = "SELECT * FROM orders WHERE id = :order_id ";
        if($stmt = $pdo->prepare($sql)){

            $stmt->bindParam(":order_id", $param_order_id, PDO::PARAM_STR);

            $param_order_id = $_GET['oid'];

            $stmt->execute();

            $order = $stmt->fetch();

            if($order['kupac_id'] != $_SESSION['id'])
                header("location: orders.php");

            return $order;
        }
    }

    $sql_user = "SELECT address FROM users WHERE id = :id";
    if($stmt = $pdo->prepare($sql_user)){

        $stmt->bindParam(":id", $param_user_id, PDO::PARAM_STR);

        $param_user_id = $_SESSION["id"];

        $stmt->execute();

        $user = $stmt->fetch();
    }

    $order_products = getOrderProducts($pdo);
    $order = order($pdo);
}else{
    header("location: orders.php");
} 



$order_remove_product_message = '';
$order_update_message = '';

if($_POST){
    if(isset($_POST['remove-product']) && $_POST['remove-product'] == 1){
        echo 'Obriši proizvod';

        $sql = "DELETE FROM order_products WHERE order_id = :order_id and product_id = :product_id";
        if($stmt = $pdo->prepare($sql)){

            $stmt->bindParam(":order_id", $param_order_id, PDO::PARAM_STR);
            $stmt->bindParam(":product_id", $param_product_id, PDO::PARAM_STR);

            $param_order_id = $_POST['oid'];
            $param_product_id = $_POST['pid'];

            $stmt->execute();

            $order_remove_product_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">Uspješno obrisan proizvod s narudžbe<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';

        }

    }

    if(isset($_POST['remove-order']) && $_POST['remove-order'] == 1){
        $sql = "DELETE FROM order_products WHERE order_id = :order_id";
        if($stmt = $pdo->prepare($sql)){

            $stmt->bindParam(":order_id", $param_order_id, PDO::PARAM_STR);

            $param_order_id = $_POST['oid'];

            $stmt->execute();

            $sql = "DELETE FROM orders WHERE id = :order_id ";
            if($stmt = $pdo->prepare($sql)){

                $stmt->bindParam(":order_id", $param_order_id, PDO::PARAM_STR);

                $param_order_id = $_POST['oid'];

                $stmt->execute();
            
                header("location: orders.php");
            }
        }
    }

    if(isset($_POST['order-update']) && $_POST['order-update'] == 1){
        $sql = "UPDATE orders SET address = :address WHERE id = :id";
        if($stmt = $pdo->prepare($sql)){

            $stmt->bindParam(":id", $param_order_id, PDO::PARAM_STR);
            $stmt->bindParam(":address", $param_address, PDO::PARAM_STR);

            $param_order_id = $_POST['order-update-id'];
            $param_address = $_POST['address'];

            $stmt->execute();

            foreach($_POST['pid'] as $key => $value){
                $sql_order = "UPDATE order_products SET amount = :amount WHERE order_id = :order_id AND product_id = :product_id";
                if($stmt = $pdo->prepare($sql_order)){

                    $stmt->bindParam(":order_id", $param_order_id, PDO::PARAM_STR);
                    $stmt->bindParam(":product_id", $param_product_id, PDO::PARAM_STR);
                    $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);

                    $param_order_id = $_POST['order-update-id'];
                    $param_product_id = $value;
                    $param_amount = $_POST['amount'][$key];

                    $stmt->execute();
                }
            }

            $order_update_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">Uspješno ažurirana narudžba
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';
        }
    }

    $order_products = getOrderProducts($pdo);
    $order = order($pdo);
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
                    <li class="nav-item active">
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
                    <h4>Uredi narudžbu <?php echo ORDER_PREFIX.$order['id'] ?></h4>
                </div>
                <div class="col-11 m-auto">
                    <div class="product-update-message col-12 col-md-10 m-auto padding0"><?php echo $order_remove_product_message; echo $order_update_message; ?></div>

                    <div class="row product-results edit-form">
                        <div class="col-12 col-md-10 col-lg-10 m-auto">
                            <?php 
                            if(count($order_products) != 0){
                            ?>
                                <form id="order-update-form" method="POST" action="<?php echo ROOT_URL; ?>order-edit.php?oid=<?php echo $_GET['oid'] ?>">

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
                                            foreach($order_products as $product){
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
                                                            <a href="javascript:void(0)" class="btn btn-primary update_on_order">Ažuriraj</a>
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
                                                        <?php
                                                        if(count($order_products) > 1){
                                                        ?>
                                                            <span data-pid="<?php echo $product['product_id']; ?>">
                                                                <i class="fas fa-times remove-product-from-order"></i>
                                                            </span>
                                                        <?php
                                                        }
                                                        ?>
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
                                        <input id="address" class="form-control" type="text" name="address" value="<?php echo $order['address']; ?>">
                                    </div>

                                    <div class="form-group margin-top-20 text-right">
                                        <input type="hidden" name="order-update" value="1">
                                        <input type="hidden" name="order-update-id" value="<?php echo $_GET['oid']; ?>">
                                        <button class="btn btn-primary store-order">Uredi</button>
                                        <a href="javascript:void(0)" class="btn btn-danger remove-order">Obriši</a>
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

        <div class="modal fade" id="order-product-delete-modal" tabindex="-1" role="dialog" aria-labelledby="order-product-delete-modal-label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Brisanje proizvoda</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Jeste li sigurni da želite obrisati proizvod <strong><span id="delete-order-product-name"></span></strong>?</p>
                        <form id="order-product-remove-form" method="POST" action="<?php echo ROOT_URL; ?>order-edit.php?oid=<?php echo $_GET['oid'] ?>">
                            <input type="hidden" name="remove-product" value="1">
                            <input type="hidden" name="pid" value="">
                            <input type="hidden" name="oid" value="<?php echo $_GET['oid']; ?>">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="order-product-remove-form" class="btn btn-danger">Obriši</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="order-delete-modal" tabindex="-1" role="dialog" aria-labelledby="order-delete-modal-label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Brisanje narudžbe</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Jeste li sigurni da želite obrisati narudžbu?</p>
                        <form id="order-remove-form" method="POST" action="<?php echo ROOT_URL; ?>order-edit.php?oid=<?php echo $_GET['oid'] ?>">
                            <input type="hidden" name="remove-order" value="1">
                            <input type="hidden" name="oid" value="<?php echo $_GET['oid']; ?>">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="order-remove-form" class="btn btn-danger">Obriši</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php';?>
</body>
</html>