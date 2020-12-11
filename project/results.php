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
    $stmt = $db->prepare("SELECT Surveys.id, title, description, user_id FROM Surveys WHERE
    Surveys.id = :survey_id");
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
    $stmt = $db->prepare("SELECT Responses.id as id, Responses.survey_id, Responses.question_id, answer, question Count(Responses.answer_id) as TOTAL FROM Responses JOIN Answers on Answers.id = Responses.answer_id JOIN Question on Responses.question_id = Question.id WHERE Responses.survey_id = :survey and Responses.user_id = :user");
    $r = $stmt->execute([":survey_id" => $s_id, ":user" => get_user_id()]);
    if($r){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>

<h3>Survey Responses</h3>

<?php if(isset($results) && !empty($results)): ?>
    <div class=”result”>
        <div class=”cardBody”>
            <div>
                <div>Title of Survey: <?php safer_echo($result["title"]); ?></div>
                <div>Description of Survey: <?php safer_echo($result["description"]); ?></div>
                //display survey questions
                <?php foreach($results as $r): ?>
                    <div>
                        <div>Question: <?php safer_echo($r["question"]); ?></div>
                    </div>  
                    <div>
                        <div>Answer: <?php safer_echo($r["answer"]); ?></div>
<div> Times Picked: <?php safer_echo($r["TOTAL"]); ?></div>
        </div>
    </div>
              <?php endforeach; ?>
        </div>
  <?php else: ?>
    <p>No results</p>
</div>
  <?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php");
