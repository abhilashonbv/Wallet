<?php
class ApplianceModel extends Database
{
    private $exp = "Oops! Your session expired, you might have logged in other device. Pls login again";
    protected function applianceSave($request)
    {
        $userId  = trim($request['userId']);
        $tokenId    = trim($request['tokenId']);
        $category = trim($request['category']);
        $model_no  = trim($request['model_no']);
        $serial_no  = trim($request['serial_no']);    
        $appliance_mode='0';
        
        if($request['date_of_parched']){$date_of_parched = trim($request['date_of_parched']);}else{$date_of_parched  = "NULL";}
        if($request['location_tag']){$location_tag  = trim($request['location_tag']);}else{$location_tag  = "";}
        if($request['custom_location']){$custom_location = trim($request['custom_location']);}else{$custom_location  = "";}
        if($request['name_tag']){$name_tag  = trim($request['name_tag']);}else{$name_tag  = "";}
        if($request['invoice_no']){$invoice_no  = trim($request['invoice_no']);}else{$invoice_no  = "";}
        if($request['purchased_from']){$purchased_from  = trim($request['purchased_from']);}else{$purchased_from  = "";}
        if($request['warranty_tenure']){$warranty_tenure = trim($request['warranty_tenure']);}else{$warranty_tenure  = "";}
        if($request['carePack']){$carePack  = trim($request['carePack']);}else{$carePack  = "";}
        if($request['carePack_tenure']){$carePack_tenure  = trim($request['carePack_tenure']);}else{$carePack_tenure  = "";}
        date_default_timezone_set('Asia/Kolkata');
        $created_at      = date('Y-m-d h:i:s', time());
        $update_at      = date('Y-m-d h:i:s', time());

        if($this->verifyToken($userId,$tokenId)){

            if($request['subCategory']=='Custom'){
                $catsql     = "SELECT * FROM `category` WHERE `cotegory_name`='$category'";
                $catresult  = $this->connect()->query($catsql);
                $catrow = $catresult->fetch_assoc();

                $categoryId = trim($catrow['cotegory_id']);
                $custom_value_subCategory  = trim($request['custom_value_subCategory']);

                $this->connect()->query("INSERT INTO `sub_category`(`category_id`, `subcategory_name`) VALUES ('$categoryId','$custom_value_subCategory')");
                $subCategory = $custom_value_subCategory;   
            }else{
                $subCategory  = $request['subCategory'];
            }

            if($request['manufacturer']=='Custom'){
                $subsql     = "SELECT * FROM `sub_category` WHERE `subcategory_name`='$subCategory'";
                $subresult  = $this->connect()->query($subsql);
                $subrow = $subresult->fetch_assoc();

                $manufacturer_subcat_id = trim($subrow['subcategory_id']);
                $custom_value_manufacturer  = trim($request['custom_value_manufacturer']);

                $this->connect()->query("INSERT INTO `manufacturer`(`manufacturer_subcat_id`, `manufacturer_name`) VALUES ('$manufacturer_subcat_id','$custom_value_manufacturer')");

                $manufacturer = $custom_value_manufacturer; 
            }else{
                $manufacturer = trim($request['manufacturer']);
            }

            $expiry_date = date('d-m-Y', strtotime($date_of_parched .'+'.$request['warranty_tenure']));
            if($request['appliance_document']){
                $decode_image = base64_decode($request['appliance_document']);         
                $imageName = 'document_'.rand().$request['image_extension'].',';
                $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/appliance_images/';
                $user_dir_path  = $baseUrl . '/' . $imageName;
                $file      = fopen($user_dir_path, 'wb');
                fwrite($file, $decode_image);
                fclose($file); 
            }else{
                $imageName = "";
            }

           $date_of_parched1 = $this->ChangeDateYMD($date_of_parched);
           $expiry_date1 = $this->ChangeDateYMD($expiry_date);

           
            if ($this->connect()->query("INSERT INTO `appliance`(`user_id`, `appliance_category`, `appliance_subCategory`, `appliance_manufacturer`, `appliance_model_no`, `appliance_serial_no`, `appliance_date_of_parched`, `appliance_warranty_tenure`,`appliance_expiry_date`, `appliance_location_tag`, `appliance_custom_location`, `appliance_name_tag`, `appliance_invoice_no`, `appliance_purchased_from`, `appliance_carePack`, `appliance_carePack_tenure`,`appliance_images`, `appliance_mode`, `appliance_created_at`, `appliance_update_at`) VALUES ('$userId','$category','$subCategory','$manufacturer','$model_no','$serial_no','$date_of_parched1','$warranty_tenure','$expiry_date1','$location_tag','$custom_location','$name_tag','$invoice_no','$purchased_from','$carePack','$carePack_tenure','$imageName','$appliance_mode','$created_at','$update_at')")) {
             
                $result = '1';
                $msg    = "Appliance Added Successfully.";
                $status = '1';
            } else {
                $result = '0';
                $msg    = "Appliance Not Added.";
                $status = '0';
            }
            fclose($file);
        }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
        );
    }

    protected function applianceDocumentUpload($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];
        $applianceId = $request['applianceId'];
        $upload_document = $request['upload_document'];

        date_default_timezone_set('Asia/Kolkata');
        $update_at      = date('Y-m-d h:i:s', time());
        if($this->verifyToken($userId,$tokenId)){

        $decode_image = base64_decode($upload_document);
        $imageName = 'document_'.rand().$request['image_extension'];
        $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/appliance_images/';
        $user_dir_path  = $baseUrl . '/' . $imageName;
        $file      = fopen($user_dir_path, 'wb');

        $sql = "SELECT * FROM `appliance` WHERE `appliance_id`='$applianceId'";
        $result  = $this->connect()->query($sql);
        $Numrows = mysqli_num_rows($result);

        if ($Numrows > 0) {
            $row = $result->fetch_assoc();
            $images = $row['appliance_images'];
            $imageName1 = $images.$imageName.',';
        }
        if(fwrite($file, $decode_image)){
            if($this->connect()->query("UPDATE `appliance` SET `appliance_images`='$imageName1',`appliance_update_at`='$update_at' WHERE `appliance_id`='$applianceId'")){

                $result = '1';
                $msg    = "Appliance Document Uploaded Successfully.";
                $status = '1';  
            }else{
                $result = '0';
                $msg    = "Appliance Not Uploaded";
                $status = '0';
            }  
        }
        fclose($file); 
        }else{

            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
   
        return array('status' => $status,'msg' => $msg,'data' => $result);       
    }

    protected function applianceScan($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];
        $tagname    = $request['tagname'];
        date_default_timezone_set('Asia/Kolkata');
        $created_at      = date('Y-m-d h:i:s', time());
        $update_at      = date('Y-m-d h:i:s', time());

        $appliance_mode='1';
        if($this->verifyToken($userId,$tokenId)){

            $decode_image = base64_decode($upload_document);
            $imageName = 'document_'.rand().$request['image_extension'];
            $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/appliance_images/';
            $user_dir_path  = $baseUrl . '/' . $imageName;
            $file      = fopen($user_dir_path, 'wb');

            if($request['scan_appliance']!=''){
                $scan_appliance = $request['scan_appliance'];
                $decode_image = base64_decode($scan_appliance);
                $imageName = 'scan_'.rand().$request['image_extension'];
                $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/appliance_images/';
                $user_dir_path  = $baseUrl . '/' . $imageName;
                $file      = fopen($user_dir_path, 'wb');
            }else{
                $upload_appliance = $request['upload_appliance'];
                $decode_image = base64_decode($upload_appliance); 
                $imageName = 'upload_'.rand().$request['image_extension'];
                $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/appliance_images/';
                $user_dir_path  = $baseUrl . '/' . $imageName;
                $file      = fopen($user_dir_path, 'wb');
            }         
            
            if(fwrite($file, $decode_image)){
                $image = $imageName.',';
                if($this->connect()->query("INSERT INTO `appliance`(`user_id`,`appliance_images`, `appliance_name_tag`, `appliance_mode`,`appliance_created_at`, `appliance_update_at`) VALUES ('$userId','$image','$tagname','$appliance_mode','$created_at','$update_at')")){
                    $result = '1';
                    $msg    = "Appliance Added Successfully.";
                    $status = '1';  
                }else{
                    $result = '0 ';
                    $msg    = "Appliance Not Uploaded.";
                    $status = '0';
                } 
                fclose($file); 
            }else{
                $result = '0';
                $msg    = "Image Not Uploaded";
                $status = '0';
            }
           
        }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
            
        }
        return array('status' => $status,'msg' => $msg,'data' => $result);  
    }

    protected function listAppliance($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];
        $catId    = $request['catId'];
        $cat_location    = $request['cat_location'];
       
        if($this->verifyToken($userId,$tokenId)){

            if($request['catId']){
                $sql = "SELECT * FROM `appliance` WHERE `user_id`='$userId' AND `appliance_subCategory`='$catId' AND `appliance_is_deleted`='0' AND `appliance_location_tag`='$cat_location' ORDER BY appliance_name_tag"; //DESC
            }else{
                 $sql = "SELECT * FROM `appliance` WHERE `user_id`='$userId' AND `appliance_is_deleted`='0' AND `appliance_category`!='' ORDER BY appliance_name_tag"; //DESC   
            }
            //$sql = "SELECT * FROM `appliance` WHERE `user_id`='$userId' AND `appliance_is_deleted`='0' AND `appliance_category`!='' ORDER BY appliance_name_tag"; //DESC
            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);
            
            if ($Numrows > 0) {

            while ($row = $result->fetch_assoc()) { 

                if($row['appliance_expiry_status']=='0'){ 
                    $applianceId = $row['appliance_id'];
                    $date_of_parched = $row['appliance_date_of_parched'];
                    $expiry_date = $row['appliance_expiry_date'];
                }else{
                    $applianceId = $row['appliance_id'];
                    $sqlext = "SELECT * FROM `extend_warranty` WHERE `user_id`='$userId' AND `appliance_Id`='$applianceId'";
                    $resultext  = $this->connect()->query($sqlext);
                    $rowext = $resultext->fetch_assoc();

                    $date_of_parched = $rowext['extend_warranty_date'];
                    $expiry_date = $rowext['extend_warranty_expire'];
                }

            $brand_warranty_url = $this->getNameById($row['appliance_subCategory'],$row['appliance_manufacturer']);
            
            $date_of_parched1 = $this->ChangeDateDMY($date_of_parched);
            $expiry_date1 = $this->ChangeDateDMY($expiry_date);

            $data[] = array('appliance_id' => $row['appliance_id'],'appliance_category' => ucwords($row['appliance_category']),'appliance_subCategory' => ucwords($row['appliance_subCategory']),'appliance_name_tag' => ucwords($row['appliance_name_tag']),'appliance_location_tag' => ucwords($row['appliance_location_tag']),'appliance_custom_location' => ucwords($row['appliance_custom_location']),'appliances_model' => $row['appliance_model_no'],'appliance_serial' => $row['appliance_serial_no'],'appliance_date_of_parched' => $date_of_parched1,'appliance_expiry_date' => $expiry_date1,'brand_warranty_url'=>$brand_warranty_url);
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
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
           
        );
    }

    protected function applianceViewDetails($request)
    {
        $userId  = $request['userId'];
        $applianceId  = $request['applianceId'];
        $tokenId    = $request['tokenId'];

        if($this->verifyToken($userId,$tokenId)){ 

            $sql = "SELECT * FROM `appliance` WHERE `appliance_id`='$applianceId'";
            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);
             if ($Numrows > 0) {
            $row = $result->fetch_assoc();

                if(!empty($row['appliance_images'])){
                    $images = explode( ',', $row['appliance_images']);
                    array_pop($images);
                    $count1 = count($images);

                    for ($i=0; $i < $count1; $i++) { 
                        $img = $i+1;
                        $image[] = array('document' =>'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/appliance_images/'.$images[$i],'document_name' =>$images[$i]);
                    }
                }else{
                    $image =array();
                }

                if($row['appliance_brand_logo']!='null'){$brand_logo = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/brand_logo/'.$row['appliance_brand_logo'];}else{$brand_logo = '';}

                    $name_tag = explode( ',', $row['other_name_tag']);
                    array_pop($name_tag);
                    $count = count($name_tag);
                    $date_of_parched = explode( ',', $row['other_date_of_parched']);
                    array_pop($date_of_parched);
                    $expiry_date = explode( ',', $row['other_expiry_date']);
                    array_pop($expiry_date);
                    for ($i=0; $i < $count; $i++) { 
                        $other[] = array('other_name_tag' =>$name_tag[$i] ,'other_date_of_parched' => $this->ChangeDateDMY($date_of_parched[$i]),'other_expiry_date' =>$this->ChangeDateDMY($expiry_date[$i]));
                    }
                    if(empty($other)){$other=array();}

               
                    $date_of_parched = $this->ChangeDateDMY($row['appliance_date_of_parched']);
                    $expiry_date = $this->ChangeDateDMY($row['appliance_expiry_date']);

                $data[] = array('appliance_id' => $row['appliance_id'],'appliance_category' => ucwords($row['appliance_category']),'appliance_name_tag' => ucwords($row['appliance_name_tag']),'appliance_capacity' => $row['appliance_capacity'],'appliance_manufacturer' => ucwords($row['appliance_manufacturer']),'appliances_model' => $row['appliance_model_no'],'appliances_serial' => $row['appliance_serial_no'],'appliance_date_of_parched' => $date_of_parched,'appliance_expiry_date' => $expiry_date,'appliance_invoice' => $row['appliance_invoice_no'],'brand_logo'=>$brand_logo,'appliance_document' => $image,'other' => $other);
         

            $result = $data;
            $msg    = "Appliance View Details List.";
            $status = '1';
           
        }else{

            $result = '0';
            $msg    = "Appliance View Details List Not Found.";
            $status = '0';
            
        }
       }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        } 
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result,
            
        );
    }

    protected function deleteAppliance($request)
    {
        $userId  = $request['userId'];
        $applianceId  = $request['applianceId'];
        $tokenId    = $request['tokenId'];

        if($this->verifyToken($userId,$tokenId)){ 
            
            if($this->connect()->query("UPDATE `appliance` SET `appliance_is_deleted`='1' WHERE `appliance_id`='$applianceId'")){
                $result = '1';
                $msg    = "Appliance deleted successfully.";
                $status = '1';
            }else{
                $result = '0';
                $msg    = "sorry appliance not delete.";
                $status = '0';    
            }
        }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        } 
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result,
            
        );
    }
  
    protected function applianceUpdate($request)
    {
        $userId  = trim($request['userId']);
        $tokenId    = trim($request['tokenId']);
        $applianceId = trim($request['applianceId']);
        
        if($request['category']){$category  = trim($request['category']);}else{$category  = "";}
        if($request['manufacturer']){$manufacturer  = trim($request['manufacturer']);}else{$manufacturer  = "";}
        if($request['capacity']){$capacity  = trim($request['capacity']);}else{$capacity  = "";}
        if($request['name_tag']){$name_tag  = trim($request['name_tag']);}else{$name_tag  = "";}
        if($request['model_no']){$model_no  = trim($request['model_no']);}else{$model_no  = "";}
        if($request['serial_no']){$serial_no  = trim($request['serial_no']);}else{$serial_no  = "";}
        if($request['date_of_parched']){$date_of_parched  = trim($request['date_of_parched']);}else{$date_of_parched  = "";}
        if($request['expiry_date']){$expiry_date  = trim($request['expiry_date']);}else{$expiry_date  = "";}
        if($request['invoice_no']){$invoice_no  = trim($request['invoice_no']);}else{$invoice_no  = "";}

        
        date_default_timezone_set('Asia/Kolkata');
        $update_at      = date('Y-m-d h:i:s', time());

        if($this->verifyToken($userId,$tokenId)){

        $date_of_parched1 = $this->ChangeDateYMD($date_of_parched);
        $expiry_date1 = $this->ChangeDateYMD($expiry_date);

            $sql = "SELECT * FROM `appliance` WHERE `appliance_id`='$applianceId'";
            $result  = $this->connect()->query($sql);
            while ($row = $result->fetch_assoc()) { $data=$row;
                }
                if($data['appliance_expiry_status']=='0'){
                    $updateValue =$this->connect()->query("UPDATE `appliance` SET `appliance_category`='$category',`appliance_manufacturer`='$manufacturer',`appliance_model_no`='$model_no',`appliance_serial_no`='$serial_no',`appliance_capacity`='$capacity',`appliance_date_of_parched`='$date_of_parched1',`appliance_expiry_date`='$expiry_date1',`appliance_name_tag`='$name_tag',`appliance_invoice_no`='$invoice_no',`appliance_update_at`='$update_at' WHERE `appliance_id`='$applianceId'");
                }else{
                    $updateValue = $this->connect()->query("UPDATE `appliance` SET `appliance_category`='$category',`appliance_manufacturer`='$manufacturer',`appliance_model_no`='$model_no',`appliance_serial_no`='$serial_no',`appliance_capacity`='$capacity',`appliance_name_tag`='$name_tag',`appliance_invoice_no`='$invoice_no',`appliance_update_at`='$update_at' WHERE `appliance_id`='$applianceId'");

                    $extendValue = $this->connect()->query("UPDATE `extend_warranty` SET `extend_warranty_date`='$date_of_parched1',`extend_warranty_expire`='$expiry_date1',`extend_created_at`='$update_at' WHERE `appliance_Id`='$applianceId'");
                }
                    if($updateValue) {           
                    $result = '1';
                    $msg    = "Appliance Successfully Updated";
                    $status = '1';
                    } else {
                        $result = '0';
                        $msg    = "Appliance Not Updated.";
                        $status = '0';
                    } 
        }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
        );
    }

    protected function applianceInvoiceList($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];
        
        if($this->verifyToken($userId,$tokenId)){

            $sql = "SELECT * FROM `appliance` WHERE `user_id`='$userId'AND `appliance_mode`='1' AND `appliance_category`!=''";
            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);
            if ($Numrows > 0) {
                while ($row = $result->fetch_assoc()) {

                if(!empty($row['appliance_images'])){$imageUrl = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/appliance_images/'.$row['appliance_images'];}else{$imageUrl = '';}

                if($row['appliance_brand_logo']!='null'){$brand_logo = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/brand_logo/'.$row['appliance_brand_logo'];}else{$brand_logo = '';}


                $date_of_parched = $this->ChangeDateDMY($row['appliance_date_of_parched']);
                $expiry_date = $this->ChangeDateDMY($row['appliance_expiry_date']);

                $data[] = array('appliance_id' => $row['appliance_id'],'appliance_category' => ucwords($row['appliance_category']),'appliance_name_tag' => ucwords($row['appliance_name_tag']),'appliance_manufacturer' => ucwords($row['appliance_manufacturer']),'appliances_model' => $row['appliance_model_no'],'appliances_serial' => $row['appliance_serial_no'],'appliance_date_of_parched' => $date_of_parched,'appliance_expiry_date' => $expiry_date,'brand_logo'=>$brand_logo,'appliance_document' => $imageUrl);
                }     
                $result = $data;
                $msg    = "Appliance Invoice List";
                $status = '1';
            }else{
                $result = '0';
                $msg    = "Appliance Invoice empty.";
                $status = '0';
            }
        }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
        );
    }

    protected function extendApplianceWarranty($request)
    {
        $userId  = trim($request['userId']);
        $tokenId    = trim($request['tokenId']);
        $applianceId  = trim($request['applianceId']);
        $productName  = trim($request['product_name']);
        $warrantyTenure    = trim($request['warranty_tenure']);
        $warrantyType  = trim($request['warranty_type']);
        date_default_timezone_set('Asia/Kolkata');
        $created_at      = date('Y-m-d', time());

        $created_at1      = date('Y-m-d');

        if($this->verifyToken($userId,$tokenId)){ 

            $expiry_date = date('d-m-Y', strtotime($created_at .'+'.$request['warranty_tenure'])); 

                $expiry_date1 = $this->ChangeDateYMD($expiry_date);

              if ($this->connect()->query("INSERT INTO `extend_warranty`(`user_id`,`appliance_id`, `product_name`, `warranty_tenure`, `warranty_type`, `extend_warranty_date`, `extend_warranty_expire`, `extend_created_at`) VALUES ('$userId','$applianceId','$productName','$warrantyTenure','$warrantyType','$created_at1','$expiry_date1','$created_at')")) {

                $this->connect()->query("UPDATE `appliance` SET `appliance_expiry_status`='1' WHERE `appliance_id`='$applianceId'");
                               
                $result = '1';
                $msg    = "Appliance Extend Warranty Successfully Submitted";
                $status = '1';
            } else {
                $result = '0';
                $msg    = "Appliance Extend Warranty Not Submitted.";
                $status = '0';
            }
            return array(
                'status' => $status,
                'msg' => $msg,
                'data' => $result
            );
        }
    } 

    protected function extendApplianceWarrantyList($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];

        if($this->verifyToken($userId,$tokenId)){ 

            $sql = "SELECT * FROM `extend_warranty` WHERE `user_id`='$userId'";
            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);
            
            if ($Numrows > 0) {

            while ($row = $result->fetch_assoc()) { 

                $date_of_parched = $this->ChangeDateDMY($row['extend_warranty_date']);
                $expiry_date = $this->ChangeDateDMY($row['extend_warranty_expire']);

                $data[] = array('appliance_id' => $row['appliance_Id'],'product_name' => ucwords($row['product_name']),'warranty_tenure' => $row['warranty_tenure'],'warranty_type' => $row['warranty_type'],'extend_warranty_date' => $date_of_parched,'extend_warranty_expire' => $expiry_date);
            }
                               
                $result = $data;
                $msg    = "Appliance Extend Warranty List";
                $status = '1';
            } else {
                $result = '0';
                $msg    = "Sorry No Data empty.";
                $status = '0';
            }
            return array(
                'status' => $status,
                'msg' => $msg,
                'data' => $result
            );
        }
    }

    protected function getServiceLog($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];

        if($this->verifyToken($userId,$tokenId)){ 

            $sql1 = "SELECT * FROM `amc_registered` WHERE `user_id`='$userId'";
            $result1  = $this->connect()->query($sql1);
            $Numrows1 = mysqli_num_rows($result1);

            while ($row1 = $result1->fetch_assoc()) {
                if(!empty($row1['amc_document'])){$docUrl = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/amc_images/'.$row1['amc_document'];}else{$docUrl = '';}

                $date_of_parched = $this->ChangeDateDMY($row1['amc_date_of_parched']);
                $expiry_date = $this->ChangeDateDMY($row1['amc_expiry_date']);

            $data1[] = array('service_log_id' => $row1['amc_id'],'user_id' => $row1['user_id'],'appliance_id' => $row1['appliance_id'],'service_type' => $row1['service_type'],'service_tag_name' => ucwords($row1['amc_tag_name']),'service_model' => $row1['amc_model'],'date_of_parched' => $date_of_parched,'expiry_date' => $expiry_date,'service_date' => $row1['amc_date_of_parched'],'service_assistance_name' =>'','service_assistance_company' =>'','service_assistance_number'=>'','service_document' => $docUrl);  
            }

            $sql2 = "SELECT * FROM `raise_service` WHERE `user_id`='$userId'";
            $result2  = $this->connect()->query($sql2);
            $Numrows2 = mysqli_num_rows($result2);

            while ($row2 = $result2->fetch_assoc()) {

                if(!empty($row2['service_document'])){$imageUrl = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/appliance_images/'.$row2['service_document'];}else{$imageUrl = '';}
                
                $date_of_parched1 = $this->ChangeDateDMY($row2['date_of_parched']);
                $expiry_date1 = $this->ChangeDateDMY($row2['expiry_date']);

            $data2[] = array('service_log_id' => $row2['service_id'],'user_id' => $row2['user_id'],'appliance_id' => $row2['applianceId'],'service_type' => $row2['service_type'],'service_tag_name' => ucwords($row2['raise_tag_name']),'service_model' => $row2['raise_model'],'date_of_parched' => $date_of_parched1,'expiry_date' => $expiry_date1,'service_date' => $row2['preffered_schedule'],'service_assistance_name' =>'','service_assistance_company' =>'','service_assistance_number'=>'','service_document' => '$imageUrl');  
            }

            $sql = "SELECT * FROM `service_log` WHERE `user_id`='$userId'";
            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);

            while ($row = $result->fetch_assoc()) {

            if(!empty($row['service_document'])){$imageUrl = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/appliance_images/'.$row['service_document'];}else{$imageUrl = '';}

                $date_of_parched2 = $this->ChangeDateDMY($row['date_of_parched']);
                $expiry_date2 = $this->ChangeDateDMY($row['expiry_date']);

            $data[] = array('service_log_id' => $row['service_log_id'],'user_id' => $row['user_id'],'appliance_id' => $row['appliance_id'],'service_type' => $row['service_type'],'service_tag_name' => ucwords($row['service_tag_name']),'service_model' => $row['service_model'],'service_date' => $row['service_date'],'date_of_parched' => $date_of_parched2,'expiry_date' => $expiry_date2,'service_assistance_name' => ucwords($row['service_assistance_name']),'service_assistance_company' => ucwords($row['service_assistance_company']),'service_assistance_number'=>$row['service_assistance_number'],'service_document' => $imageUrl);
            }

            if (($Numrows > 0) || ($Numrows1 > 0) || ($Numrows2 > 0)) {
            
            if (!empty($data)){ $adata = $data; }else{ $adata= array();}
            if (!empty($data1)){ $adata1 = $data1; }else{ $adata1= array();}
            if (!empty($data2)){ $adata2 = $data2; }else{ $adata2= array();}

            $dc = array_merge($adata,$adata1,$adata2);
                $result = $dc;
                $msg    = "Service Log List.";
                $status = '1';
            }else{
                $result = '0';
                $msg    = "No Service Logged.";
                $status = '0';
            }
         }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array('status' => $status,'msg' => $msg,'data' => $result);
    }

    protected function amcSave($request)
    {
        $userId  = trim($request['userId']);
        $tokenId    = trim($request['tokenId']); 
        $applianceId    = trim($request['applianceId']);
        $amc_tenure    = trim($request['amc_tenure']);
        $amc_start_date    = trim($request['amc_start_date']);
        $amc_provider = trim($request['amc_provider']);


        if($request['amc_provider_number']){$amc_provider_number  = $request['amc_provider_number'];}else{$amc_provider_number  = "";}
        
        if($this->verifyToken($userId,$tokenId)){ 

            $sql = "SELECT * FROM `appliance` WHERE `appliance_id`='$applianceId'";
            $result  = $this->connect()->query($sql);
            $row = $result->fetch_assoc();

            $amc_tag_name = $row['appliance_name_tag'];
            $amc_model = $row['appliance_model_no'];
            $amc_serial = $row['appliance_serial_no'];


            if($request['amc_document']){
                $decode_image = base64_decode($request['amc_document']);         
                $imageName = 'amc_'.rand().$request['image_extension'];
                $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/amc_images/';
                $user_dir_path  = $baseUrl . '/' . $imageName;
                $file      = fopen($user_dir_path, 'wb');
                fwrite($file, $decode_image);
                fclose($file); 
            }else{
                $imageName = "";
            }
            
            $expiry_date = date('d-m-Y', strtotime($amc_start_date .'+'.$request['amc_tenure']));

                $amc_start_date1 = $this->ChangeDateYMD($amc_start_date);
                $expiry_date1 = $this->ChangeDateYMD($expiry_date);

                if( $this->connect()->query("INSERT INTO `amc_registered`(`user_id`, `appliance_id`, `amc_tag_name`, `amc_model`, `amc_serial`, `amc_date_of_parched`, `amc_tenure`, `amc_expiry_date`, `amc_provider_name`, `amc_provider_number`, `amc_document`) VALUES ('$userId','$applianceId','$amc_tag_name','$amc_model','$amc_serial','$amc_start_date1','$amc_tenure','$expiry_date1','$amc_provider','$amc_provider_number','$imageName')"))
                {

                    $result = '1';
                    $msg    = "Amc Successfully Added.";
                    $status = '1';  
                }else{
                    $result = '0';
                    $msg    = "Amc Not Added.";
                    $status = '0';
                } 
         }else{
            $result = $request;
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result,
            
        );
    }

    protected function listAmc($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];

        if($this->verifyToken($userId,$tokenId)){ 

            $sql = "SELECT * FROM appliance INNER JOIN amc_registered on amc_registered.appliance_id = appliance.appliance_id AND appliance.user_id ='$userId' AND appliance.appliance_is_deleted='0' AND amc_registered.user_id='$userId'";

            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);
            if ($Numrows > 0) {
            while ($row = $result->fetch_assoc()) {

            if(!empty($row['amc_document'])){$imageUrl = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/amc_images/'.$row['amc_document'];}else{$imageUrl = '';}

            if(!empty($row['amc_logo'])){$amc_logo = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/amc_images/'.$row['amc_logo'];}else{$amc_logo = '';}

                $amc_start_date = $this->ChangeDateDMY($row['amc_date_of_parched']);
                $amc_expiry_date = $this->ChangeDateDMY($row['amc_expiry_date']);

            $data[] = array('amc_id' => $row['amc_id'],'user_id' => $row['user_id'],'appliance_id' => $row['appliance_id'],'amc_tag_name' => ucwords($row['amc_tag_name']),'amc_model' => $row['amc_model'],'amc_serial' => $row['amc_serial'],'amc_date_of_parched' => $amc_start_date,'amc_expiry_date' => $amc_expiry_date,'amc_provider_name' => $row['amc_provider_name'],'amc_document'=> $imageUrl,'amc_logo'=>$amc_logo);

            }

                $result = $data;
                $msg    = "Amc List.";
                $status = '1';

            }else{
                $result = '0';
                $msg    = "You have not added any AMC yet!";
                $status = '0';
            }
         }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result,
            
        );
    } 

    protected function saveSupport($request)
    {
        $userId  = trim($request['userId']);
        $tokenId    = trim($request['tokenId']); 
        $email    = trim($request['email']); 
        $support_subject    = trim($request['support_subject']);
        $support_message    = trim($request['support_message']);
        $created_at      = date('Y-m-d');
        if($this->verifyToken($userId,$tokenId)){ 


            
            if($request['support_document']){
                $decode_image = base64_decode($request['support_document']);         
                $imageName = 'document_'.rand().$request['image_extension'];
                $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/support_document/';
                $user_dir_path  = $baseUrl . '/' . $imageName;
                $file  = fopen($user_dir_path, 'wb');
                fwrite($file, $decode_image);
                fclose($file); 
            }else{
                $imageName = "";
            }

            if( $this->connect()->query("INSERT INTO `support`(`user_id`, `support_subject`, `support_message`, `support_document`,`created_at`) VALUES ('$userId','$support_subject','$support_message','$imageName','$created_at')"))
            {
                $to = 'tarun@onbvcorp.com,akash@onbvcorp.com,abhilash@onbvcorp.com'; 
                $subject = 'Warranty & More - Support Feedback';
                $from = $email;


                $imgurl = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/support_document/'.$imageName;

                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: '.$from."\r\n".'Reply-To: '.$from."\r\n";

                // Compose a simple HTML email message
                $message = '<html><body>';
                $message .= '<p><b>Support From  : <b> &nbsp;'.$from.'</p>';
                $message .= '<p><b>Support Subject : <b> &nbsp;'.$support_subject.'</p>';
                $message .= '<p><b>Support Message : <b> &nbsp;'.$support_message.'</p>';
                $message .= '<p><b>Document : <b> &nbsp;'.$imgurl.'</p>';
                $message .= '</body></html>';

                mail($to, $subject, $message, $headers);

                    $result = '1';
                    $msg    = "Support Successfully Saved";
                    $status = '1';  
            }else{
                    $result = '0';
                    $msg    = "Support Not Save.";
                    $status = '0';
            }  
         }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result,
            
        );
    }

    protected function getSupport($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId']; 
      
        if($this->verifyToken($userId,$tokenId)){ 
            
           $sql = "SELECT * FROM `support` WHERE `user_id`='$userId'";
            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);
            if ($Numrows > 0) {
            while ($row = $result->fetch_assoc()) {

            if(!empty($row['support_document'])){$imageUrl = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/support_document/'.$row['support_document'];}else{$imageUrl = '';}

            $data[] = array('user_id' => $row['user_id'],'support_subject' => $row['support_subject'],'support_message' => $row['support_message'],'support_document'=> $imageUrl);

            }

                $result = $data;
                $msg    = "Support List.";
                $status = '1';

            }else{
                $result = '0';
                $msg    = "Support List Not Found.";
                $status = '0';
            }
         }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result,
            
        );
    }

    protected function otherUpdateWarranty($request)
    {
        $userId  = trim($request['userId']);
        $tokenId    = trim($request['tokenId']); 
        $applianceId   = trim($request['applianceId']);
        $upload_parts = $request['upload_parts'];

        $sqlcheck = "SELECT * FROM `appliance` WHERE `appliance_id`='$applianceId'"; 
            $resultchwck  = $this->connect()->query($sqlcheck);
            $rowcheck = $resultchwck->fetch_assoc();

            $name_tag  = trim($rowcheck['other_name_tag']);
            $date_of_parched  = trim($rowcheck['other_date_of_parched']);
            $warranty_tenure  = trim($rowcheck['other_warranty_tenure']);
            $expiry_date  = trim($rowcheck['other_expiry_date']); 

            $other_name_tag = $name_tag.$request['other_name_tag'].',';
            $other_date_of_parched = $date_of_parched.$request['other_date_of_parched'].',';
            $other_warranty_tenure = $warranty_tenure.$request['other_warranty_tenure'].',';
            $other_expiry = date('d-m-Y', strtotime($request['other_date_of_parched'] .'+'.$request['other_warranty_tenure']));
            $other_expiry_date = $expiry_date.$other_expiry.',';

        if($this->verifyToken($userId,$tokenId)){ 

            $other_date_of_parched1 = $this->ChangeDateYMD($other_date_of_parched);
            $other_expiry_date1 = $this->ChangeDateYMD($other_expiry_date);

            $decode_image = base64_decode($upload_parts);
            $imageName = 'parts_'.rand().$request['image_extension'];
            $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/appliance_images/';
            $user_dir_path  = $baseUrl . '/' . $imageName;
            $file      = fopen($user_dir_path, 'wb');
            if(fwrite($file, $decode_image)){
            $this->connect()->query("UPDATE `appliance` SET `other_name_tag`='$other_name_tag',`other_date_of_parched`='$other_date_of_parched1',`other_warranty_tenure`='$other_warranty_tenure',`other_expiry_date`='$other_expiry_date1' WHERE `appliance_id`='$applianceId'");
            

                    $result = '1';
                    $msg    = "Other Warranty Successfully Update";
                    $status = '1';  
            }else{
                    $result = '0';
                    $msg    = "Other Warranty Not Update.";
                    $status = '0';
            }  
         }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result,
            
        );
    } 
///////
    protected function serviceRaiseRequest($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId']; 
        $applianceId   = $request['applianceId'];
        $description    = $request['description'];
        $preffered_schedule    = $request['preffered_schedule'];
        $service_document = $request['service_document'];
        $created_at      = date('Y-m-d');

        if($this->verifyToken($userId,$tokenId)){ 

            $sql = "SELECT * FROM `appliance` WHERE `appliance_id`='$applianceId'";
            $result  = $this->connect()->query($sql);
            $row = $result->fetch_assoc();

            $raise_tag_name = $row['appliance_name_tag'];
            $raise_model = $row['appliance_model_no'];
            $date_of_parched = $row['appliance_date_of_parched'];
            $expiry_date = $row['appliance_expiry_date'];

            $decode_image = base64_decode($service_document);
            $imageName = 'document_'.rand().$request['image_extension'];
            $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/appliance_images/';
            $user_dir_path  = $baseUrl . '/' . $imageName;
            $file      = fopen($user_dir_path, 'wb');
            fwrite($file, $decode_image);

                $date_of_parched1 = $this->ChangeDateYMD($date_of_parched);
                $expiry_date1 = $this->ChangeDateYMD($expiry_date);

            if( $this->connect()->query("INSERT INTO `raise_service`(`user_id`, `applianceId`, `raise_tag_name`, `raise_model`, `date_of_parched`, `expiry_date`, `preffered_schedule`, `service_description`, `service_document`,`created_at`) VALUES ('$userId','$applianceId','$raise_tag_name','$raise_model','$date_of_parched1','$expiry_date1','$preffered_schedule','$description','$imageName','$created_at')"))
            {

                    $result = '1';
                    $msg    = "Raise Service Request Successfully.";
                    $status = '1';  
            }else{
                    $result = '0';
                    $msg    = "Not Raise Service Request.";
                    $status = '0';
            }  
         }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result,
            
        );
    }  

    protected function listByCategory($request)
    {
        $userId  = trim($request['userId']);
        $tokenId    = trim($request['tokenId']); 
        
        if($this->verifyToken($userId,$tokenId)){ 
            if($request['cat_location']=='Office'){
                $sql = "SELECT DISTINCT(appliance_category) FROM `appliance` WHERE `user_id`='$userId' AND `appliance_is_deleted`='0' AND `appliance_location_tag`='Office'";
                $loc = "Office";
            }else if($request['cat_location']=='Home'){
                $sql = "SELECT DISTINCT(appliance_category) FROM `appliance` WHERE `user_id`='$userId' AND `appliance_is_deleted`='0' AND `appliance_is_deleted`='0' AND `appliance_location_tag`='Home'";
                $loc = "Home";
            }else{
              $sql = "SELECT DISTINCT(appliance_category) FROM `appliance` WHERE `user_id`='$userId' AND `appliance_is_deleted`='0' AND `appliance_is_deleted`='0'";
                $loc = "";  
            }

            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);

            while ($row = $result->fetch_assoc()) {
                if($row['subcategory_icon']!=''){$url = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/category_icon/'.$row['subcategory_icon'];}else{$url = '';}
                $data[] = array('category_name' => $row['appliance_category'],'category_icon' => $url);
            }
            
            if($Numrows>0)
            {
                $result = $data;
                $msg    = "Appliance By Category List.";
                $status = '1';  
            }else{
                $result = '0';
                $msg    = "No Appliance List Found in ".$loc.".";
                $status = '0';
            }  
         }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result,
            
        );
    } 

    protected function listBySubCategory($request)
    {
        $userId  = trim($request['userId']);
        $tokenId    = trim($request['tokenId']);
        $category    = trim($request['category']);
        $cat_location    = trim($request['cat_location']);
                
        if($this->verifyToken($userId,$tokenId)){ 

            $sql = "SELECT DISTINCT(appliance_subCategory) FROM `appliance` WHERE `user_id`='$userId' AND `appliance_category`='$category' AND `appliance_is_deleted`='0' AND `appliance_location_tag`='$cat_location'";
            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);
            while ($row = $result->fetch_assoc()) {
                if($row['subcategory_icon']!=''){$url = 'http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/category_icon/'.$row['subcategory_icon'];}else{$url = '';}
                $data[] = array('sub_category_name' => $row['appliance_subCategory'],'sub_category_icon' =>$url );
            }
            
            if($Numrows>0)
            {
                $result = $data;
                $msg    = "Appliance By SubCategory List.";
                $status = '1';  
            }else{
                $result = '0';
                $msg    = "Not Appliance By SubCategory List.";
                $status = '0';
            }  
         }else{
            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result,
            
        );
    }   
}
?>