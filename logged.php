<?php
	session_start();
	include("header.php");
	include("navbar.php");
	if ($_SESSION['email']){
		echo '
		<div class="container">
		<p class="p-3 mb-2 bg-success text-white">You are successfully logged in as ' .$_SESSION['email']. '.</p>
		</div>';
	} else {
		header("Location: index.php");
	}
	include("footer.php");
?>