<?php 
	include '../includes/database.php';
	include '../includes/applianceModel.php';
	include '../includes/applianceRestHandler.php';

if($_POST['userId']!='' && $_POST['tokenId']!='')
{

	$ApplianceRestHandler = new ApplianceRestHandler();
	$ApplianceRestHandler->ServiceLogList($_POST);
}else{

	$error =  array('status' => '0','msg' => 'Mention the all credantions. aaa');
	echo json_encode($error);
}

?>

