<?php
class ApplianceRestHandler extends ApplianceModel
{
    
    public function SaveAppliance($request,$file)
    {
        $rawData = $this->appliance($request,$file);
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

    public function ApplianceList($request)
    {
        $rawData = $this->appliances($request);
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

    public function ScanAppliance($request,$file)
    {
        $rawData = $this->applianceScan($request,$file);
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

    public function UploadAppliance($request,$file)
    {
        $rawData = $this->applianceUpload($request,$file);
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

    public function ViewApplianceDetails($request)
    {
        $rawData = $this->ApplianceDetails($request);
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