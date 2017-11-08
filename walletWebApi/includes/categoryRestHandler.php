<?php
class CategoryRestHandler extends CategoryModel
{
    
    public function getCategoryList()
    {
        $rawData = $this->categoryList();
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
    
    public function getSubCategoryList($request)
    {
        $rawData = $this->subCategoryList($request);
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

    public function getManufacturerList($request)
    {
        $rawData = $this->manufacturerList($request);
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