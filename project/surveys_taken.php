<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<?php
$db = getDB();
$stmt = $db->prepare("SELECT title, Count(Responses.survey_id) as TOTAL from Responses JOIN Surveys ON Responses.survey_id = Surveys.id WHERE Responses.user_id=:id GROUP BY title LIMIT 10");
$r = $stmt->execute([":id" => get_user_id()]);
$result = [];
if($r){
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
}
?>
<?php
if(isset($_POST["result"])){
    die(header("Location: " . getURL("results.php")));
}
?>

<h3>Surveys Taken</h3>

<?php if(count($result) > 0): ?>
    <div class=”results”>
        <?php foreach($result as $s): ?>
            <div>
                <div><?php safer_echo($s["title"]); ?></div>
                <div><?php safer_echo($s["TOTAL"]); ?></div>
            </div>
        <?php endforeach; ?>
	<div>
		<a type="button" href="results.php?id=<?php safer_echo($r['id']); ?>">Results</a>
	</div>
    </div>
<?php else: ?>
    <p>No Results</p>
<?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php"); ?>
