<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("User")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
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
    $name = $_POST["question"];
    $user = get_user_id();
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Question (question, survey_id) VALUES(:name, :user)");
    $r = $stmt->execute([
        ":name"=>$name,
	":user"=>$user,
    ]);
    if($r){
        flash("Created successfully with id: " . $db->lastInsertId());
    }
    else{
        $e = $stmt->errorInfo();
	flash("Error creating: " . var_export($e, true));
    }
}
?>

    <div class="createQuestion">
    <h3>Create Question</h3>
    </div>
    <div class="createQForm">
    <form method="POST">
        <div class="createQ">
	<label>Question</label>
        <input name="question" placeholder="Question"/>
	</div>
	<div class="createQSubmit">
        <input type="submit" name="save" value="Create"/>
	</div>
    </form>
    </div>

<?php require(__DIR__ . "/partials/flash.php");
