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
<?php
$page = 1;
$per_page = 10;
if(isset($_GET["page"])){
    try{
        $page = (int)$_GET["page"];
    }
    catch(Exception $s){
    }
}
$db = getDB();
$stmt = $db->prepare("SELECT count(*) as total from Surveys s where s.user_id = :id");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$total = 0;
if($result){
    $total = (int)$result["total"];
}
$total_pages = ceil($total / $per_page);
$offset = ($page-1) * $per_page;

$stmt = $db->prepare("SELECT s.*, title from Surveys s WHERE s.user_id = :id LIMIT :offset, :count");
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":id", get_user_id());
$s = $stmt->errorInfo();
//if($s[0] != "00000"){
  //  flash(var_export($s, true), "alert");
//}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<h3>Surveys Created</h3>
<div class="results">
    <?php if(count($results) > 0): ?>
        <div class="list-group">
	    <?php foreach ($results as $r): ?>
	        <div class="list-group-item">
	            <div>
		        <div class="ProfileSurveysCreate">Title: <?php safer_echo($r["title"]); ?></div>
		    </div>
	        </div>
            <?php endforeach; ?>
	</div>
    <?php else: ?>
        <p>No Created Surveys</p>
    <?php endif; ?>
</div>
<div class="surveysCreated">
<nav aria-label="Surveys Created">
</div>
<ul class="pagination justify-content-center">
    <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
        <div class="ProfilePrevious">
        <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
	</div>
    </li>
    <?php for($i = 0; $i < $total_pages; $i++):?>
    <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
    <?php endfor; ?>
    <li class="page-item <?php echo ($page+1) >= $total_pages?"disabled":"";?>">
        <div class="ProfileNext">
	<a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
        </div>
    </li>
</ul>
</nav>
<h3>Surveys Taken</h3>
<div class="results">
    <?php if(count($results) > 0): ?>
        <?php foreach($results as $s): ?>
	    <div>
	        <div>Title: <?php safer_echo($s["title"]); ?></div>
	    </div>
	<?php endforeach; ?>
    <?php else: ?>
        <p>No Taken Surveys</p>
    <?php endif; ?>
</div>

<nav aria-label="Surveys Taken">
<ul class="pagination justify-content-center">
    <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
         <div class="ProfileTakenPrevious">
        <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
        </div>
    </li>
    <?php for($i = 0; $i < $total_pages; $i++):?>
        <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
    <?php endfor; ?>
    <li class="page-item <?php echo ($page+1) >= $total_pages?"disabled":"";?>">
        <div class="ProfileTakenNext">
	<a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
        </div>
    </li>
</ul>
</nav>
<?php require(__DIR__ . "/partials/flash.php");
