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
use Zend\Crypt\Password\Bcrypt as Bcrypt;

use Application\Entity\Period;
use Application\View\HighChart\DataTransform;



class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        
		return $this->redirect()->toRoute('home/members');
    }

    public function htmlAction()
    {        
        $uri = $this->getRequest()->getUriString();         
        
        $page = preg_replace('/.*home\/html\//', '', $uri);

        if($page===$uri){
            $page='blank';
        }            


        $page = preg_replace('/\.html$/', '', $page);
        $page='application/ace_ajax/'.$page.'.html';
        
        $view =  new ViewModel(array('caller'=>'simple', 'page'=>'/home/html'));

        $view->setTemplate($page);
        
        $this->layout('layout/layout_html');
        
        return $view;
    }

    public function stuffAction()
    {
        return new ViewModel(array('caller'=>'stuff'));
    }

    public function firstAction(){
        
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        $migratedUser=$current_user->getMigratedUserId();
        
        $valid_first_user=false;
        if($migratedUser){
            $query = "SELECT * FROM ami_users WHERE user_id = '$migratedUser'  and status = '1'"; //ami_users
            $result = @mysql_db_query($dbname,$query);
            while ($row = mysql_fetch_array($result)) {       
                $_SESSION['login'] = array (
                                "login" => $myUser,
                                "ID" => session_id(),
                                "user_id" => $myID,
                                "user_type" => $myType,
                                "user_level" => $myLevel,
                                "user_dept" => $myDept,
                                "user_email" => $myEmail1,
                                "config_allow_attachments" => 1,
                                "branch_code" => $myBCode
                                );
            }
            if (($myChange_pw!="") && ($myExpiry_date!="") && ($Error!="Error")) {
                $valid_first_user=true;                
            }
        } 

        if($valid_first_user){
            $this->redirect();
        }else{
            
        }
    }

    public function helpAction()
    {
        return new ViewModel(array('caller'=>'help'));
    }


    public function membersAction()
    {
        
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();        
    
        $authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
        $roles = $authorize->getIdentityRoles();
        
        
        
        $officeSrv = $this->serviceLocator->get('OfficeAssignmentService');
        $office = $officeSrv->getActiveOffices($current_user);

        $msgSrv = $this->serviceLocator->get('MessagesService');        
        $messages = $msgSrv->getUserMessages($current_user);

        //error_log($office[0]->getBranch()->getBranchName());
            
        $year = null;
        
        $period_code=Period::createLast();
        
        $post = $this->params()->fromPost();
        //$post['period_code']='Aug-2014';
        if(isset($post['period_code'])){
            $period_code = $post['period_code'];
            $year = substr($period_code,4);
        }

        $dashboardSrv =  $this->serviceLocator->get('DashboardService');
        $dashboard_data = $dashboardSrv->reportsSubmission($current_user,$year,$period_code);
        
        $subordinate_graph_data=null;
        if($dashboard_data['has_subordinates']){
            $subordinate_graph_data = DataTransform::convertToColumn($dashboard_data['subordinate']['data'],'period_code','report_status',null,array());
        }
        
        
        // $data =  $dashboardSrv->getBasicDashBoardSubBranch($offices[0]->getBranch());
        // $data = DataTransform::convertToColumn($data['last_six_month'],'period_code','branch_name','report_cont');
        //print_r($subordinate_graph_data);
        //exit;
        
        //Subordinate title
        //Jama`at
        //National
        //Halqa
        $designation=$office[0]->getBranch()->getOfficeBearerDesignation();
        $subordinate_title='Halqajat';
        if($designation=='National'){
            $subordinate_title='Jama`at';
        }
        
        
        $view = new ViewModel(array('caller'=>'members','messages'=>$messages,
                                     'office'=>$office,'dashboard_data'=>$dashboard_data, 
                                     'subordinate_graph_data' =>$subordinate_graph_data,
                                     'subordinate_title'=>$subordinate_title));

        //$view->setTemplate('application/index/members');
        return $view;
    }
  
    public function emailAction(){
        $uri = explode('/',$this->getRequest()->getUri());      
        $path=explode('?',array_pop($uri));   
        $token = $this->getServiceLocator()->get('UserProfileService')->createUserUpdateToken($this->zfcUserAuthentication()->getIdentity(),'reset_password');
        
        $vars=array('user'=>$this->zfcUserAuthentication()->getIdentity(),'token'=>$token);
        $param = array_map(function($arg){$parts=explode('=', $arg);return array($parts[0]=>urldecode($parts[1]));}, explode('&',$path[1]));
        foreach ($param as $key => $value) {
           $vars = array_merge($vars,$value);
        }
        $tmpl = 'email/'.$path[0];
        
        $view = new ViewModel($vars);
        $view->setTemplate($tmpl);
     
        return $view;
    }

    public function ajaxAction()
    {
        $this->layout('layout/layout_ajax');

        $view =  new ViewModel(array('caller'=>'simple', 'page'=>'#/page/home/ajax'));
		
		$uri = $this->getRequest()->getUriString();			
		
		$page = preg_replace('/.*home\/ajax\//', '', $uri);
			
		$page = preg_replace('/\.html$/', '', $page);

		$view->setTemplate('application/ace_ajax/'.$page.'.html');
	
        //$bcrypt = new Bcrypt();    	

        //print   $bcrypt->create('Password');


		return $view;
    }

}
