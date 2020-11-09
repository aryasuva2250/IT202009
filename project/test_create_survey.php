<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<form method="POST">
    <label>Title</label>
    <input name="title" placeholder="Title"/>
    <label>Visibility</label>
    <select name="visibility">
        <option value="0">Draft</option>
	<option value="1">Private</option>
	<option value="2">Public</option>
    </select>
    <label>Description</label>
    <input type="text" name="description"/>
    <input type="submit" name="save" value="Create"/>
</form>
<?php
if(isset($_POST["save"])){
    $title = $_POST["title"];
    $visibility = $_POST["visibility"];	
    $description = $_POST["description"];
    $user = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Survey (title, visibility, description, user_id) VALUES(:title, :visibility, :description, :user)");
    $r = $stmt->execute([
        ":title"=>$title,
	":visibility"=>$visibility,
	":description"=>$description,
	":user"=>$user
    ]);
    if($r){
        flash("Created successfully with id: " . $db->lastInsertId());
	//echo "Success";
    }
    else{
    	$e = $stmt->errorInfo();
	flash("Error creating: " . var_export($e, true));
	//echo "Error";
    }
}
?>
<?php require(__DIR__ . "/partials/flash.php");
