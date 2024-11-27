<?php
 
 $EMPTY_ARRAY=array();
 function getBranchesWithOffices($user_id,$include_child=true){
        
       $office_list = array('27'=>'27');

       $branches_with_offices=array();
       
       foreach($office_list as $ind=>$office) {
           //add this office's dept 
           $dept_list=array($office);
           
               
           if(count(array_diff(EMPTY_ARRAY,$office->getSuperviseDepartments())) ){
               $dept_list = array_merge($dept_list,$office->getSuperviseDepartments());
           }
           if(count(array_diff($this->EMPTY_ARRAY,$office->getOverseeDepartments()) ) ){
               $dept_list = array_merge($dept_list,$office->getOverseeDepartments());
           }

           //add this office's branch
           if(key_exists($office->getBranch()->getId(),$branches_with_offices)){
              $branches_with_offices[$office->getBranch()->getId()]=array_merge($branches_with_offices[$office->getBranch()->getId()],$dept_list);    
           }else{
               $branches_with_offices[$office->getBranch()->getId()]=$dept_list;
           }
           
           //add child branches
           if($include_child){
               $child_branches = $branch_srv->getChildBranches($office->getBranch());
               foreach($child_branches as $child){
                   if(key_exists($child->getId(),$branches_with_offices)){
                      $branches_with_offices[$child->getId()]=array_merge($branches_with_offices[$child->getId()],$dept_list);    
                   }else{
                       $branches_with_offices[$child->getId()]=$dept_list;
                   }                                         
               }

               //TODO add  branche area links (regions)
           }
                      
       }        
        return $branches_with_offices;
    }
