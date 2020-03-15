<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
function userDetails($pdo){
    $sql = "SELECT * FROM users WHERE id = :id";
    if($stmt = $pdo->prepare($sql)){

        $stmt->bindParam(":id", $param_user_id, PDO::PARAM_STR);

        $param_user_id = $_SESSION["id"];

        $stmt->execute();

        return $stmt->fetch();
    }
}
$user = userDetails($pdo);

// Define variables and initialize with empty values
$user_update_message = "";
$username = $new_password = $confirm_password = $address = $email = "";
$username_error = $new_password_error = $confirm_password_error = $address_error = $email_error = "";
 
// Processing form data when form is submitted
if($_POST){
    $user = userDetails($pdo);
    
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_error = "Molimo unesite korisničko ime.";
    }else{
        if(trim($_POST["username"]) != $user['username']){
            // Prepare a select statement
            $sql = "SELECT id FROM users WHERE username = :username";
            
            if($stmt = $pdo->prepare($sql)){
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                
                // Set parameters
                $param_username = trim($_POST["username"]);
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    if($stmt->rowCount() == 1){
                        $username_error = "Korisničko ime već postoji.";
                    }else{
                        $username = trim($_POST["username"]);
                    }
                }else{
                    echo "Oops! Greška, pokušajte ponovo.";
                }
            }
             
            // Close statement
            unset($stmt);
        }
    }
 
    // Validate new password
    if(!empty(trim($_POST["new_password"])) && strlen(trim($_POST["new_password"])) < 6){
        $new_password_error = "Lozinka treba sadržavati minimalno 6 znakova.";
    }elseif(!empty(trim($_POST["new_password"]))){
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(!empty(trim($_POST["new_password"])) && empty(trim($_POST["confirm_password"]))){
        $confirm_password_error = "Molimo unesite ponovljenu lozinku.";
    }else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($confirm_password_error) && ($new_password != $confirm_password)){
            $confirm_password_error = "Lozinke se ne poklapaju.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($confirm_password_error) && empty($confirm_password_error)){
        
        $sql = "UPDATE users SET address = :address, email = :email WHERE id = :id";
        if($stmt = $pdo->prepare($sql)){

            $stmt->bindParam(":address", $param_address, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);

            $param_address = $_POST['address'];
            $param_email = $_POST['email'];
            $param_id = $_SESSION["id"];

            $stmt->execute();
        }

        if($username != ""){
            $sql = "UPDATE users SET username = :username WHERE id = :id";
            if($stmt = $pdo->prepare($sql)){

                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);

                $param_username = $username;
                $param_id = $_SESSION["id"];

                $stmt->execute();
            }
        }

        if($new_password != ""){
            $sql = "UPDATE users SET password = :new_password WHERE id = :id";
            if($stmt = $pdo->prepare($sql)){

                $stmt->bindParam(":new_password", $param_new_password, PDO::PARAM_STR);
                $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);

                $param_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $param_id = $_SESSION["id"];

                $stmt->execute();
            }
        }

        $user_update_message = '<div class="alert alert-success alert-dismissible fade show" role="alert">Uspješno ažuriran profil
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>';

        $user = userDetails($pdo);
        
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($pdo);
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

            <div class="row content-container">
                <div class="col-5 m-auto page-title">
                    <h4>Profil</h4>
                </div>

                <div class="col-11 m-auto">
                    <div class="product-add-message col-12 col-md-6 m-auto padding0"><?php echo $user_update_message; ?></div>

                    <div class="row product-results insert-form">
                        <div class="col-12 col-md-10 col-lg-6 m-auto">
                            
                            <form autocomplete="off" id="profil-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <input autocomplete="off" name="hidden" type="search" style="display:none;">

                                <div class="form-group <?php echo (!empty($username_error)) ? 'has-error' : ''; ?>">
                                    <label class="required_label">Korisničko ime</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>">
                                    <span class="help-block"><?php echo $username_error; ?></span>
                                </div>

                                <div class="form-group <?php echo (!empty($new_password_error)) ? 'has-error' : ''; ?>">
                                    <label class="required_label">Nova lozinka</label>
                                    <input type="password" name="new_password" class="form-control" value="" autocomplete="new-password">
                                    <span class="help-block"><?php echo $new_password_error; ?></span>
                                </div>

                                <div class="form-group <?php echo (!empty($confirm_password_error)) ? 'has-error' : ''; ?>">
                                    <label class="required_label">Ponovljena lozinka</label>
                                    <input type="password" name="confirm_password" class="form-control" value="">
                                    <span class="help-block"><?php echo $confirm_password_error; ?></span>
                                </div>

                                <div class="form-group <?php echo (!empty($address_error)) ? 'has-error' : ''; ?>">
                                    <label class="required_label">Adresa</label>
                                    <input type="text" name="address" class="form-control" value="<?php echo $user['address']; ?>">
                                    <span class="help-block"><?php echo $address_error; ?></span>
                                </div>

                                <div class="form-group <?php echo (!empty($email_error)) ? 'has-error' : ''; ?>">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>">
                                    <span class="help-block"><?php echo $email_error; ?></span>
                                </div>

                                <div class="form-group margin-top-20">
                                    <input type="submit" class="btn btn-success" value="Ažuriraj">
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