<?php
class CategoryModel extends Database
{
    protected function categoryList()
    {
      $sql     = "SELECT * FROM `category` ORDER BY cotegory_name";
      $result  = $this->connect()->query($sql);
      $Numrows = mysqli_num_rows($result);
            
      if ($Numrows > 0) {

      while ($row = $result->fetch_assoc()) {
        $data[] = array('cotegory_id' => $row['cotegory_id'], 'cotegory_name' => ucwords($row['cotegory_name']));
      }
      $result = $data;
      $msg    = "Category List";
      $status = '1';
      } else {
      $result = '0';
      $msg    = "Sorry No Category List.";
      $status = '0';
      }
      return array(
      'status' => $status,
      'msg' => $msg,
      'data' => $result
      );    
    }  

    protected function subCategoryList($request)
    {
      $catId = $request['catId'];
      $sql     = "SELECT * FROM `sub_category` WHERE `category_id`='$catId' ORDER BY subcategory_name";
      $result  = $this->connect()->query($sql);
      $Numrows = mysqli_num_rows($result);
       if ($Numrows > 0) {
      while ($row = $result->fetch_assoc()) {

        $data[] = array('subcategory_id' => $row['subcategory_id'],'category_id' => $catId, 'subcategory_name' => ucwords($row['subcategory_name']));

       }
       $result = $data;
       $msg    = "Sub Category List";
       $status = '1';
       } else {
       $result = '0';
       $msg    = "Sorry No Sub Category List.";
       $status = '0';
      }
       return array(
       'status' => $status,
       'msg' => $msg,
       'data' => $result
       );    
    }

    protected function manufacturerList($request)
    {
      $subCatId = trim($request['subCatId']);

      if($subCatId =='Cus'){
        $sql     = "SELECT * FROM `manufacturer` LIMIT 390";  
      }else{
        $sql     = "SELECT * FROM `manufacturer` WHERE `manufacturer_subcat_id`='$subCatId' ORDER BY manufacturer_name";
      }
      $result  = $this->connect()->query($sql);
      $Numrows = mysqli_num_rows($result);
       if ($Numrows > 0) {
      while ($row = $result->fetch_assoc()) {
         $data[] = array('manufacturer_id' => $row['manufacturer_id'],'manufacturer_subcat_id' =>$subCatId,'manufacturer_name' =>ucwords($row['manufacturer_name']));
       }
       $result = $data;
       $msg    = "Manufacturer List";
       $status = '1';
       } else {
       $result = '0';
       $msg    = "Sorry No Manufacturer List.";
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