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
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class UserProfileController extends AbstractActionController
{

    public function indexAction()
    {

        $view =  new ViewModel(array('user'=>$this->current_user() ));
        
        return $view;
    }

    public function updateAction(){

        $view =  new ViewModel(array('user'=>$this->current_user()));
        
        $view->setTemplate('user-profile/index');
        $req=$this->getRequest();
        
        if(! $req->isPost()){
                
            $this->flashMessenger()->addErrorMessage(sprintf("Invalid request, submit below form"));                
            
            return $view;
        }
        
        $data = $this->params()->fromPost();
        /**
         * @var \Application\Service\UserProfileService
         */
        $user_profile_srv=$this->getServiceLocator()->get('UserProfileService');
                     
        $curr_user = $user_profile_srv->getCurrentUser();
        $user =  $user_profile_srv->getUserById($data['user_id']);


        //update phone information
        $result = $user_profile_srv->updatePhoneName($user, $data);
        if($result){
            $this->flashMessenger()->addSuccessMessage(sprintf("Phone information is updated"));
        }else if($data['primaryphone'] != $user->getPhonePrimary() || $data['alternatephone']!=$user->getPhoneAlternate()){
            $this->flashMessenger()->addErrorMessage(sprintf("Unable to update phone information, please contact administrator."));
        }
        
        //update password
        if($user->getid() != $curr_user->getId()){
            $this->flashMessenger()->addErrorMessage(sprintf("Invalid access, you can only change your password!"));
        } else{
            $data['member_code']=$user->getMemberCode();
            $result = $user_profile_srv->updatePassword($user, $data);
            if($result){
                $this->flashMessenger()->addSuccessMessage(sprintf("Password updated!"));
            }else if(!empty($data['newpassword'])){
                $this->flashMessenger()->addErrorMessage(sprintf("Unable to update your password, values don't match."));
            }
        }
                
        return $view;
    }
      
    public function changePasswordAction(){

        $req=$this->getRequest();
        /**
         * @var \Application\Service\UserProfileService
         */
        $user_profile_srv=$this->getServiceLocator()->get('UserProfileService');
                     
        $user = $user_profile_srv->getCurrentUser();

        if($req->isPost()){
            
            $user_id=$req->getPost('user_id');
            $password=$req->getPost('password');
            $password_match=$req->getPost('password_match');
            $user = $user_profile_srv->getUserById($user_id) ;
            //update Password
            $result = $user_profile_srv->updatePassword($user,$password);
            if(!$result){
                $this->flashMessenger()->addErrorMessage(sprintf("Unable to update passowrd, values don't match"));                
            }
        }
        $view = new ViewModel();
        $view->setTemplate('user-profile/index');
        
        return $view;        
    }

    public function validateUserNameAction(){
        
        $full_name=$this->getRequest()->getPost('full_name');
        $user_name=$this->getRequest()->getPost('user_name');
        $result=array('user_name'=>$user_name,'full_name'=>$full_name);
        
        if(!$this->getRequest()->isPost()){
            $result['status']='error';
            $result['error']='User validation request needs to be a post';
        }else{
            $user_profile_srv=$this->getServiceLocator()->get('UserProfileService');
            $proposed=null;
            $valid=true;
            if(!$user_profile_srv->isUserNameValid($user_name,$full_name)){
                $valid=true;
                $proposed=$user_profile_srv->proposeUserName($full_name);            
                if(!$proposed){
                    $valid=false;
                    //if we are unable to get a vliad user name chances are name given is not valid
                    $result['error']='Unable to propose username based on ['.$full_name.']';
                    $result['status']='invalid';
                }
                $result['valid']=$valid;
                $result['proposed']=$proposed;
            }                            
        }
        return new JsonModel($result);
    }


	public function settingsAction(){
		
		$result=array();
		
		if($this->getRequest()->ispost()){
	        /**
	         * @var \Application\Service\UserProfileService
	         */
	        $user_profile_srv=$this->getServiceLocator()->get('UserProfileService');
	                     
	        $curr_user = $user_profile_srv->getCurrentUser();
			$data = $this->params()->fromPost();
			$curr_user->addSetting('zoom',$data['zoom']);
			$user_profile_srv->saveUser($curr_user);			
			$result['success']=true;
			$result['setting']=$curr_user->getSettingsArray();
		}
		
		return new JsonModel($result);
	}
    
    public function searchAction(){

        $results=array();
        $data = $this->params()->fromPost();
        
        $profileService = $this->getServiceLocator()->get('UserProfileService');
        
        $queryString='';
        if(key_exists('query', $data)){
            $queryString = sha3($data['query']);
            $results = array_merge($results,$profileService->findUsersBy(array('ehash'=>$queryString) ) );
            $results = array_merge($results,$profileService->findUsersBy(array('mhash'=>$queryString) ) );
            $results = array_merge($results,$profileService->findUsersBy(array('uhash'=>$queryString) ) );
            $results = array_merge($results,$profileService->findUsersBy(array('ehash'=>$queryString) ) );
            $results = array_merge($results,$profileService->findUsersBy(array('mhash'=>$queryString) ) );
            $results = array_merge($results,$profileService->findUsersBy(array('uhash'=>$queryString) ) );
            $results = array_merge($results,$profileService->findUsersBy(array('id'=>$data['query']) ) );
        }        
        
        $view =  new ViewModel(array('data'=>$data,'results'=>$results,'queryString'=>$queryString));

        if($this->json()->uri()){
            
            return $this->json()->send(array('data'=>$results));                   

        }else{
            return $view;        
        }
    }

}
