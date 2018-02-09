<?php 
	include '../includes/database.php';
	include '../includes/applianceModel.php';
	include '../includes/applianceRestHandler.php';

if($_POST['userId']!='' && $_POST['tokenId']!='' && $_POST['image_extension']!='')
{

	$ApplianceRestHandler = new ApplianceRestHandler();
	$ApplianceRestHandler->ScanAppliance($_POST);
}else{

	$error =  array('status' => $_POST,'msg' => 'Mention the all credantions.');
	echo json_encode($error);
}

?>
