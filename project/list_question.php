<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$query = "";
$results = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}
if (isset($_POST["search"]) && !empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT quest.id,quest.question,quest.survey_id,Surveys.user_id from Question as quest JOIN Surveys on quest.survey_id = Surveys.id WHERE quest.question like :q LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
}
?>
<div class="listQuestionForm">
<div class="listQuestion">
<h3>List Questions</h3>
</div>
<form method="POST">
    <input name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
    <input type="submit" value="Search" name="search"/>
</form> 
</div>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
		<div>
                        <div class="listQTitle">
			<div>Title of Question:</div>
			</div>
                        <div><?php safer_echo($r["question"]); ?></div>
                </div>
		<div>
		        <div class="listQOwner">
                        <div>Owner:</div>
			</div>
                        <div><?php safer_echo($r["id"]); ?></div>
                </div>
		<div>
		        <div class="listQSurvey">
		        <div>Survey</div>
			</div>
		        <div><?php safer_echo($r["survey_id"]); ?></div>
		</div>
                    <div>
                        <a type="button" href="edit_question.php?id=<?php safer_echo($r['id']); ?>">Edit Question</a>
                        <a type="button" href="view_question.php?id=<?php safer_echo($r['id']); ?>">View Question</a>
		        <a type="button" href="create_answer.php?id=<?php safer_echo($r['id']); ?>">Add Answer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>

