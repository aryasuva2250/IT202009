

<link rel="stylesheet" href="static/css/styles.css">
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav>
<ul class="nav">
    <li><a href="home.php">Home</a></li>
    <?php if(!is_logged_in()):?>
    <li><a href="login.php">Login</a></li>
    <li><a href="register.php">Register</a></li>
    <?php endif;?>
    <?php if(is_logged_in()): ?>
        <li><a href="create_survey.php">Create Survey</a></li>
	<li><a href="list_survey.php">View Survey</a></li>
	<li><a href="create_question.php">Create Question</a></li>
	<li><a href="list_question.php">View Question</a></li>
	<li><a href="all_surveys.php">Your Surveys</a></li>
	<li><a href="take_survey.php">Take Survey</a></li>
	<li><a href="surveys_taken.php">Taken Surveys</a></li>
        <li><a href="admin_edit_survey.php">Admin - Edit</a></li>
	<li><a href="admin_view_survey.php">Admin - View</a></li>
    <?php endif; ?>
    <?php if(is_logged_in()):?>
    <li><a href="profile.php">Profile</a></li>
    <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
</ul>
</nav>
