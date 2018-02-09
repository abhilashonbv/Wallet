<?php
class UserModel extends Database
{
    private $exp = "Oops! Your session expired, you might have logged in other device. Pls login again";
    protected function getSignIn($request)
    {
        $username = $request['username'];
        $password = md5($request['password']);
        $sql      = "SELECT * FROM `users` WHERE (`user_email`='$username' OR `user_phone`='$username') AND `user_password`='$password' AND `user_status`='1'";
        $count    = $this->connect()->query($sql);
        
        $Numrows = mysqli_num_rows($count);
        
        if ($Numrows > 0) {
            $tokenData = time() . "_" . $username;
            $token     = $this->createToken($tokenData);
            
            $Updatesql = "UPDATE `users` SET `session_token`='$token' WHERE (`user_email`='$username' OR `user_phone`='$username')";
            $this->connect()->query($Updatesql);
            
            $sql    = "SELECT * FROM `users` WHERE (`user_email`='$username' OR `user_phone`='$username')";
            $result = $this->connect()->query($sql);
            while ($row = $result->fetch_assoc()) {

                 $data[] = array('user_id' => $row['user_id'],'session_token' => $row['session_token'],'first_name' => ucfirst(strtolower(ucfirst($row['first_name']))),'last_name' => ucfirst(strtolower(ucfirst($row['last_name']))),'user_email' => $row['user_email'],'country_code' => $row['country_code'],'user_phone' => $row['user_phone'],'user_image' => $row['user_image'],'user_location' => $row['user_location'],'user_group' => $row['user_group'],'user_notifications' => $row['user_notifications'],'user_notifications_sound' => $row['user_notifications_sound'],'user_facebook_token_id' => $row['user_facebook_token_id'],'user_google_token_id'=>$row['user_google_token_id']);
            }
            
            $result = $data;
            $msg    = "Login Successfully";
            $status = '1';
        } else {
            $result = '0';
            $msg    = "The username or passwrord you entered is incorrect.";
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
        );
    }
      
    protected function getSignUp($request)
    {
        $username  = trim($request['username']);
        $mobile    = trim($request['mobile']);
        $countryCode= trim($request['countryCode']);
        $password  = md5($request['password']);
        $date      = date('Y-m-d h:i:s', time());
        $tokenData = time() . "_" . $username;
        $token     = $this->createToken($tokenData);
        
        $sql = "SELECT * FROM `users` WHERE (`user_email`='$username' OR `user_phone`='$mobile') AND `user_status`='1'";
        $count   = $this->connect()->query($sql);
        $NumRows = mysqli_num_rows($count);
        if ($NumRows > 0) {
            $result = '0';
            $msg    = "The user you have entered is already registered.";
            $status = '0';
        } else {
            $sql = "INSERT INTO `users`(`session_token`, `user_email`, `country_code`,`user_phone`, `user_password`,`user_status`, `user_created_at`) VALUES ('$token','$username','$countryCode','$mobile','$password','1','$date')";
            
            if ($this->connect()->query($sql)) {
                
                $sql    = "SELECT * FROM `users` WHERE `user_email`='$username' AND `user_status`='1'";
                $result = $this->connect()->query($sql);
                while ($row = $result->fetch_assoc()) {

                 $data[] = array('user_id' => $row['user_id'],'session_token' => $row['session_token'],'first_name' => ucfirst(strtolower(ucfirst($row['first_name']))),'last_name' => ucfirst(strtolower(ucfirst($row['last_name']))),'user_email' => $row['user_email'],'country_code' => $row['country_code'],'user_phone' => $row['user_phone'],'user_image' => $row['user_image'],'user_location' => $row['user_location'],'user_group' => $row['user_group'],'user_notifications' => $row['user_notifications'],'user_notifications_sound' => $row['user_notifications_sound'],'user_facebook_token_id' => $row['user_facebook_token_id'],'user_google_token_id'=>$row['user_google_token_id']);
                   
                }
                
                $result = $data;
                $msg    = "Registration Done! Great!!";
                $status = '1';
            } else {
                $result = '0';
                $msg    = "Oops! Registration error.";
                $status = '0';
            }
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
        );
    }

    protected function SignUpSocial($request)
    {
        $email = trim($request['email']);
        $date      = date('Y-m-d h:i:s', time());
        $tokenData = time() . "_" . $email;
        $token     = $this->createToken($tokenData);

        $ak ="SELECT * FROM `users` WHERE `user_email`='$email' AND `user_status`='1'";
        $res = $this->connect()->query($ak);
        $row1 = $res->fetch_assoc();
        $NumRows = mysqli_num_rows($res);

        $user_id = $row1['user_id'];
        if($row1['first_name']==''){$f_name = $request['first_name'];}else{$f_name = $row1['first_name'];}
        if($row1['last_name']==''){$l_name = $request['last_name'];}else{$l_name = $row1['last_name'];}
        if($request['socialType'] == 'google'){
            if($row1['user_google_token_id']==''){$gaccessToken = $request['accessToken'];}else{$gaccessToken = $row1['user_google_token_id'];}
        }else{
            if($row1['user_facebook_token_id']==''){$faccessToken = $request['accessToken'];}else{$faccessToken = $row1['user_facebook_token_id'];}
        }

        $userimage = $request['userimage'];

        if($NumRows > 0){
            if($request['socialType'] == 'google'){
               
                $updateSql = "UPDATE `users` SET `session_token`='$token',`first_name`='$f_name',`last_name`='$l_name',`user_image`='$userimage',`user_google_token_id`='$gaccessToken' WHERE `user_id`='$user_id'";
            }else{
                
                $updateSql = "UPDATE `users` SET `session_token`='$token',`first_name`='$f_name',`last_name`='$l_name',`user_image`='$userimage',`user_facebook_token_id`='$faccessToken' WHERE `user_id`='$user_id'";
            }
            $this->connect()->query($updateSql);

            $res1=$this->connect()->query("SELECT * FROM `users` WHERE `user_email`='$email' AND `user_status`='1'");
            $row = $res1->fetch_assoc();
            $data[] = array('user_id' => $row['user_id'],'session_token' => $row['session_token'],'first_name' => $row['first_name'],'last_name' => $row['last_name'],'user_email' => $row['user_email'],'country_code' => $row['country_code'],'user_phone' => $row['user_phone'],'user_image' => $row['user_image'],'user_location' => $row['user_location'],'user_group' => $row['user_group'],'user_notifications' => $row['user_notifications'],'user_notifications_sound' => $row['user_notifications_sound'],'user_facebook_token_id' => $row['user_facebook_token_id'],'user_google_token_id'=>$row['user_google_token_id']);

            $result = $data;
            $msg    = "Registration Successfully.";
            $status = '1';
        }else{
            $password = md5($this->random_password(6));
            $Usql = "INSERT INTO `users`(`session_token`, `first_name`, `last_name`, `user_email`, `user_password`, `user_image`,  `user_facebook_token_id`, `user_google_token_id`, `user_status`, `user_created_at`) VALUES ('$token','$f_name','$l_name','$email','$password','$userimage','$faccessToken','$gaccessToken','1','$date')";
            
            if($this->connect()->query($Usql))
            {
                $sql    = "SELECT * FROM `users` WHERE `user_email`='$email' AND `user_status`='1'";
                $result = $this->connect()->query($sql);
                $row = $result->fetch_assoc();

                $data[] = array('user_id' => $row['user_id'],'session_token' => $row['session_token'],'first_name' => $row['first_name'],'last_name' => $row['last_name'],'user_email' => $row['user_email'],'country_code' => $row['country_code'],'user_phone' => $row['user_phone'],'user_image' => $row['user_image'],'user_location' => $row['user_location'],'user_group' => $row['user_group'],'user_notifications' => $row['user_notifications'],'user_notifications_sound' => $row['user_notifications_sound'],'user_facebook_token_id' => $row['user_facebook_token_id'],'user_google_token_id'=>$row['user_google_token_id']);  

                $result = $data;
                $msg    = "Registration Successfully";
                $status = '1';
            } else {
                $result = '0';
                $msg    = 'Registration error.';
                $status = '0';
            }
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
        );
    }

    protected function getDashboard($request)
    {
        $userId  = trim($request['userId']);
        $tokenId    = trim($request['tokenId']);
        
        if ($this->verifyToken($userId,$tokenId)) {
            $sql = "SELECT * FROM `appliance` WHERE  `user_id`='$userId' AND `appliance_is_deleted`='0' AND `appliance_category`!=''"; //DESC";

            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);

            $sql1 = "SELECT amc_registered.amc_id, appliance.appliance_id FROM appliance INNER JOIN amc_registered on amc_registered.appliance_id = appliance.appliance_id AND appliance.user_id ='$userId' AND appliance.appliance_is_deleted='0' AND amc_registered.user_id='$userId'";


            $result1  = $this->connect()->query($sql1);
            $total_amc = mysqli_num_rows($result1);

            $sqlInvoice = "SELECT * FROM `appliance` WHERE  `user_id`='$userId' AND `appliance_mode`='1' AND `appliance_is_deleted`='0' AND `appliance_category`!=''";

            $resultInvoice  = $this->connect()->query($sqlInvoice);
            $total_invoice = mysqli_num_rows($resultInvoice);

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

                     if(!empty($expiry_date)){
                        $numberDays = (strtotime($expiry_date) - strtotime(date("Y-m-d"))) / (60 * 60 * 24);
                        $cdate =  date('d-m-Y');
                       
                        //if(($numberDays <= 30) && ($cdate <= $expiry_date)){
                        if($numberDays <= 30){
                            $mydata[] = $this->checkExp($expiry_date,$date_of_parched,$applianceId); 
                        }                 
                    }      
                }  

                $total_expiry=count(@$mydata);
                if($total_expiry=='0'){
                    $mydata= array();
                }

                $registered = array('total_registered' =>"$Numrows" ,'total_invoice' =>"$total_invoice" ,'total_amc' =>"$total_amc",'total_expiry' =>"$total_expiry" ,'expiry_list' =>$mydata);

                $result =  $registered;
                $msg    = "Dashboard data.";
                $status = '1';
            } else {
                $result = '0';
                $msg    = "Hey! Let's start our journey ! Please add your first appliance, I will assist in every section.";
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

    public function checkExp($expiry_date,$date_of_parched,$applianceId)
    { 
        $sql = "SELECT * FROM `appliance` WHERE `appliance_id` = '$applianceId' AND `appliance_is_deleted`='0'";

        $result1  = $this->connect()->query($sql);
          
        $row = $result1->fetch_assoc();

        $brand_warranty_url = $this->getNameById($row['appliance_subCategory'],$row['appliance_manufacturer']);
         
            $date_of_parched1 = $this->ChangeDateDMY($date_of_parched);
            $expiry_date1 = $this->ChangeDateDMY($expiry_date);  

        $data= array('appliance_id' =>$row['appliance_id'] ,'user_id' =>$row['user_id'] ,'appliance_manufacturer' =>$row['appliance_manufacturer'] ,'appliance_model_no' =>$row['appliance_model_no'] ,'appliance_capacity' =>$row['appliance_capacity'] ,'date_of_parched' =>$date_of_parched1,'expiry_date' =>$expiry_date1,'appliance_name_tag' =>$row['appliance_name_tag'],'brand_warranty_url'=>$brand_warranty_url);
        
        return $data;
    }

    protected function getProfileList($request)
    {
        $userId  = trim($request['userId']);
        $tokenId    = trim($request['tokenId']);
        
        if ($this->verifyToken($userId,$tokenId)) {
            $sql = "SELECT * FROM `users` WHERE  `user_id`='$userId'";

            $result  = $this->connect()->query($sql);
            $Numrows = mysqli_num_rows($result);
            
            if ($Numrows > 0) {

                $row = $result->fetch_assoc();
                    
                 $data[] = array('user_id' => $row['user_id'],'session_token' => $row['session_token'],'first_name' => ucfirst(strtolower(ucfirst($row['first_name']))),'last_name' => ucfirst(strtolower(ucfirst($row['last_name']))),'user_email' => $row['user_email'],'country_code' => $row['country_code'],'user_phone' => $row['user_phone'],'user_image' => $row['user_image'],'user_location' => $row['user_location'],'user_group' => $row['user_group'],'user_notifications' => $row['user_notifications'],'user_notifications_sound' => $row['user_notifications_sound'],'user_facebook_token_id' => $row['user_facebook_token_id'],'user_google_token_id'=>$row['user_google_token_id']);
                

                $result =  $data;
                $msg    = "Profile Info Details.";
                $status = '1';
            } else {
                $result = '0';
                $msg    = "Profile Info Details error.";
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

    protected function UpdateProfile($request)
    {
        $userId  = $request['userId'];
        $tokenId    = $request['tokenId'];
        $first_name  = $request['first_name'];
        $last_name    = $request['last_name'];
        $profile_image = $request['profile_image'];

        
        if($this->verifyToken($userId,$tokenId)){
            $decode_image = base64_decode($profile_image);                
            $imageName = 'profile_'.rand().$request['image_extension'];
            $baseUrl = $_SERVER['DOCUMENT_ROOT'].'/walletWebApi/profile_images/';
            $user_dir_path  = $baseUrl . '/' . $imageName;
            $file      = fopen($user_dir_path, 'wb');
            if(fwrite($file, $decode_image))
            {
                $imageUrl='http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/profile_images/'.$imageName;   
            }else{
                $imageUrl='http://'.$_SERVER['HTTP_HOST'].'/walletWebApi/profile_images/icons8-male-user-filled.png';
            }
            fclose($file);

            if($this->connect()->query("UPDATE `users` SET `first_name`='$first_name',`last_name`='$last_name',`user_image`='$imageUrl' WHERE `user_id`='$userId'")){
                $sql = "SELECT * FROM `users` WHERE  `user_id`='$userId'";
                $result  = $this->connect()->query($sql);
                $row = $result->fetch_assoc();

                $data[] = array('user_id' => $row['user_id'],'session_token' => $row['session_token'],'first_name' => ucwords($row['first_name']),'last_name' => ucwords($row['last_name']),'user_email' => $row['user_email'],'country_code' => $row['country_code'],'user_phone' => $row['user_phone'],'user_image' => $row['user_image'],'user_location' => $row['user_location'],'user_group' => $row['user_group'],'user_notifications' => $row['user_notifications'],'user_notifications_sound' => $row['user_notifications_sound'],'user_facebook_token_id' => $row['user_facebook_token_id'],'user_google_token_id'=>$row['user_google_token_id']);
                 
                    $result = $data;
                    $msg    = "Profile Update Successfully.";
                    $status = '1';  
            }else{
                    $result = '0';
                    $msg    = "Profile Not Uploaded ";
                    $status = '0';
            }  
        }else{

            $result = '0';
            $msg    = $this->exp;
            $status = '0';
        }

        return array('status' => $status,'msg' => $msg,'data' => $result);       
    }

    public function createToken($data)
    {
        $tokenGeneric = SECRET_KEY . $_SERVER["SERVER_NAME"];
        $token        = hash('sha256', $tokenGeneric . $data);
        return $token;
    }

    public function random_password( $length = 6 )
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $password = substr( str_shuffle( $chars ), 0, $length );
        return $password;
    }

}
?>