<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION['role'] != 1){
    header("location: index.php");
    exit;
}

require_once "config.php";

$product_update_message = '';
$product = null;
$slika = $naziv = $cijena = '';
$slika_error = $naziv_error = $cijena_error = '';

$file_image_error = $file_exists_error = $file_size_error = $file_format_error = $file_upload_error = '';

if($_GET && $_GET['pid']){
    $sql = "SELECT * FROM products WHERE id = :pid ";
    if($stmt = $pdo->prepare($sql)){

        $stmt->bindParam(":pid", $param_product_id, PDO::PARAM_STR);

        $param_product_id = $_GET['pid'];

        $stmt->execute();

        $product = $stmt->fetch();
    }
}else{
    header("location: own-products.php");
}

if($_SESSION['id'] != $product['proizvodac_id'])
    header("location: own-products.php");


if($_POST){

    $param_product_image = '';
    if($_FILES["slika"]["tmp_name"] != ''){
        $target_dir = "img/products/";

        //Obriši sliku sa servera
        unlink($target_dir.$product['slika']);


        $target_file = $target_dir . basename($_FILES["slika"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        //Error je li datoteka slika
        $check = getimagesize($_FILES["slika"]["tmp_name"]);
        if($check !== false){
            $uploadOk = 1;
        }else{
            $file_image_error = 'Odabrana datoteka nije slika.';
            $uploadOk = 0;
        }

        // Error je li slika s tim imenom već postoji
        if(file_exists($target_file)){
            $file_exists_error = "Slika s tim imenom već postoji.";
            $uploadOk = 0;
        }

        // Error veličine slike
        if($_FILES["slika"]["size"] > UPLOAD_IMAGE_FILE_SIZE){
            $file_size_error = "Veličina slike prelazi maksimalnu dozvoljenu. (".UPLOAD_IMAGE_FILE_SIZE.' B)';
            $uploadOk = 0;
        }


        // Error formata datoteke
        if(!in_array($imageFileType, UPLOAD_IMAGE_FILE_FORMATS)){
            $file_format_error = "Dozvoljeni tipovi datoteke su ".implode(', ', UPLOAD_IMAGE_FILE_FORMATS); 
            $uploadOk = 0;
        }

        // Provjeri je li postoji jedan od errora
        if($uploadOk != 0){
            if(move_uploaded_file($_FILES["slika"]["tmp_name"], $target_file)){
                // Ako nema errora dodaj proizvod u tablicu
                $param_product_image = basename($_FILES["slika"]["name"]);
            }else{
                $file_upload_error = "Dogodila se greška prilikom učitavanja slike.";
            }
        }
    }

    // Ako nema errora ažuriraj proizvod u tablicu
    $update_img_query = '';
    if($param_product_image != ''){
        $update_img_query = ', slika = :slika';
    }
    $sql = "UPDATE products SET naziv = :naziv, cijena = :cijena".$update_img_query." WHERE id = :id";
    if($stmt = $pdo->prepare($sql)){

        $stmt->bindParam(":id", $param_product_id, PDO::PARAM_STR);
        $stmt->bindParam(":naziv", $param_product_name, PDO::PARAM_STR);
        $stmt->bindParam(":cijena", $param_product_price, PDO::PARAM_STR);
        if($param_product_image != ''){
            $stmt->bindParam(":slika", $param_product_image, PDO::PARAM_STR);
        }

        $param_product_id = $product['id'];
        $param_product_name = $_POST['naziv'];
        $param_product_price = $_POST['cijena'];

        $stmt->execute();

        $product_update_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">Uspješno ažuriran proizvod <strong>'.$_POST['naziv'].'</strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>';
    }

    $sql = "SELECT * FROM products WHERE id = :pid ";
    if($stmt = $pdo->prepare($sql)){

        $stmt->bindParam(":pid", $param_product_id, PDO::PARAM_STR);

        $param_product_id = $_GET['pid'];

        $stmt->execute();

        $product = $stmt->fetch();
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

            <div class="row content-container">
                <div class="col-5 m-auto page-title">
                    <h4>Uredi proizvod <?php echo $product['naziv']; ?></h4>
                </div>

                <div class="col-11 m-auto">
                    <div class="product-update-message col-12 col-md-6 m-auto padding0"><?php echo $product_update_message; ?></div>

                    <div class="row product-results edit-form">
                        <div class="col-12 col-md-10 col-lg-6 m-auto">
                            <form id="product-edit-form" method="POST" action="<?php echo ROOT_URL; ?>edit-product.php?pid=<?php echo $_GET['pid']; ?>" enctype="multipart/form-data">
                                <input type="hidden" name="pid" value="<?php echo $product['id'] ?>">

                                <div class="row img-row form-group <?php echo (!empty($slika_error)) ? 'has-error' : ''; ?>">
                                    <div class="col-12 col-md-6">
                                        <div class="row">
                                            <label for="img" class="">Nova slika</label>
                                        </div>
                                        <div class="row new-img-name">
                                            <p></p>
                                        </div>
                                        <input id="img" type="file" name="slika" class="form-control" value="<?php echo $slika; ?>">

                                        <div class="new-img-preview"></div>
                                        <div class="row">
                                            <label for="img" class="btn btn-primary">Odaberi</label>
                                        </div>
                                        <div class="row help-row">
                                            <?php 
                                            if($file_image_error != '') echo '<span class="help-block">'.$file_image_error.'</span>';
                                            if($file_exists_error != '') echo '<span class="help-block">'.$file_exists_error.'</span>';
                                            if($file_size_error != '') echo '<span class="help-block">'.$file_size_error.'</span>';
                                            if($file_format_error != '') echo '<span class="help-block">'.$file_format_error.'</span>';
                                            if($file_upload_error != '') echo '<span class="help-block">'.$file_upload_error.'</span>';
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 current-product-img">
                                        <div class="row">
                                            <label>Trenutna slika</label>
                                        </div>
                                        <div class="row">
                                            <img src="<?php echo ROOT_URL.'/img/products/'.$product['slika'] ?>" alt="<?php echo htmlspecialchars($product['naziv']); ?>" title="<?php echo htmlspecialchars($product['naziv']); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group <?php echo (!empty($naziv_error)) ? 'has-error' : ''; ?>">
                                    <label class="required_label">Naziv</label>
                                    <input type="text" name="naziv" class="form-control" value="<?php echo $product['naziv']; ?>">
                                    <span class="help-block"><?php echo $naziv_error; ?></span>
                                </div>

                                <div class="form-group <?php echo (!empty($cijena_error)) ? 'has-error' : ''; ?>">
                                    <label class="required_label">Cijena</label>
                                    <input type="text" name="cijena" class="form-control" value="<?php echo $product['cijena']; ?>">
                                    <span class="help-block"><?php echo $cijena_error; ?></span>
                                </div>

                                <div class="form-group margin-top-20">
                                    <button class="btn btn-primary product-remove" data-pid="<?php echo $product['id']; ?>">Uredi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include 'footer.php';?>
</body>
</html>