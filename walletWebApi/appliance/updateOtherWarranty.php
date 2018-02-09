<?php 
	include '../includes/database.php';
	include '../includes/applianceModel.php';
	include '../includes/applianceRestHandler.php';

if($_POST['userId']!='' && $_POST['tokenId']!='' && $_POST['applianceId']!='' && $_POST['other_name_tag']!='' && $_POST['other_date_of_parched']!='' && $_POST['other_warranty_tenure']!='')
{

	$ApplianceRestHandler = new ApplianceRestHandler();
	$ApplianceRestHandler->UpdateOtherWarranty($_POST);
}else{

	$error =  array('status' => '0','msg' => 'Mention the all credantions.');
	echo json_encode($error);
}

?>
