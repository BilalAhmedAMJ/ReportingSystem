<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Form\Form;
use Zend\Form\Element;
use Dompdf\Dompdf;


class AmiSynchController extends AbstractActionController
{

    public function synchAction(){        

        $app_key = $offices = $this->params()->fromPost('app-key');
        $app_id = $offices = $this->params()->fromPost('app-id');
        // if($app_key!==getenv('app_key')||$app_id!==getenv('app_id')){
        //     return $this->json()->send(array('data'=>array('error'=>'Inavlid app credentials')));
        // }

        //For each User
        $post_data = $this->getRequest()->getContent();  
        $json = json_decode($post_data, true);
        $offices = $json['offices'];
        $term = $json['term'];
   
        if(! strpos($term,'-')){
           $term = substr($term,0,4).'-'.substr($term,-4);
        }
        $office_assignment_srv = $this->getServiceLocator()->get('OfficeAssignmentService');
        $synchData = array();

    try{
        $count=0;
        foreach ($offices as $office) {
            $office['term']=$term;
            $data = $office_assignment_srv->processAMIAamilaSynch($office);
            $synchData[] = array('memberCode'=>$data['member_code'],
                                 'fullName'=>$data['full_name'],
                                 'officeId'=>$data['office_id'],
                                 'officeExpiry'=>$data['period_to']);
        }
    }catch (Exception $e){
        $synchData['status']='error';
        $synchData['message']=$e->getMessage();
        error_log('*** UNABLE TO SYNC '.$e->getMessage());
    }
        error_log('Sending Data '.print_r($synchData, true)); 
        return $this->json()->send($synchData);
    }


}
