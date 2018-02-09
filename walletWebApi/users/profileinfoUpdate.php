<?php 
	include '../includes/database.php';
	include '../includes/userModel.php';
	include '../includes/userRestHandler.php';

if($_REQUEST['userId']!='' && $_REQUEST['tokenId']!='' && $_REQUEST['first_name']!='' && $_REQUEST['last_name']!='' && $_REQUEST['profile_image']!='')
{

	$UserRestHandler = new UserRestHandler();
	$UserRestHandler->ProfileUpdate($_REQUEST);
}

?>

