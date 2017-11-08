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
}
?>