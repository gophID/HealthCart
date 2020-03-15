<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
	header("location: dashboard.php");
	exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_error = $password_error = "";

// Processing form data when form is submitted
if($_POST){

	// Check if username is empty
	if(empty(trim($_POST["username"]))){
		$username_error = "Molimo unesite korisničko ime.";
	}else{
		$username = trim($_POST["username"]);
	}

	// Check if password is empty
	if(empty(trim($_POST["password"]))){
		$password_error = "Molimo unesite lozinku.";
	}else{
		$password = trim($_POST["password"]);
	}

	// Validate credentials
	if(empty($username_error) && empty($password_error)){
		// Prepare a select statement
		$sql = "SELECT id, username, password, role_id FROM users WHERE username = :username";

		if($stmt = $pdo->prepare($sql)){
			// Bind variables to the prepared statement as parameters
			$stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

			// Set parameters
			$param_username = trim($_POST["username"]);

			// Attempt to execute the prepared statement
			if($stmt->execute()){
				// Check if username exists, if yes then verify password
				if($stmt->rowCount() == 1){
					if($row = $stmt->fetch()){
						$id = $row["id"];
						$role_id = $row["role_id"];
						$username = $row["username"];
						$hashed_password = $row["password"];

						if(password_verify($password, $hashed_password)){
							// Password is correct, so start a new session
							session_start();

							// Store data in session variables
							$_SESSION["loggedin"] = true;
							$_SESSION["id"] = $id;
							$_SESSION["role"] = $role_id;
							$_SESSION["username"] = $username;                            

							// Redirect user to welcome page
							header("location: dashboard.php");
						}else{
							// Display an error message if password is not valid
							$password_error = "Netočno unesena lozinka.";
						}
					}
				}else{
					// Display an error message if username doesn't exist
					$username_error = "Ne postoji račun s unesenim korisničkim imenom.";
				}
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
		<?php include 'head.php'; ?>
	</head>
	<body class="login-page">
		<div class="row">
			<div class="login-form-container col-12 col-sm-4 m-auto">
				<img class="logo-img" src="<?php echo ROOT_URL; ?>img/opg-logo.png">
				<h2 class="text-center">Prijava</h2>
				<div class="row">
					<div class="col-10 m-auto">
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
							<div class="form-group <?php echo (!empty($username_error)) ? 'has-error' : ''; ?>">
								<label>Korisničko ime</label>
								<input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
								<span class="help-block"><?php echo $username_error; ?></span>
							</div>

							<div class="form-group <?php echo (!empty($password_error)) ? 'has-error' : ''; ?>">
								<label>Lozinka</label>
								<input type="password" name="password" class="form-control">
								<span class="help-block"><?php echo $password_error; ?></span>
							</div>

							<p>Nemate račun? <a href="<?php echo ROOT_URL; ?>register.php">Registracija</a>.</p>

							<div class="form-group">
								<input type="submit" class="btn btn-primary" value="Prijava">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php include 'footer.php';?>
	</body>
</html>