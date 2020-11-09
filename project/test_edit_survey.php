<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<?php
if(isset($_GET["id"])){
	$id = $_GET["id"];
}
?>
<?php
if(isset($_POST["save"])){
	$title = $_POST["title"];
	$visibility = $_POST["visibility"];
	$description = $_POST["description"];
	$user = get_user_id();
	$db = getDB();
	if(isset($id)){
		$stmt = $db->prepare("UPDATE Survey set title=:title, visibility=:visibility, description=:description where id=:id");
		$r = $stmt->execute([
			":title"=>$title,
			":visibility"=>$visibility,
			":description"=>$description,
			":id"=>$id
		]);
				if($r){
			flash("Updated successfully with id: " . $id);
		}
		else{
			$e = $stmt->errorInfo();
			echo "Error updating: " . var_export($e, true);
		}
	}
	else{
		flash("ID is not set, we need an ID in order to update");
	}
}
?>
<?php
$result =[];
if(isset($id)){
	$id = $_GET["id"];
	$db = getDB();
	$stmt = $db->prepare("SELECT * FROM Survey where id = :id");
	$r = $stmt->execute([":id"=>$id]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<form method="POST">
	<label>Title</label>
	<input name="title" placeholder="Title" value="<?php echo $result["title"];?>"/> 
	<label>Visibility</label>
	<select name="visibility" value="<? echo $result["visibility"];?>">
		<option value="0" <?php echo ($result["visibility"] == "0"?'selected="selected"':'');?>>Draft</option>  
		<option value="1" <?php echo ($result["visibility"] == "1"?'selected="selected"':'');?>>Private</option>
		<option value="2" <?php echo ($result["visibility"] == "2"?'selected="selected"':'');?>>Public</option>
	</select>
	<label>description</label>
	<input type="text" name="description"/>
	<input type="submit" name="save" value="Update"/>
</form>

<?php require(__DIR__ . "/partials/flash.php");
