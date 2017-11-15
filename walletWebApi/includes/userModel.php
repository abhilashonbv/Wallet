<?php
class UserModel extends Database
{
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
                $data[] = $row;
            }
            
            $result = $data;
            $msg    = "Login Successfully";
            $status = '1';
        } else {
            $result = '0';
            $msg    = "Login credentials are incorrect";
            $status = '0';
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
        );
    }
    
    protected function getSignIn1()
    {
        
        echo $sql = "SELECT * FROM `users`";
        $count = $this->connect()->query($sql);      
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
        
        $sql     = "SELECT * FROM `users` WHERE `user_email`='$username' AND `user_status`='1'";
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
                    $data[] = $row;
                }
                
                $result = $data;
                $msg    = "Registration Successfully";
                $status = '1';
            } else {
                $result = '0';
                $msg    = "Registration error.";
                $status = '0';
            }
        }
        return array(
            'status' => $status,
            'msg' => $msg,
            'data' => $result
        );
    }
    
    public function createToken($data)
    {
        $tokenGeneric = SECRET_KEY . $_SERVER["SERVER_NAME"];
        $token        = hash('sha256', $tokenGeneric . $data);
        return $token;
    }
}
?>