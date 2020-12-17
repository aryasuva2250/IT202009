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
	$description = $_POST["description"];
	$category = $_POST["category"];
	$visibility = $_POST["visibility"];
	$date = $_POST["date"];
	$user = get_user_id();
	$db = getDB();
	if(isset($id)){
		$stmt = $db->prepare("UPDATE Surveys set title=:title, description=:description, category=:category, visibility=:visibility, date=:date where id=:id");
		$r = $stmt->execute([
			":title"=>$title,
			":description"=>$description,
			":category"=>$category,
			":visibility"=>$visibility,
			":date"=>$date,
			":user"=>$user
		]);
				if($r){
			          flash("Updated successfully with id: " . $id);
		                } 
		                else{
			           $e = $stmt->errorInfo();
			           flash("Error updating: " . var_export($e, true));
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
	$stmt = $db->prepare("SELECT * FROM Surveys where id = :id");
	$r = $stmt->execute([":id"=>$id]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<div class="AdminEditSurvey">
<form method="POST">
	<div class="AdminEditSurveyTitle">
	<label>Title</label>
	</div>
	<div class="AdminEditSurveyTitlePlace">
	<input name="title" placeholder="Title" value="<?php echo $result["title"];?>"/>
	</div>
	<div class="AdminEditSurveyDesc">
	<label>Description</label>
	</div>
	<div class="AdminEditSurveyDescPlace">
	<input type="text" name="description"/>
	</div>
	<div class="AdminEditSurveyCategory">
	<label>Category</label>
	</div>
	<div class=AdminEditSurveyCategoryPlace">
	<input type=”text” name="category"/>
	</div>
	<div class="AdminEditSurveyVisib">
	<label>Visibility</label>
	</div>
	<div class="AdminEditSurveyVisibPlace">
	<select name="visibility" value="<? echo $result["visibility"];?>">
		<option value="Draft" <?php echo ($result["visibility"] == "Draft"?'selected="selected"':'');?>>Draft</option>
		<option value="Private" <?php echo ($result["visibility"] == "Private"?'selected="selected"':'');?>>Private</option>
		<option value="Public" <?php echo ($result["visibility"] == "Public"?'selected="selected"':'');?>>Public</option>
	</select>
	</div>
	<div class="AdminEditSurveyDate">
	<label>Date</label>
	</div>
	<div class="AdminEditSurveyDatePlace">
	<input type=”date” name="date"/>
	</div>
	<div class="AdminEditSurveySubmit">
	<input type="submit" name="save" value="Update"/>
	</div>
</form>
</div>

<?php require(__DIR__ . "/partials/flash.php");
