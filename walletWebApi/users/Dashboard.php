<?php 
	include '../includes/database.php';
	include '../includes/userModel.php';
	include '../includes/userRestHandler.php';

if($_REQUEST['userId']!='' && $_REQUEST['tokenId']!='')
{

	$UserRestHandler = new UserRestHandler();
	$UserRestHandler->Dashboard($_REQUEST);
}

?>

