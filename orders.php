<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION['role'] != 2){
    header("location: index.php");
    exit;
}

require_once "config.php";

function fetchOrders($pdo){
    $sql_orders = "SELECT orders.id as order_id, orders.address as order_address, orders.status as order_status, orders.created_at as order_date, order_products.*, COUNT(order_products.id) as product_number, GROUP_CONCAT(order_products.status) as products_status FROM orders INNER JOIN order_products ON orders.id = order_products.order_id WHERE orders.kupac_id = :id GROUP BY orders.id ORDER BY orders.id DESC";
    if($stmt = $pdo->prepare($sql_orders)){

        $stmt->bindParam(":id", $param_user_id, PDO::PARAM_STR);

        $param_user_id = $_SESSION["id"];

        $stmt->execute();

        return $stmt->fetchAll();
    }
}

$orders = fetchOrders($pdo);

$order_remove_message = '';

if($_POST){
    $sql = "DELETE FROM order_products WHERE order_id = :order_id";
    if($stmt = $pdo->prepare($sql)){

        $stmt->bindParam(":order_id", $param_order_id, PDO::PARAM_STR);

        $param_order_id = $_POST['oid'];

        $stmt->execute();

        $sql = "DELETE FROM orders WHERE id = :order_id ";
        if($stmt = $pdo->prepare($sql)){

            $stmt->bindParam(":order_id", $param_product_id, PDO::PARAM_STR);

            $param_product_id = $_POST['oid'];

            $stmt->execute();

            $order_remove_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">Uspješno obrisana narudžba '.ORDER_PREFIX.$_POST['oid'].'<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';

        }
    }

    $orders = fetchOrders($pdo);
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
                    <h4>Narudžbe</h4>
                </div>
                <div class="col-11 m-auto">
                    <div class="product-update-message col-12 col-md-10 m-auto padding0"><?php echo $order_remove_message; ?></div>

                    <div class="row order-results order-list">
                        <div class="col-12 col-md-10 col-lg-10 m-auto">
                            <?php 
                            if(count($orders) != 0){
                            ?>
                                <table width="100%">
                                    <thead>
                                        <th>ID</th>
                                        <th>Adresa</th>
                                        <th>Datum</th>
                                        <th>Status</th>
                                        <th>Broj proizvoda</th>
                                        <th width="100px">Akcija</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach($orders as $key => $order){
                                        ?>
                                            <tr class="order">
                                                <td class="order-id" data-oid="<?php echo $order['order_id']; ?>"><?php echo ORDER_PREFIX; ?><?php echo $order['order_id']; ?></td>
                                                <td><?php echo $order['order_address']; ?></td>
                                                <td><?php echo date_format(date_create($order['order_date']),"d.m.Y H:i:s"); ?></td>
                                                <td>
                                                    <?php
                                                    switch($order['order_status']){
                                                        case 11:
                                                            echo '<i class="far fa-clock process-order"></i>';
                                                            break;
                                                        case 1:
                                                            echo '<i class="fas fa-check-circle success-order"></i>';
                                                            break;
                                                        case 0:
                                                            echo '<i class="fas fa-times-circle failure-order"></i>';
                                                            break;
                                                        default:
                                                            echo '<i class="far fa-clock process-order"></i>';
                                                            break;
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo $order['product_number']; ?></td>
                                                <td>
                                                    <?php
                                                    $status_array = explode(',', $order['products_status']);
                                                    $edit_delete_allowed = true;

                                                    if (in_array(1, $status_array) || in_array(0, $status_array)){
                                                        $edit_delete_allowed = false;
                                                    }
                                                    ?>
                                                    <a class="show-order-details action-link" href="<?php echo ROOT_URL; ?>order-show.php?oid=<?php echo $order['order_id']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php
                                                    if($edit_delete_allowed){
                                                    ?>
                                                        <a class="edit-order-details action-link" href="<?php echo ROOT_URL; ?>order-edit.php?oid=<?php echo $order['order_id']; ?>">
                                                            <i class="far fa-edit"></i>
                                                        </a>
                                                        <a data-oid="<?php echo $order['order_id']; ?>" data-o_prefix="<?php echo ORDER_PREFIX; ?>" class="remove-order action-link" href="javascript:void(0)">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php 
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            <?php
                            }else{
                            ?>
                                <p class="text-center" style="margin-bottom: 0;">Ne postoje narudžbe. Povratak na <a href="<?php echo ROOT_URL; ?>dashboard.php">proizvode.</a></p>
                            <?php
                            }
                            ?>
                        </div>
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
                        <p>Jeste li sigurni da želite obrisati narudžbu <strong><span id="delete-order-id"></span></strong>?</p>
                        <form id="order-remove-form" method="POST" action="<?php echo ROOT_URL; ?>orders.php">
                            <input type="hidden" name="oid" value="">
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