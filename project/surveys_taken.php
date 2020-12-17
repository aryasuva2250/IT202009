<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$page = 1;
$per_page = 10;
if(isset($_GET["page"])){
    try{
        $page = (int)$_GET["page"];
    }
    catch(Exception $e){
    }
}
$db = getDB();
$result = [];
$stmt = $db->prepare("SELECT title, Count(Responses.survey_id) as TOTAL from Responses JOIN Surveys ON Responses.survey_id = Surveys.id WHERE Responses.user_id=:id GROUP BY title LIMIT 10");
//$stmt = $db->prepare("SELECT count(*) as total from Surveys s join Responses r on s.id = r.survey_id where r.user_id = :id");
//$stmt = $db->prepare("SELECT s.title FROM Surveys s JOIN Responses r on s.id = r.survey_id where r.user_id = :sid LIMIT 10"); 
$r = $stmt->execute([":id" => get_user_id()]);
//$r = $stmt->execute([":sid" => $sessionid]);
//$result = $stmt->fetch(PDO::FETCH_ASSOC);
$t = 0;
if($r){
    //$t = (int)$result["total"];
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
else {
    flash("There was a problem fetching the results");// . var_export($stmt->errorInfo(), true));
}
$total = ceil($t / $per_page);
$offset = ($page-1) * $per_page;
$stmt = $db->prepare("SELECT s.* from Surveys s join Responses r on s.id = r.survey_id where r.user_id = :id ORDER BY id DESC LIMIT :offset, :count");
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
$stmt->bindValue(":id", get_user_id());
$stmt->execute();
$e = $stmt->errorInfo();
if($e[0] != "00000"){
    flash(var_export($e, true), "alert");
}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<nav aria-label="Surveys Taken By You">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
            <a class="page-link" href="?page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
	</li>
	<?php for($i = 0; $i < $total; $i++):?>
	<li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
        <?php endfor; ?>
	<li class="page-item <?php echo ($page) >= $total_pages?"disabled":"";?>">
	    <a class="page-link" href="?page=<?php echo $page+1;?>">Next</a>
	</li>
    </ul>
</nav>
<?php require(__DIR__ . "/partials/flash.php"); ?>
