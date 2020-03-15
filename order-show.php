<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

require_once "config.php";

if($_GET && $_GET['oid']){

    function getOrderProducts($pdo){
        if($_SESSION['role'] == 1){
            $sql_order_products = "SELECT order_products.*, products.naziv as name, users.username as prodavac_name, users.id as prodavac_id FROM order_products INNER JOIN products ON order_products.product_id = products.id INNER JOIN users ON products.proizvodac_id = users.id WHERE order_id = :id AND products.proizvodac_id = :proizvodac_id";
            if($stmt = $pdo->prepare($sql_order_products)){

                $stmt->bindParam(":id", $param_order_id, PDO::PARAM_STR);
                $stmt->bindParam(":proizvodac_id", $param_proizvodac_id, PDO::PARAM_STR);

                $param_order_id = $_GET['oid'];
                $param_proizvodac_id = $_SESSION['id'];

                $stmt->execute();

                return $stmt->fetchAll();
            }
        }else{
            $sql_order_products = "SELECT order_products.*, products.naziv as name, users.username as prodavac_name, users.id as prodavac_id FROM order_products INNER JOIN products ON order_products.product_id = products.id INNER JOIN users ON products.proizvodac_id = users.id WHERE order_id = :id";
            if($stmt = $pdo->prepare($sql_order_products)){

                $stmt->bindParam(":id", $param_order_id, PDO::PARAM_STR);

                $param_order_id = $_GET['oid'];

                $stmt->execute();

                return $stmt->fetchAll();
            }
        }
    }

    function order($pdo){
        $sql = "SELECT * FROM orders WHERE id = :order_id ";
        if($stmt = $pdo->prepare($sql)){

            $stmt->bindParam(":order_id", $param_order_id, PDO::PARAM_STR);

            $param_order_id = $_GET['oid'];

            $stmt->execute();

            $order = $stmt->fetch();

            return $order;
        }
    }

    $order_products = getOrderProducts($pdo);
    $order = order($pdo);
}else{
    header("location: orders.php");
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
                    <?php if($_SESSION['role'] == 1){ ?>
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo ROOT_URL; ?>orders-received.php">Narudžbe</a>
                        </li>
                    <?php }else{ ?>
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo ROOT_URL; ?>orders.php">Narudžbe</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
    </div>

    <div class="row page-content">
        <div class="col-12">

            <div class="row content-container">
                <div class="col-9 m-auto page-title">
                    <h4>Detalji narudžbe <?php echo ORDER_PREFIX.$order['id'] ?></h4>
                </div>
                <div class="col-11 m-auto">

                    <div class="row product-results edit-form">
                        <div class="col-12 col-md-10 col-lg-10 m-auto">
                            <?php 
                            if(count($order_products) != 0){
                            ?>
                                <div id="order-show">

                                    <table width="100%">
                                        <thead>
                                            <th>Naziv</th>
                                            <th>Količina</th>
                                            <th>Cijena</th>
                                            <th>Ukupno</th>
                                            <th width="5%">
                                                <?php if($_SESSION['role'] == 1){  
                                                    echo 'Potvrdi/Odbij';
                                                }else{
                                                    echo 'Status';
                                                }
                                                ?>
                                            </th>
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
                                                    <td>
                                                        <div class="update-amount">
                                                            <input type="hidden" name="amount[]" value="<?php echo $product['amount'] ?>">
                                                            <span data-amount="<?php echo $product['amount']; ?>">
                                                                <?php echo $product['amount']; ?> HRK
                                                            </span>
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
                                                    <td class="product-action">
                                                        <?php if($_SESSION['role'] == 1){ ?>
                                                            <div class="row">
                                                                <?php
                                                                if($product['status'] != 11){
                                                                    echo '<p>'.$product['comment'].'</p>';
                                                                }else{
                                                                ?>
                                                                    <textarea class="form-control"></textarea>
                                                                <?php
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="row">
                                                                <?php
                                                                if($product['status'] == 0){
                                                                    echo '<p><i class="fas fa-times-circle failure-order"></i></p>';
                                                                }else if($product['status'] == 1){
                                                                    echo '<p><i class="fas fa-check-circle success-order"></i></p>';
                                                                }else{
                                                                ?>
                                                                    <a data-oid="<?php echo $_GET['oid']; ?>" data-pid="<?php echo $product['product_id'] ?>" href="javascript:void(0)" class="btn btn-success">Potvrdi</a>
                                                                    <a data-oid="<?php echo $_GET['oid']; ?>" data-pid="<?php echo $product['product_id'] ?>" href="javascript:void(0)" class="btn btn-danger ml-auto">Odbij</a>
                                                                <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        <?php }else{
                                                            if($product['status'] == 11){
                                                                echo '<div class="row"><p><i class="far fa-clock process-order"></i></p></div>';
                                                            }else if($product['status'] == 0){
                                                                echo '<div class="row"><p>'.$product['comment'].'</p></div>';
                                                                echo '<div class="row"><p><i class="fas fa-times-circle failure-order"></i></p></div>';
                                                            }else if($product['status'] == 1){
                                                                echo '<div class="row"><p>'.$product['comment'].'</p></div>';
                                                                echo '<div class="row"><p><i class="fas fa-check-circle success-order"></i></p></div>';
                                                            }
                                                        }?>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td class="sum-title"><strong>Ukupno</strong></td>
                                                <td class="sum-number"><strong><?php echo number_format((float)$sum, 2, '.', ''); ?> HRK</strong></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="form-group margin-top-20 text-right">
                                        <label for="address">Adresa</label>
                                        <input id="address" class="form-control" type="text" name="address" value="<?php echo $order['address']; ?>" disabled>
                                    </div>
                                </div>
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