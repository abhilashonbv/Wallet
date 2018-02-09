<?php
class UserRestHandler extends UserModel
{
    
    public function SignIn($request)
    {
        $rawData = $this->getSignIn($request);
        if (empty($rawData)) {
            $statusCode = 404;
            $rawData    = array(
                'error' => 'No Data found!'
            );
        } else {
            $statusCode = 200;
        }
        
        $requestContentType = 'application/json';
        $this->setHttpHeaders($requestContentType, $statusCode);
        
        if (strpos($requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($rawData);
            echo $response;
        }
    }
    
    public function SignUp($request)
    {
        $rawData = $this->getSignUp($request);
        if (empty($rawData)) {
            $statusCode = 404;
            $rawData    = array(
                'error' => 'No Data found!'
            );
        } else {
            $statusCode = 200;
        }
        
        $requestContentType = 'application/json';
        $this->setHttpHeaders($requestContentType, $statusCode);
        
        if (strpos($requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($rawData);
            echo $response;
        }
    }

    public function SocialSignUp($request)
    {
        $rawData = $this->SignUpSocial($request);
        if (empty($rawData)) {
            $statusCode = 404;
            $rawData    = array(
                'error' => 'No Data found!'
            );
        } else {
            $statusCode = 200;
        }
        
        $requestContentType = 'application/json';
        $this->setHttpHeaders($requestContentType, $statusCode);
        
        if (strpos($requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($rawData);
            echo $response;
        }
    }

    public function Dashboard($request)
    {
        $rawData = $this->getDashboard($request);
        if (empty($rawData)) {
            $statusCode = 404;
            $rawData    = array(
                'error' => 'No Data found!'
            );
        } else {
            $statusCode = 200;
        }
        
        $requestContentType = 'application/json';
        $this->setHttpHeaders($requestContentType, $statusCode);
        
        if (strpos($requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($rawData);
            echo $response;
        }
    }

    public function ProfileList($request)
    {
        $rawData = $this->getProfileList($request);
        if (empty($rawData)) {
            $statusCode = 404;
            $rawData    = array(
                'error' => 'No Data found!'
            );
        } else {
            $statusCode = 200;
        }
        
        $requestContentType = 'application/json';
        $this->setHttpHeaders($requestContentType, $statusCode);
        
        if (strpos($requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($rawData);
            echo $response;
        }
    }

    public function ProfileUpdate($request)
    {
        $rawData = $this->UpdateProfile($request,$files);
        if (empty($rawData)) {
            $statusCode = 404;
            $rawData    = array(
                'error' => 'No Data found!'
            );
        } else {
            $statusCode = 200;
        }
        
        $requestContentType = 'application/json';
        $this->setHttpHeaders($requestContentType, $statusCode);
        
        if (strpos($requestContentType, 'application/json') !== false) {
            $response = $this->encodeJson($rawData);
            echo $response;
        }
    }
    
}
?>