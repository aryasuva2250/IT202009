<?php
session_start();

session_unset();

session_destroy();
?>
<?php require_once(__DIR__ . "/partials/nav.php");
?>
<?php

flash("You have been logged out");
die(header("Location: login.php"));
?>
