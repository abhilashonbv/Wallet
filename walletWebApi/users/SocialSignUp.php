<?php 
	include '../includes/database.php';
	include '../includes/userModel.php';
	include '../includes/userRestHandler.php';

if($_REQUEST['first_name']!='' && $_REQUEST['last_name']!='' && $_REQUEST['email']!='' && $_REQUEST['accessToken']!='' && $_REQUEST['socialType']!='')
{

	$UserRestHandler = new UserRestHandler();
	$UserRestHandler->SocialSignUp($_REQUEST);
}

?>
