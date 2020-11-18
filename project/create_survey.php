<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("User")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<form method="POST">
    <label>Survey Title</label>
    <input name="title" placeholder="Title"/>
    <label>Survey Description</label>
    <input type="text" name="description"/>
    <label>Survey Category</label>
    <select name="category">
        <option value="Volunteer Feedback">Volunteer Feedback</option>
        <option value="Event Feedback">Event Feedback</option>
        <option value="Event Registration">Event Registration</option>
        <option value="Quiz">Quiz</option>
        <option value="Customer Feedback">Customer Feedback</option>
        <option value="Food">Food</option>
        <option value="Cars">Cars</option>
        <option value="Other">Other</option>
    </select>
    <label>Survey Visibility</label>
    <select name="visibility">
        <option value="Draft">Draft</option>
        <option value="Private">Private</option>
        <option value="Public">Public</option>
    </select>
    <label name="date">Date</label>
    <input type="date" id="date" name="date">
    <input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
    $title = $_POST["title"];
    $description = $_POST["description"];
    $category = $_POST["category"];
    $visibility = $_POST["visibility"];	
    $date = $_POST["date"];
    $user = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Surveys (title, description, visibility, category, date, user_id) VALUES(:title, :description, :visibility, :category, :date, :user)");
    $r = $stmt->execute([
        ":title"=>$title,
        ":description"=>$description,
	":visibility"=>$visibility,
	":category"=>$category,
        ":date"=>$date,
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
