<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (isset($_POST["register"])) {
    $email = null;
    $password = null;
    $confirm = null;
    $username = null;
    if (isset($_POST["email"])) {
    	$email = $_POST["email"];
    }
    if (isset($_POST["password"])){
    	$password = $_POST["password"];
    }
    if (isset($_POST["confirm"])) {
    	$confirm = $_POST["confirm"];
    }
    if(isset($_POST["username"])){
    	$username = $_POST["username"];
    }
    $isValid = true;
    //check if passwords match on the server side
    if($password == $confirm) {
    	//echo "Passwords match <br>";
    }
    else {
    	//echo "Passwords don't match<br>";
	flash("Passwords don't match");
	$isValid = false;
    }
    if(!isset($email)||!isset($password)||!isset($confirm)){
    	$isValid = false;
    }
    if($isValid) {
	$hash =	password_hash($password, PASSWORD_BCRYPT);
	$db = getDB();
	if(isset($db)) {
		$stmt = $db->prepare("INSERT INTO Users(email, username, password) VALUES(:email,:username,:password)");
		$params = array(":email" => $email, ":username" => $username, ":password" => $hash);
		$r = $stmt->execute($params);
		//echo "db returned: " . var_export($r, true);
		$e = $stmt->errorInfo();
		if($e[0] == "00000"){
			//echo "<br>Welcome! You successfully registered, please login.";
			flash("Successfully registered! Please login.");
		}
		else {
			if($e[0] == "23000") {
				//echo "<br>Either username or email is already registered, please try again";
				flash("Username or email already exists");
			}
			else {
				//echo "uh oh something went wrong: " . var_export($e, true);
				flash("An error occurred, please try again");
			}
		}
	}
}
else {
	//echo "There was a validation issue";
	flash("There was a validation issue");
}
}
if(!isset($email)){
	$email = "";
}
if(!isset($username)){
	$username = "";
}

?>
<div class="reg">
<form method="POST">
	<div class="reg1">
	<label for ="email">Email:</label>
	<input type="email" id="email" name="email" required value="<?php safer_echo($email); ?>" size="10"/>
	</div>
	<div class="reg2">
	<label for="user">Username:</label>
	<input type="text" id="user" name="username" required maxlength="60" value="<?php safer_echo($username); ?>"/>
	</div>
	<div class="reg3">
	<label for="p1">Password:</label>
	<input type="password" id="p1" name="password" required/>
	</div>
	<div class="reg4">
	<label for="p2">Confirm Password:</label>
	<input type="password" id="p2" name="confirm" required/>
	</div>
	<div class="reg5">
	<input type="submit" name="register" value="Register"/>
	</div>
</form>
</div>
<?php require(__DIR__ . "/partials/flash.php");
