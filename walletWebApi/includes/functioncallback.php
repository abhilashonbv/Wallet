<?php
 
class functionCallBack{

    function verifyToken($userId,$token)
    {
        $sql     = "SELECT * FROM `users` WHERE `user_id`='$userId' AND `session_token`='$token'";
        $result  = $this->connect()->query($sql);
        $Numrows = mysqli_num_rows($result);
        if ($Numrows > 0) {
            return true;
        }else{
            return false;
        }
    }
    protected function ChangeDateDMY($date){
    {
       return $newDate = date("d-m-Y", strtotime($date));
    }

    protected function ChangeDateYMD($date){
    {
       return $newDate = date("Y-m-d", strtotime($date));
    }
}

?>

