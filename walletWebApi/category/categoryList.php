<?php 
	include '../includes/database.php';
	include '../includes/categoryModel.php';
	include '../includes/categoryRestHandler.php';

	$CategoryRestHandler = new CategoryRestHandler();
	$CategoryRestHandler->getCategoryList();

?>

