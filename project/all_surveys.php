<?php 
//user's surveys
?>
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
$stmt = $db->prepare("SELECT id, title, description, category, visibility, user_id from Surveys WHERE user_id = :id LIMIT 10");

$r = $stmt->execute([":id" => get_user_id()]);
if($r){
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else{
    flash("There was a problem fetching results");
}
$count = 0;
if(isset($results)){
    $count = count($results);
}
?>
<div class="AllYourSurveys">
<h3>All Your Surveys</h3>
</div>
<div class=”result”>
    <?php if (count($results) > 0): ?>
        <div class="list-group">
	     <?php foreach ($results as $s): ?>
	         <div class="list-group-item">
		     <div class="YourSurveysTitle">
		         <div> Title: </div> 
		     </div>
		     <div class="YourSurveysTitleResult">
			 <?php safer_echo($s["title"]); ?>
		     </div>
		     <div class="YourSurveysOwner">
		         <div>Owner: </div> 
		     </div>
		     <div class="YourSurveysOwnerResult">
			 <?php safer_echo($s["user_id"]); ?> 
		     </div>
		     <div class="YourSurveysCategory">
		        <div>Category: </div>
		     </div>
		     <div class="YourSurveysCategoryResult">
			<?php safer_echo($s["category"]);?>
		     </div>
		     <div class="YourSurveysVisibility">
		         <div>Visibility: </div> 
		     </div>
		     <div class="YourSurveysVisibilityResult">
			 <?php safer_echo($s["visibility"]);?>
		     </div>
		</div>
		<a type="button" href="create_question.php?id=<?php safer_echo($s['id']); ?>">Add Question</a>
	    <?php endforeach; ?>
	   </div>
    <?php else: ?>
        <p>No Surveys</p>
    <?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php");

