<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<?php
if(isset($_GET["id"])){
    $s_id = $_GET["id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT Surveys.id, title, description, user_id FROM Surveys WHERE Surveys.id = :survey_id");
    $r = $stmt->execute([":survey_id" => $s_id]);
    if($r){
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
      flash("There was a problem fetching the results");
    }
}

?>
<?php

if(isset($_GET["id"])){
    $s_id = $_GET["id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT Responses.id as id, Responses.survey_id, Responses.question_id, answer, question FROM Responses JOIN Answers on Answers.id = Responses.answer_id JOIN Question on Responses.question_id = Question.id WHERE Responses.survey_id = :survey and Responses.user_id = :user");
    //$stmt = $db->prepare("SELECT q.id as QuestionId,a.id as AnswerId,s.title as title,s.description description,q.question as question,a.answer as answer,(SELECT count(distinct user_id) from Responses where answer_id = a.id) as Total FROM Question q JOIN Survey s on s.id = q.survey_id JOIN Answers a on q.id = a.question_id WHERE q.survey_id = :id group by q.id,a.id");
    $r = $stmt->execute([":survey_id" => $s_id, ":user" => get_user_id()]);
    //$r = $stmt->execute([":id" => $s_id]);
    if($r){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>

<h3>Survey Responses</h3>
<div class="results">
<?php if(isset($results) && !empty($results)):  ?>
    <div class=”result”>
        <div class=”cardBody”>
            <div>
                <div>Title of Survey: <?php safer_echo($r["title"]); ?></div>
                <div>Description of Survey: <?php safer_echo($r["description"]); ?></div>
             
                 <?php foreach($results as $r): ?>
		 <div>
                    <div>Question: <?php safer_echo($r["question"]); ?></div>
                </div>  
                <div>
                   <div>Answer: <?php safer_echo($r["answer"]); ?></div>
                   <div> Times Picked: <?php safer_echo($r["Total"]); ?></div>
			<div class="progress">
			    <div class="progress-bar bg-info" role="progressbar" style="width: <?php $r["Total"] ?>" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
                </div>
          </div>
	<?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>No results</p>
  <?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
