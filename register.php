<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = $address = $email = "";
$username_error = $password_error = $confirm_password_error = $address_error = $email_error = "";
 
// Processing form data when form is submitted
if($_POST){

    if(empty(trim($_POST["role"]))){
        header("location: ".ROOT_URL);
    }
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_error = "Molimo unesite korisničko ime.";
    }else{
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
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_error = "Molimo unesite lozinku.";     
    }elseif(strlen(trim($_POST["password"])) < 6){
        $password_error = "Lozinka treba sadržavati minimalno 6 znakova.";
    }else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_error = "Molimo unesite ponovljenu lozinku.";     
    }else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_error) && ($password != $confirm_password)){
            $confirm_password_error = "Lozinke se ne poklapaju.";
        }
    }
    
    // Validate password
    if(empty(trim($_POST["address"]))){
        $address_error = "Molimo unesite adresu.";     
    }else{
        $address = trim($_POST["address"]);
    }

    // Validate unique email
    if(!empty(trim($_POST["email"]))){
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_error = "Email već postoji.";
                }else{
                    $email = trim($_POST["email"]);
                }
            }else{
                echo "Oops! Greška, pokušajte ponovo.";
            }
        }
         
        // Close statement
        unset($stmt);
    }
    
    // Check input errors before inserting in database
    if(empty($username_error) &&empty($email_error) && empty($password_error) && empty($confirm_password_error)){

        $role_id = trim($_POST["role"]);
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, email, password, address, role_id) VALUES (:username, :email, :password, :address, :role_id)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":address", $param_address, PDO::PARAM_STR);
            $stmt->bindParam(":role_id", $param_role_id, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_address = $address;
            $param_role_id = $role_id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: ".ROOT_URL);
            }else{
                echo "Oops! Greška, pokušajte ponovo.";
            }
        }
         
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
        <?php include 'head.php';?>
    </head>
    <body class="register-page">
        <div class="row">
            <div class="register-form-container col-12 col-sm-4 m-auto">
                <img class="logo-img" src="<?php echo ROOT_URL; ?>img/opg-logo.png">
                <h2 class="text-center">Registracija</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-group <?php echo (!empty($username_error)) ? 'has-error' : ''; ?>">
                        <label class="required_label">Korisničko ime</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                        <span class="help-block"><?php echo $username_error; ?></span>
                    </div>

                    <div class="form-group">
                        <label class="required_label">Uloga</label>
                        <select class="form-control" name="role">
                            <option value="1">Proizvođač</option>
                            <option value="2">Kupac</option>
                        </select>
                    </div>

                    <div class="form-group <?php echo (!empty($password_error)) ? 'has-error' : ''; ?>">
                        <label class="required_label">Lozinka</label>
                        <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                        <span class="help-block"><?php echo $password_error; ?></span>
                    </div>

                    <div class="form-group <?php echo (!empty($confirm_password_error)) ? 'has-error' : ''; ?>">
                        <label class="required_label">Ponovljena lozinka</label>
                        <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                        <span class="help-block"><?php echo $confirm_password_error; ?></span>
                    </div>

                    <div class="form-group <?php echo (!empty($address_error)) ? 'has-error' : ''; ?>">
                        <label class="required_label">Adresa</label>
                        <input type="text" name="address" class="form-control" value="<?php echo $address; ?>">
                        <span class="help-block"><?php echo $address_error; ?></span>
                    </div>

                    <div class="form-group <?php echo (!empty($email_error)) ? 'has-error' : ''; ?>">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                        <span class="help-block"><?php echo $email_error; ?></span>
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Registriraj">
                        <input type="reset" class="btn btn-default" value="Poništi">
                    </div>
                    <p>Već ste registrirani? <a href="<?php echo ROOT_URL; ?>">Prijavite se</a>.</p>
                </form>
            </div>
        </div>

        <?php include 'footer.php';?>
    </body>
</html>