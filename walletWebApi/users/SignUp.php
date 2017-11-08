<?php 
	include '../includes/database.php';
	include '../includes/userModel.php';
	include '../includes/userRestHandler.php';

if($_REQUEST['username']!='' && $_REQUEST['countryCode']!='' && $_REQUEST['mobile']!='' && $_REQUEST['password']!='')
{

	$UserRestHandler = new UserRestHandler();
	$UserRestHandler->SignUp($_REQUEST);
}

?>
