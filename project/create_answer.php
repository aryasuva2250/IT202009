<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("User")) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
if(isset($_GET["id"])){
    $id=$_GET["id"];
}
?>
<?php
$db = getDB();
$stmt = $db->prepare("SELECT id, question from Question");
$r = $stmt->execute();
$survey = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="createAnswer">
<h3>Create Answer</h3>
</div>
    <div class="createAnswerForm">
    <form method="POST">
    	<div class="createA">
        <label>Answer</label>
	</div>
        <input name="answer1" placeholder="Answer 1"/>
	<input name="answer2" placeholder="Answer 2"/>
	<div class="createASubmit">
        <input type="submit" name="save" value="Create"/>
	</div>
    </form>
    </div>
<?php
if(isset($_POST["save"])){
    $answer1 = $_POST["answer1"];
    $answer2 = $_POST["answer2"];
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Answers (answer, question_id) VALUES(:answer1, :question_id), (:answer2, :question_id)");
    $r = $stmt->execute([
        ":answer1" => $answer1,
	":answer2" => $answer2,
        ":question_id" => $id
    ]);
    if ($r) {
        flash("Created successfully with id: " . $db->lastInsertId());
    }
    else {
        $e = $stmt->errorInfo();
        flash("Error creating: " . var_export($e, true));
    }
}
?>
<?php require(__DIR__ . "/partials/flash.php"); 

