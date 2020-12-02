<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("User")) {
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
    $stmt = $db->prepare("SELECT id,description,category,visibility,date,user_id,title from Surveys WHERE title like :q LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);    
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	//echo var_export($results,true);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>
<div class="listSurvey">
<form method="POST">
    <div class="listSearch">
    <label>Search For Survey</label>
    <input name="query" placeholder="Search" value="<?php safer_echo($query); ?>"/>
    </div>
    <div class="listSubmit">
    <input type="submit" value="Search" name="search"/>
    </div>
</form>
</div>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <div>
			<div class="listTitle">Title:</div>
			<div><?php safer_echo($r["title"]); ?></div>
		   </div>
		 <div>
                      		 <div class="listDescription">Description:</div>
			<div><?php safer_echo($r["description"]); ?></div>
                        </div>
		 <div>
                       		<div class="listCategory">Category:</div>
			<div><?php safer_echo($r["category"]); ?></div>
                    </div>
		   <div>
                       <div class="listVisibility">Visibility:</div>
		       <div><?php safer_echo($r["visibility"]); ?></div> 
                   </div>
		 <div> 
                       		<div class="listDate">Date:</div>
			<div><?php safer_echo($r["date"]); ?></div>
                    </div>
                    <div>
                        <div class="listOwner">Owner Id:</div>
                        <div><?php safer_echo($r["user_id"]); ?></div>
                    </div>
                    <div class="listButtons">
                        <a type="button" href="edit_survey.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                        <a type="button" href="view_survey.php?id=<?php safer_echo($r['id']); ?>">View</a>
			<a type="button" href="create_question.php?id=<?php safer_echo($r['id']); ?>">Add Question</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
