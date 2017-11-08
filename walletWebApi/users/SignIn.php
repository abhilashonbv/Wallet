<?php 
	include '../includes/database.php';
	include '../includes/userModel.php';
	include '../includes/userRestHandler.php';

if($_REQUEST['username']!='' && $_REQUEST['password']!='')
{

	$UserRestHandler = new UserRestHandler();
	$UserRestHandler->SignIn($_REQUEST);
}

?>

