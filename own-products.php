<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION['role'] != 1){
    header("location: index.php");
    exit;
}

require_once "config.php";

$product_remove_message = '';

if($_POST){
    $target_dir = "img/products/";
    $sql = "SELECT * FROM products WHERE id = :pid ";
    if($stmt = $pdo->prepare($sql)){

        $stmt->bindParam(":pid", $param_product_id, PDO::PARAM_STR);

        $param_product_id = $_POST['pid'];

        $stmt->execute();

        $product = $stmt->fetch();

        unlink($target_dir.$product['slika']);
    }


    $sql = "DELETE FROM products WHERE id = :pid ";
    if($stmt = $pdo->prepare($sql)){

        $stmt->bindParam(":pid", $param_product_id, PDO::PARAM_STR);

        $param_product_id = $_POST['pid'];

        $stmt->execute();


$product_remove_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">Uspješno obrisan proizvod <strong>'.$_POST['pname'].'</strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>';
    }
}
// print_r($_SESSION);
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
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo ROOT_URL; ?>own-products.php">Moji proizvodi</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo ROOT_URL; ?>orders-received.php">Narudžbe</a>
                    </li>
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
                    <div style="display: none;" class="form-group">
                        <input type="text" class="form-control" id="search-prodavac" placeholder="Prodavač">
                    </div>
                    <div class="form-group add-product-page">
                        <a href="<?php echo ROOT_URL; ?>add-product.php" class="btn btn-success product-add">Dodaj proizvod</a>
                    </div>
                </div>
            </div>

            <div class="row content-container">
                <div class="col-11 m-auto">
                    <div class="product-remove-message col-12 col-md-6"><?php echo $product_remove_message; ?></div>

                    <div class="row product-results">
                        <?php
                            $sql = "SELECT users.*, products.*, products.id as product_id FROM products INNER JOIN users ON products.proizvodac_id = users.id WHERE products.proizvodac_id = :id";
                            
                            if($stmt = $pdo->prepare($sql)){
                                $stmt->bindParam(":id", $param_user_id, PDO::PARAM_STR);

                                $param_user_id = $_SESSION["id"];
                                $stmt->execute();
                        
                                $products = $stmt->fetchAll();
                            }

                            if(count($products) != 0){
                                //echo '<pre>'; print_r($products); echo '</pre>';
                                foreach($products as $key => $product) {
                        ?>
                                    <div class="product col-12 col-sm-3 text-center">
                                        <div class="row product-img">
                                            <img src="<?php echo ROOT_URL.'/img/products/'.$product['slika'] ?>" alt="<?php echo htmlspecialchars($product['naziv']); ?>" title="<?php echo htmlspecialchars($product['naziv']); ?>">
                                        </div>
                                        <div class="row product-title">
                                            <p><?php echo htmlspecialchars($product['naziv']); ?></p>
                                        </div>
                                        <div class="row product-price">
                                            <label>Cijena: &nbsp;</label>
                                            <span><?php echo $product['cijena']; ?> HRK</span>
                                        </div>
                                        <div class="product-edit-delete">
                                            <a href="<?php echo ROOT_URL; ?>edit-product.php?pid=<?php echo $product['product_id']; ?>" class="btn btn-primary product-edit" data-pid="<?php echo $product['product_id']; ?>">Uredi</a>
                                            <button class="btn btn-danger product-remove" data-pid="<?php echo $product['product_id']; ?>">Obriši</button>
                                        </div>
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

            <div class="modal fade" id="product-delete-modal" tabindex="-1" role="dialog" aria-labelledby="product-delete-modal-label" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Brisanje proizvoda</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Jeste li sigurni da želite obrisati proizvod <strong><span id="delete-product-name"></span></strong>?</p>
                            <form id="product-remove-form" method="POST" action="<?php echo ROOT_URL; ?>own-products.php">
                                <input type="hidden" name="pname" value="">
                                <input type="hidden" name="pid" value="">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" form="product-remove-form" class="btn btn-danger">Obriši</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include 'footer.php';?>
</body>
</html>