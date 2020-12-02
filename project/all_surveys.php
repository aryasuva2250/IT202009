<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("User")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$results = [];
$db = getDB();
$stmt = $db->prepare("SELECT id, title, description, category, visibility, FROM Surveys where user_id = :id ORDER BY created LIMIT 10");

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
<div class=”container-fluid”>
    <h3>All Surveys (<?php echo $count; ?>)</h3>
    <div class="list-group">
        <?php if (isset($results) && $count > 0): ?>
	     <div class="list-group-item">
	         <div class="row">
		     <div class="col-8">Title</div>
		     <div class="col-5">Description</div>
		     <div class="col-2">Category</div>
		     <div class="col-2">Visibility</div>
		 </div>
	     </div>
	     <?php foreach ($results as $s): ?>
	         <div class="list-group-item">
		      <div class="row">
		          <div class="col-8"><?php safer_echo($s["title"]); ?></div>
		         <div class=”col-5”><?php safer_echo($s["description"]); ?></div>
		         <div class=”col-2”><?php safer_echo($s["category"]); ?></div>
		        <div class=”col-2”><?php getState($s["visibility"]); ?></div>
		</div>
	   </div>
<?php endforeach; ?>
</div>
<form method="POST">
	<input type="submit" name="results" value="Results Page"/>
</form>
<?php else: ?>
    <p>No Surveys.</p>
<?php endif; ?>
<?php require(__DIR__ . "/partials/flash.php");

