<?php
class ApplianceModel extends Database
{
    protected function appliance($request,$file)
    {

        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];
        $category = $request['category'];
        $subCategory  = $request['subCategory'];
        $manufacturer  = $request['manufacturer'];
        $model_no  = $request['model_no'];
        $serial_no  = $request['serial_no'];
        
        if($request['date_of_parched']){$date_of_parched  = $request['date_of_parched'];}else{$date_of_parched  = "";}
        if($request['location_tag']){$location_tag  = $request['location_tag'];}else{$location_tag  = "";}
        if($request['custom_location']){$custom_location  = $request['custom_location'];}else{$custom_location  = "";}
        if($request['name_tag']){$name_tag  = $request['name_tag'];}else{$name_tag  = "";}
        if($request['invoice_no']){$invoice_no  = $request['invoice_no'];}else{$invoice_no  = "";}
        if($request['purchased_from']){$purchased_from  = $request['purchased_from'];}else{$purchased_from  = "";}
        if($request['warranty_tenure']){$warranty_tenure  = $request['warranty_tenure'];}else{$warranty_tenure  = "";}
        if($request['carePack']){$carePack  = $request['carePack'];}else{$carePack  = "";}
        if($request['carePack_tenure']){$carePack_tenure  = $request['carePack_tenure'];}else{$carePack_tenure  = "";}
        date_default_timezone_set('Asia/Kolkata');
        $created_at      = date('Y-m-d h:i:s', time());
        $update_at      = date('Y-m-d h:i:s', time());

        if($this->verifyToken($userId,$tokenId)){
            
            $expiry_date = date('d-m-Y', strtotime($date_of_parched .'+'.$request['warranty_tenure'])); 

            if($file['invoice_images']){
            $invoice_images  = $file['invoice_images']['name'];
            $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/appliance_images/';
            $uploaddir = $baseUrl. basename($invoice_images);
            if (move_uploaded_file($file['invoice_images']['tmp_name'], $uploaddir)) {
            $img= "File is valid, and was successfully uploaded.";
            } else {
            $img= "Possible file upload attack!";
            }        
            }else{
                $invoice_images  = "NULL";
            } 
            
            if ($this->connect()->query("INSERT INTO `appliance`(`user_id`, `appliance_category`, `appliance_subCategory`, `appliance_manufacturer`, `appliance_make_no`, `appliance_serial_no`, `appliance_date_of_parched`, `aappliance_expiry_date`, `appliance_location_tag`, `appliance_custom_location`, `appliance_name_tag`, `appliance_invoice_no`,`appliance_purchased_from`, `appliance_warranty_tenure`, `appliance_carePack`, `appliance_carePack_tenure`,`appliance_images`, `appliance_created_at`, `appliance_update_at`) VALUES ('$userId','$category','$subCategory','$manufacturer','$model_no','$serial_no','$date_of_parched','$expiry_date','$location_tag','$custom_location','$name_tag','$invoice_no','$purchased_from','$warranty_tenure','$carePack','$carePack_tenure','$invoice_images','$created_at','$update_at')")) {
                               
                $result = '1';
                $msg    = "Appliance Successfully Registration";
                $imgMsg = $img;
                $status = '1';
            } else {
                $result = '0';
                $msg    = "Appliance Registration error.";
                $imgMsg = $img;
                $status = '0';
            }
        }else{
            $result = '0';
            $msg    = "Sorry token Not valid.";
            $imgMsg = '0';
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'imgMsg' => $imgMsg,
            'data' => $result
        );
    }

    protected function appliances($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];
       
        if($this->verifyToken($userId,$tokenId)){
            $sql = "SELECT * FROM `appliance` WHERE `user_id`='$userId'";
            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);
            
            if ($Numrows > 0) {

            while ($row = $result->fetch_assoc()) {

                $data[] = array('appliance_id' => $row['appliance_id'],'appliance_category' => $row['appliance_category'],'appliance_name_tag' => $row['appliance_name_tag'],'appliances_model' => $row['appliance_make_no'],'appliance_date_of_parched' => $row['appliance_date_of_parched'],'appliance_expiry_date' => $row['appliance_expiry_date']);
            }
                                      
                $result = $data;
                $msg    = "Appliance List";
                $status = '1';
            } else {
                $result = '0';
                $msg    = "Appliance List empty.";
                $status = '0';
            }
        }else{
            $result = '0';
            $msg    = "Appliance List empty.";
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
        );
    }

    protected function applianceScan($request,$file)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];
        date_default_timezone_set('Asia/Kolkata');
        $created_at      = date('Y-m-d h:i:s', time());
        $update_at      = date('Y-m-d h:i:s', time());
        if($this->verifyToken($userId,$tokenId)){

            $invoice_images  = $file['scan_appliance']['name'];
            $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/appliance_images/';
            $uploaddir = $baseUrl. basename($invoice_images);
            if (move_uploaded_file($file['scan_appliance']['tmp_name'], $uploaddir)) {
            $img= "File is valid, and was successfully uploaded.";
            } else {
            $img= "Possible file upload attack!";
            } 

            if($this->connect()->query("INSERT INTO `appliance`(`user_id`,`appliance_images`, `appliance_created_at`, `appliance_update_at`) VALUES ('$userId','$invoice_images','$created_at','$update_at')")){
                $result = '1';
                $msg    = "Appliance Successfully Scan Uploaded";
                $imgMsg = $img;
                $status = '1';  
            }else{
                $result = '0';
                $msg    = "Appliance Registration error";
                $imgMsg = $img;
                $status = '0';
            }
        }else{
            $result = '0';
            $msg    = "Sorry token Not valid.";
            $imgMsg = '0';
            $status = '0';
            
        }
        return array('status' => $status,'msg' => $msg,'imgMsg' => $imgMsg,'data' => $result); 
    }

    protected function applianceUpload($request,$file)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];
        date_default_timezone_set('Asia/Kolkata');
        $created_at      = date('Y-m-d h:i:s', time());
        $update_at      = date('Y-m-d h:i:s', time());
        if($this->verifyToken($userId,$tokenId)){

            $invoice_images  = $file['upload_appliance']['name'];
            $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/appliance_images/';
            $uploaddir = $baseUrl. basename($invoice_images);
            if (move_uploaded_file($file['upload_appliance']['tmp_name'], $uploaddir)) {
            $img= "File is valid, and was successfully uploaded.";
            } else {
            $img= "Possible file upload attack!";
            } 

            if($this->connect()->query("INSERT INTO `appliance`(`user_id`,`appliance_images`, `appliance_created_at`, `appliance_update_at`) VALUES ('$userId','$invoice_images','$created_at','$update_at')")){
                $result = '1';
                $msg    = "Appliance Successfully Uploaded";
                $imgMsg = $img;
                $status = '1';  
            }else{
                $result = '0';
                $msg    = "Appliance Registration error";
                $imgMsg = $img;
                $status = '0';
            }
        }else{
            $result = '0';
            $msg    = "Sorry token Not valid.";
            $imgMsg = '0';
            $status = '0';
            
        }
        return array('status' => $status,'msg' => $msg,'imgMsg' => $imgMsg,'data' => $result);       
    }

    protected function ApplianceDetails($request)
    {
        $userId  = $request['userId'];
        $applianceId  = $request['applianceId'];
        $tokenId    = $request['tokenId'];

        if($this->verifyToken($userId,$tokenId)){ 

            $sql = "SELECT * FROM `appliance` WHERE `appliance_id`='$applianceId'";
            $result  = $this->connect()->query($sql);
            while ($row = $result->fetch_assoc()) {
                $imageUrl = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/appliance_images/'.$row['appliance_images'];

                $data[] = array('appliance_id' => $row['appliance_id'],'appliance_category' => $row['appliance_category'],'appliance_name_tag' => $row['appliance_name_tag'],'appliances_model' => $row['appliance_make_no'],'appliances_serial' => $row['appliance_serial_no'],'appliance_date_of_parched' => $row['appliance_date_of_parched'],'appliance_expiry_date' => $row['appliance_expiry_date'],'appliance_invoice' => $row['appliance_invoice_no'],'appliance_document' => $imageUrl);
            }

            $result = $data;
            $msg    = "Appliance View Details List.";
            $status = '1';
        }else{

            $result = '0';
            $msg    = "Appliance View Details List Not Found.";
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
        );
    }
    
}
?>