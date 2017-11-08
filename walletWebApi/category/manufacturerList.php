<?php 
	include '../includes/database.php';
	include '../includes/categoryModel.php';
	include '../includes/categoryRestHandler.php';

if($_POST['subCatId']!='')
{

	$CategoryRestHandler = new CategoryRestHandler();
	$CategoryRestHandler->getManufacturerList($_REQUEST);
}
?>

