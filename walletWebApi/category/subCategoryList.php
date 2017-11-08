<?php 
	include '../includes/database.php';
	include '../includes/categoryModel.php';
	include '../includes/categoryRestHandler.php';

if($_POST['catId']!='')
{

	$CategoryRestHandler = new CategoryRestHandler();
	$CategoryRestHandler->getSubCategoryList($_REQUEST);
}
?>

