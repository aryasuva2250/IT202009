<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
	flash("You must be logged in to access this page");
	die(header("Location: login.php"));
}
$db = getDB();
$stmt = $db->prepare("SELECT visibility from Users WHERE id = :id LIMIT 1");
$stmt->execute([":id" => get_user_id()]);
$v = $stmt->fetch(PDO::FETCH_ASSOC);
$visibility = $v["visibility"];
$result = [];
if (isset($_POST["saved"])) {
	$isValid = true;
	$result = [];
	$newEmail = get_email();
	if (get_email() != $_POST["email"]) {
		$email = $_POST["email"];
        	$stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where email = :email");
        	$stmt->execute([":email" => $email]);
        	$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$inUse = 1;
		if ($result && isset($result["InUse"])) {
			try {
				$inUse = intval($result["InUse"]);
			}
			catch (Exception $e){
			}
		}
		if ($inUse > 0) {
			//echo "Email is already in use";
			flash("Email already in use");
			$isValid = false;
		}
		else{
			$newEmail = $email;
		}
	}
	$newUsername = get_username();
    	if (get_username() != $_POST["username"]) {
		$username = $_POST["username"];
		$stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where username = :username");
		$stmt->execute([":username" => $username]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$inUse = 1;
		if ($result && isset($result["InUse"])) {
			try {
				$inUse = intval($result["InUse"]);
			}
			catch (Exception $e){
			}
		}
		if ($inUse > 0) {
			//echo "Username is already in use";
			flash("Username already in use");
			$isValid = false;
		}
		else{
			$newUsername = $username;
		}
	}
	if ($isValid){
		$visibility = $_POST["visibility"];
		$stmt = $db->prepare("UPDATE Users set email = :email, username= :username, visibility= :visibility where id = :id");
		$r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername, ":id" => get_user_id(), ":visibility" => $visibility]);
		if ($r){
			//echo "Updated profile";
			flash("Updated profile");
		}
		else{
			//echo "Error updating profile";
			flash("Error updating profile");
		}
		if (!empty($_POST["password"]) && !empty($_POST["confirm"])) {
			if ($_POST["password"] == $_POST["confirm"]) {
				$password = $_POST["password"];
				$hash = password_hash($password, PASSWORD_BCRYPT);
				$stmt = $db->prepare("UPDATE Users set password = :password where id = :id");
				$r = $stmt->execute([":id" => get_user_id(), ":password" => $hash]);
				if ($r){
					//echo "Reset password";
					flash("Reset Password");
				}
				else{
					//echo "Error resetting password";
					flash("Error resetting password");
				}
			}
		}
		$stmt = $db->prepare("SELECT email, username, visibility from Users WHERE id = :id LIMIT 1");
		$stmt->execute([":id" => get_user_id()]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($result) {
			$email = $result["email"];
			$username = $result["username"];
			$visibility = $result["visibility"];
			$_SESSION["user"]["email"] = $email;
			$_SESSION["user"]["username"] = $username;
		}
	}
	else{
	}
}
?>
<div class="prof">
<form method="POST">
	<div class="prof1">
	<label for="email">Email</label>
	<input name="email" value="<?php safer_echo(get_email()); ?>"/>
	</div>
	<div class="prof2">
	<label for="username">Username</label>
	<input type="text" maxlength="60" name="username" value="<?php safer_echo(get_username()); ?>"/>
	</div>
	<div class="prof3">
	<!-- DO NOT PRELOAD PASSWORD-->
	<label for="pw">Password</label>
	<input type="password" name="password"/>
	</div>
	<div class="prof4">
	<label for="cpw">Confirm Password</label>
	<input class="prof4" type="password" name="confirm"/>
	</div>
	<label>Visibility</label>
	<select name="visibility" value="<?php echo $result["visibility"];?>">
	    <option value="1" <?php echo ($result["visibility"] == "1"?'selected="selected"':'');?>>Private</option>
	    <option value="2" <?php echo ($result["visibility"] == "2"?'selected="selected"':'');?>>Public</option>
	</select>
	<div class="prof5">
	<input type="submit" name="saved" value="Save Profile"/>
	</div>
</form>
</div>
<?php require(__DIR__ . "/partials/flash.php");
