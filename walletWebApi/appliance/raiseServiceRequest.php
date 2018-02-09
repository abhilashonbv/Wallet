<?php 
	include '../includes/database.php';
	include '../includes/applianceModel.php';
	include '../includes/applianceRestHandler.php';

if($_POST['userId']!='' && $_POST['tokenId']!='' && $_POST['applianceId']!='' && $_POST['description']!='')
{

	$ApplianceRestHandler = new ApplianceRestHandler();
	$ApplianceRestHandler->RaiseServiceRequest($_POST);
}else{

	$error =  array('status' => '0','msg' => 'Mention the all credantions.');
	echo json_encode($error);
}

?>
