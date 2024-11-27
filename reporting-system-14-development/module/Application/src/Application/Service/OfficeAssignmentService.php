<?php


namespace Application\Service;

use Application\Entity\OfficeAssignmentRequest;
use Application\Entity\OfficeAssignment;
use Application\Entity\Period;
use Application\Entity\User;
use Application\Entity\UserToken;


use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;

use ZfcDatagrid\DataSource\Doctrine2 as GridDataSource;


class OfficeAssignmentService implements FactoryInterface{
    
    public $USER_LEVELS=array('national','imarat','jamaat','halqa'); 
    
    private $EMPTY_ARRAY=array('');
    
    private $serviceLocator;
    private $entityManager;
    private $entityFactory;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  
        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $this->entityFactory = $this->serviceLocator->get('entityFactory');

        $this->logger = $serviceLocator->get('Logger');
        return $this;
    }
    
    public function getOfficeAssignmentRequest($id){
        return $this->entityManager->find('Application\Entity\OfficeAssignmentRequest',$id);
    }

    public function getOfficeAssignment($id){
        return $this->entityManager->find('Application\Entity\OfficeAssignment',$id);
    }


    /**
     * Processes an office assignment approval
     * 
     *   Office Assignment request approval combinations to test
     *   
     *   +-----------+-------------------+-----------+---------------------+     
     *   | User      | User Status       | Office    | Office Status
     *   +-----------+-------------------+-----------+---------------------+     
     *   | New       | N/A               | None      | N/A
     *   | New       | N/A               | Exists    | Locked/Disabled
     *   | New       | N/A               | Exists    | Active
     * 
     *   | Exists    | Locked/Disabled   | None      | N/A
     *   | Exists    | Locked/Disabled   | Exists    | Locked/Disabled
     *   | Exists    | Locked/Disabled   | Exists    | Active
     * 
     *   | Exists    | Active            | None      | N/A
     *   | Exists    | Active            | Exists    | Locked/Disabled
     *   | Exists    | Active            | Exists    | Active
     *   +-----------+-------------------+-----------+---------------------+     
     *   
     */
    public function processOfficeAssignmentRequest($data){
        
        /* @var OfficeAssignmentRequest */
        $request=null;
        if($data['request_id'] && is_numeric($data['request_id'])){
            $request = $this->getOfficeAssignmentRequest($data['request_id']);
        }else{
            throw new \Exception('Unable to find given request ['.$data['request_id'].']');
        }
        
        $usr_prf_srv=$this->serviceLocator->get('UserProfileService');
        $processed_by=$usr_prf_srv->getCurrentUser();
        $request->setProcessedBy($processed_by);
        $request->setProcessorComments($data['processor_comments']);
        $request->setDateProcessed(new \DateTime());
        
        $request = $this->updateRequestedPropertiesFromData($request,$data);
        
        $user_message='';
        if($data['approval_status'] == 'approved' && $request->getStatus() == 'requested' ){
            //check to see if user exists, otherwise w need to create one
            $user_message='approved';
            /**
             * @var User
             */
            $user=null;
        
            if(key_exists('user',$data) ){
                $user = $usr_prf_srv->getUserByMembercode($data['user']['member_code']);        
                if($user && $user->getStatus()!='active'){
                    $user->setStatus('active');
                    $user_message='user_activated';
                }elseif($user && $data['copy_user_info_from_req']){
                                        
                    //Update user information
                    $user->setDisplayName($data['user']['full_name']);
                    $user->setEmail($data['user']['email']);
                    $user->setPhonePrimary($data['user']['primary_phone']);
                    $user->setPhoneAlternate($data['user']['alt_phone']);
                    
                }
            }
            if(! $user){
                $user_data = $data['user'];
                $user_data['phone_primary']=$data['primary_phone'];
                $user_data['phone_alternate']=$data['alt_phone'];
                $user_data['full_name']=$data['full_name'];
                $user_data['member_code']=$data['member_code'];
                $user_data['email']=$data['email'];
                $user_data['confirm_email']=$data['confirm_email'];
                $user_data['status']='active';
                
                $user = $usr_prf_srv->createUser($user_data,false);
                $user_message='new_user';
            }
            //save user
            $this->entityManager->persist($user);

            //if there is already an office for this branch/department we need to cancel that            
            $existingOffice = $this->getBranchDepartmentOffice($request->getBranch(), $request->getDepartment()->getDepartmentName(),array('active','disabled') );

            //create office Assignment            
            $officeAssignment = $request->getOfficeAssignment();
            
            if($officeAssignment!=null){
                throw new \Exception('Expecting linked office to be null, request already processed? '.($officeAssignment!=null). '  Id is '. $officeAssignment->getId());
            }
            
            if($existingOffice && $existingOffice->getUser()->getId() == $user->getId()){
                //request is to assign office to same user again
                //we will simple update office details to new allocation
                $officeAssignment  = $existingOffice;
                $officeAssignment->setStatus('active'); 
                // set periods
                $this->updateOfficeAssignmentPeriod($officeAssignment,$request);
            }else if ($existingOffice){
                //we need to cancel existing office
                //by changing to date and PeriodTo last month period
                $last_period = Period::createLast();
                $existingOffice->setPeriodTo($this->entityManager->find('\Application\Entity\Period',$last_period) );
                $existingOffice->setStatus('former');
                $this->entityManager->persist($existingOffice);   
                // TODO 
                // Insert Transitionmodule here
                //

            }                                        
                        
            if($officeAssignment == null){
                //create new office assignment
                $data['user']=$user;
                $data['branch']=$request->getBranch();
                $data['department']=$request->getDepartment();
                $data['status']='active';
                
                $officeAssignment  = $this->createOfficeAssignment($data);
                $officeAssignment->setUser($user);
            }            
            
            $this->entityManager->persist($officeAssignment);

            $request->setOfficeAssignment($officeAssignment);

            //send notifications
            $this->notifyForOfficeAssignment($usr_prf_srv,$user,$officeAssignment,$request,$user_message);
            
                    
        }elseif($data['approval_status'] == 'rejected' && $request->getStatus() == 'requested'){
            $this->notifyForOfficeAssignment($usr_prf_srv,$user,null,$request,'');
        }

        $request->setStatus($data['approval_status']);
        
        $this->entityManager->persist($request);

        $this->entityManager->flush();
        
        return $request;
    }

    private function notifyForOfficeAssignment($usr_prf_srv,$user,$officeAssignment,$request,$user_message){
        $emailSrv = $this->serviceLocator->get('EmailService');
        if(!empty($user_message)){
            if($user_message!='approved'){
                //Send Email notification to user for their new assignment            
                $token = $usr_prf_srv->createUserUpdateToken($user, UserToken::ACTION_RESET_PASSWORD);        
                $emailSrv = $this->serviceLocator->get('EmailService');
                $emailSrv->sendTemplatedEmail('AMJ Reporting System office assignment',$user->getEmail(),'office_assignment_notification',
                                             array('user'=>$user,'office'=>$officeAssignment,'token'=>$token));
            }
            //send notification to superiors
            $branch = $officeAssignment->getBranch();
            $parent = $branch->getParent();
            $parents = array($parent);//add first parent
            while(($parent!=null)){
                //while we hit Markaz keep adding all parents to list
                $parent = $parent->getParent();
                $parents[]=$parent;    
            }
            //notify for all parents
            $notify_offices=array();
            $notify_offices[] = $this->getBranchDepartmentOffice($parents, $officeAssignment->getDepartment()->getDepartmentName());
            $notify_offices[] = $this->getBranchDepartmentOffice($branch, $this->serviceLocator->get('ConfigService')->getConfig('president_department_name'));
            $notify_offices[] = $this->getBranchDepartmentOffice($branch, $this->serviceLocator->get('ConfigService')->getConfig('gs_department_name'));
            $notify_office_emails = array();
            foreach ($notify_offices as $notify_office) {
                if(is_object($notify_office)){
                    $notify_office_emails[]= $notify_office->getUser()->getEmail();
                }
            } 
            //send templated email
            $emailSrv->sendTemplatedEmail('AMJ Reporting System office assignment',$notify_office_emails,'subord_office_assignment_notification',
                                         array('user'=>$user,'office'=>$officeAssignment,'user_message'=>$user_message));
                        
        }else{
            //send notification to requester on not approval
            $notify_offices = array();
            $notify_offices[] = $this->getBranchDepartmentOffice($branch, $this->serviceLocator->get('ConfigService')->getConfig('president_department_name'));
            $notify_offices[] = $this->getBranchDepartmentOffice($branch, $this->serviceLocator->get('ConfigService')->getConfig('gs_department_name'));
            $notify_office_emails=array();
            foreach ($notify_offices as $notify_office) {
                $notify_office_emails[]= $notify_office->getUser()->getEmail();
            } 
            //send templated email
            $emailSrv->sendTemplatedEmail('AMJ Reporting System office assignment',$notify_office_emails,'subord_office_assignment_notification',
                                         array('user'=>$user,'office'=>$officeAssignment,'user_message'=>$user_message));
            
        }
        
    }

    public function updateOfficeAssignmentPeriod($officeAssignment,$data){
        // if selected term is not current term user first period of selected terms as from_period
        
        $data = $this->toArray($data);

        $now = new \DateTime();

        $term_start = $now;
        $term_end   = $now;

        if(key_exists('term',$data)){
            $term = $data['term'];
            $term_start = Period::getTermStart($term); 
            $term_end   = Period::getTermEnd($term);
        }
        
        //period from, start of office term will not be changed
        //only set it if it's null
        if($officeAssignment->getPeriodFrom()==null){
            $from_period=Period::createFromDate($term_start);
            if($now > $term_start){
                $from_period=(Period::createFromDate($now));
            }
            $officeAssignment->setPeriodFrom($this->entityManager->find('\Application\Entity\Period',$from_period));            
        }
        
        $to_period = (Period::createFromDate($term_end));
        if(key_exists('expires_period',$data) && !empty($data['expires_period'])){
            $to_period = $data['expires_period'];
        }
        #error_log('Period to is '.$to_period.' Drived from Term '. $data['term']);
        
        $period_to_saved = $this->entityManager->find('\Application\Entity\Period',$to_period);

        $officeAssignment->setPeriodTo($period_to_saved);

        return $officeAssignment;        
    }
    
    public function createOfficeAssignmentFromRequest($request,$user,$status='active'){ 

        $data=array();
        $data['branch']=$request->getBranch();
        $data['department']=$request->getDepartment();
        $data['term']=$request->getTerm();
        $data['expires_period']=$request->getExpiresPeriod();

        $data['user']=$user;
        $data['status']=$status;
        
        return $this->createOfficeAssignment($data);
    }
    
    
    public function createOfficeAssignment($data){        
        /**
         * @var OfficeAssignment
         */
        $officeAssignment  = $this->entityFactory->getOfficeAssignment();
        
        $officeAssignment->setBranch($data['branch']);        
        $officeAssignment->setDepartment($data['department']);
        $officeAssignment->setUser($data['user']);
        $officeAssignment->setStatus($data['status']);
        
        $dept=$data['department'];
        $configSrv = $this->serviceLocator->get('ConfigService');
        if(!is_object($dept) ){
            $dept = $configSrv->getDepartment($dept);
        }
        
        $additional_depts = $configSrv->getConfig('additional_departments');        
        
        if( key_exists($dept->getId(), $additional_depts)){
            
            $additional_depts = $additional_depts[$dept->getId()];
            
            if(key_exists('supervise',$additional_depts)){
                $officeAssignment->setSuperviseDepartments($additional_depts['supervise']);
            }
            
            if(key_exists('oversee',$additional_depts)){
                $officeAssignment->setOverseeDepartments($additional_depts['oversee']);
            }                        
        }
        
        // set periods
        $officeAssignment = $this->updateOfficeAssignmentPeriod($officeAssignment,$data);
        
        return $officeAssignment;
    }

    private function toArray($data){
        
        $data_array=array();

        if( !is_array( $data ) && !($data instanceof \Traversable) ){
            
            $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods;
            
            $data_array = $hydrator->extract($data);
            
        }else{
            $data_array = $data;
        }
        return $data_array;
    }     
    
    public function updateOfficeAssignmentRequest($data){
        
        $request = null;
        if($data['request_id'] && is_numeric($data['request_id'])){
            $request = $this->getOfficeAssignmentRequest($data['request_id']);
        }
        else{
            $request = $this->entityFactory->getOfficeAssignmentRequest();           
        }

        $request = $this->updateRequestedPropertiesFromData($request,$data);
     
        $this->entityManager->persist($request);
        $this->entityManager->flush();
        
        //notify the parent GS
        $notify_office  = $this->getBranchDepartmentOffice($request->getBranch()->getParent(), $this->serviceLocator->get('ConfigService')->getConfig('gs_department_name'));
        if($notify_office==null && $request->getBranch()->getBranchLevel()=='Markaz'){
            $notify_office  = $this->getBranchDepartmentOffice($request->getBranch(), $this->serviceLocator->get('ConfigService')->getConfig('gs_department_name'));
        }
        $emailSrv = $this->serviceLocator->get('EmailService');
        $emailSrv->sendTemplatedEmail('Office Assignment Request from AMJ Reporting System ',$notify_office->getUser()->getEmail(),'office_assignment_request',
                                     array('user'=>$notify_office->getUser(),'request'=>$request,'data'=>$data));
                     
        return $request;
         
    }

    public function updateOfficeAssignment($office){
    	
        $this->entityManager->transactional(function($em) use(&$office){
           $em->persist($office);
        });
		    	
	}

    public function updateOfficeAssignmentStatus($office,$office_status){

		$user = $office->getUser();
		$update_user_status=false;
		if($user){
			$update_user_status=true;
			if($office_status=='disabled'){
				$active_offices = $this->getActiveOffices($user);
				//if user have other offices assigned don't change user status
				if(count($active_offices)>1){
					$update_user_status=false;
				}
			}
		}
		if($update_user_status){
			$user->setStatus($office_status);
		}
		$office->setStatus($office_status);
		
        $this->entityManager->transactional(function($em) use(&$office,&$user){
           $em->persist($office);
		   $em->persist($user);
        });
		
		return true;
		    	
	}

    public function getBranchDepartmentOffice($branches,$department,$status = array('active')){
        
        if(!is_array($branches)){
            $branches=array($branches);
        }
        
        $repo = $this->entityManager->getRepository('Application\Entity\OfficeAssignment');

        $dept_criteria = is_numeric($department)?'department.id = :department':'department.department_name = :department';

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('office')
        
           ->from('\Application\Entity\OfficeAssignment','office')
           
           ->join('office.department','department')
           ->join('office.branch','branch')
           ->join('office.period_to','priodTo')
           ->where("office.status in (:status) ")
           ->andWhere($dept_criteria)
           ->andWhere('branch in (:branches) ')
           ->setParameter('status',$status)
           ->setParameter('department',$department)
           ->setParameter('branches',$branches)
           ->orderBy('priodTo.period_end','DESC');
           ;
        
        
        $query=$qb->getQuery();

        $office = $query->execute();
        
        #error_log('FOUND OFFICES '.count($office));
        // $markaz = false;
        // if(  (is_object($branch) && $branch->getBranchName()=='Markaz') || is_numeric($branch) == 96){
            // $markaz=true;
            // $office=array($repo->find(2917));
        // }
        // if( count($office)!=1 && (!$markaz)){
            // throw new \Exception(sprintf("Unable to get unique offices of %s for %s, we found %i",$department_name,($branch?$branch->getBranchName():''),count($office)) );
        // }        
        return ($office&&count($office)>0)?$office[0]:null;          
    }
    
    private function updateRequestedPropertiesFromData(OfficeAssignmentRequest $request,$data){
        
        $dept = $this->serviceLocator->get('ConfigService')->getDepartment($data['department']);
        $branch = $this->serviceLocator->get('BranchManagementService')->getBranch($data['branch']);
        //TDOO FIXME $start_period = $this->serviceLocator->get('ConfigService')->getPeriod($data['start_period']);
        //TDOO FIXME $end_period = $start_period->addYears($data['expires_after']);
        //TDOO FIXME $end_period = $this->entityManager->merge($end_period);
        $expires_period = null;
        if(key_exists('expires_period',$data) && !empty($data['expires_period'])){
            $request->setExpiresPeriod($data['expires_period']);
        }
                
        $request->setFullName($data['full_name']);
        $request->setEmail($data['email']);
        $request->setMemberCode($data['member_code']);
        $request->setPhonePrimary($data['primary_phone']);
        $request->setPhoneAlternate($data['alt_phone']);
        $request->setRequestReason($data['request_reason']);
        if($request->getRequestedBy() == null){
            $request->setRequestedBy($this->serviceLocator->get('UserProfileService')->getCurrentUser());
        }

        $request->setBranch($branch);
        $request->setDepartment($dept);
        $request->setTerm($data['term']);
        
        //TDOO FIXME $officeAssignment->setPeriodFrom($start_period);        
        //TDOO FIXME $officeAssignment->setPeriodTo($end_period);                                
        //TDOO FIXME $request->setOfficeAssignment($officeAssignment);        
                
        $this->entityManager->persist($request);
        
        return $request;        
    }

    
    public function getCurrentOffices($user_id,$status=array('active','disabled')){
        /**
         * @var \Doctrine\ORM\QueryBuilder
         */
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('office')
        
           ->from('\Application\Entity\OfficeAssignment','office')
           
           ->join('office.user','user')
           ->join('office.department','department')
           ->join('office.branch','branch')
           ->join('office.period_to','period_to')
           ->where("office.status in ( :status ) ")
           ->setParameter('status', $status)
           ->andWhere('user = :user')
           ->setParameter('user',$user_id)
           ->andWhere('period_to.period_end >= :periodEnd')
           ->setParameter('periodEnd',new \DateTime());
           ;
    
        $q=$qb->getQuery();
        $result=$q->execute();
        
        return $result;        
    }
    
    public function getActiveOffices($user_id,$branch=null,$department=null,$supervise=false){
        
        // $repo = $this->entityManager->getRepository('Application\Entity\OfficeAssignment');
        // $offices= $repo->findBy(array('status'=>'active','user'=>$user_id));    

        /**
         * @var \Doctrine\ORM\QueryBuilder
         */
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('office')
        
           ->from('\Application\Entity\OfficeAssignment','office')
           
           ->join('office.user','user')
           ->join('office.department','department')
           ->join('office.branch','branch')
           ->join('office.period_to','period_to')
           ->where("office.status = 'active'")
           ->andWhere('user = :user')
           ->setParameter('user',$user_id)
           ->andWhere('period_to.period_end >= :periodEnd')
           ->setParameter('periodEnd',new \DateTime());
           ;

        if($supervise){
            $expr_builder=$qb->expr();
            $orX = $expr_builder->orX();

            //create  OR department is give dept or given dept is in uspervise
            //create  OR department is give dept or given dept is in uspervise
            $orX->add($expr_builder->eq('department',':department'))
                ->add($expr_builder->gt('FIND_IN_SET(:supervise_departments,office.supervise_departments)',0));
            
            $qb->andWhere($orX);
            
            $qb->setParameter('supervise_departments',$department);
            $qb->setParameter('department',$department);
        }
        elseif($department && !($supervise) ){
            $qb->andWhere('department = :department')
            ->setParameter('department',$department);

        }

        if($branch){
           $qb->andWhere('branch = :branch')
           ->setParameter('branch',$branch);            
        }     
    
        $q=$qb->getQuery();

        error_log(($q->getSql()));

        $result=$q->execute();
        
        return $result;    
    }
    

    /**
     * $force_third_level: 'force-grandchildren', 'force-no-grandchildren', ''
     */
    public function getBranchesWithOffices($user_id,$include_child=true, $departments=null, $force_third_level=''){
        
       $branch_srv = $this->serviceLocator->get('BranchManagementService');
       $office_list = $this->getActiveOffices($user_id);
	   $user = $this->serviceLocator->get('UserProfileService')->getUserById($user_id);
       $branches_with_offices=array();
       
       foreach($office_list as $ind=>$office) {

            if( !empty($departments) ) {
                if( !in_array($office->getDepartment()->getId(), $departments) ) {
                    continue;
                }
            }

           //add this office's dept 
           $dept_list=array($office->getDepartment()->getId());
           
               
           if(count(array_diff($this->EMPTY_ARRAY,$office->getSuperviseDepartments())) ){
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
           	   //get immediate children
               $child_branches = $branch_srv->getChildBranches($office->getBranch());
			   $first_level=$child_branches;
			   // if user has "admin" role 
			   
			   if($force_third_level!='force-no-grandchildren' && ($user->hasRole('admin') || $user->hasRole('sys-admin') ||  $user->hasRole('national-secretary') || $force_third_level==='force-grandchildren')){
				   //traverse next level for children
				   foreach($first_level as $child){
				       $child_branches = array_merge($child_branches,$branch_srv->getChildBranches($child));
				   }
			   }
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
    
    
    public function getAdminBranchOffices(){
       
       $usr_prf_srv=$this->serviceLocator->get('UserProfileService');
       $user = $usr_prf_srv->getCurrentUser();
       
       $roles = $user->getRoles();  
       
       return $roles;
       
       exit;
       
       $branch_srv = $this->serviceLocator->get('BranchManagementService');
       $all_branches = $branch_srv->listBranchNames();
       $all_departments = $this->serviceLocator->get('ConfigService')->listDepartmentNames();
       
       $branch_departments=array();
       foreach ($all_branches as $bid => $bname) {                  
           $branch_departments[$bid] = array_keys($all_departments);
       }
       return $branch_departments;  
    }
    public function listRequestsForStatus($status,$currentUser){
            
        $offices = $this->getBranchesWithOffices($currentUser);
        $branches = array_keys($offices);
        
        $branch_srv = $this->serviceLocator->get('BranchManagementService');
        $child_branches = $branch_srv->getChildBranches($branches);
        
        $repo = $this->entityManager->getRepository('Application\Entity\OfficeAssignmentRequest');
        
        $requests= $repo->findBy(array('status'=>$status,'branch'=>array_merge($child_branches,$branches) ) );    
        
       
        return $requests;    
    }
    
    public function getRequestsDataSource($status,$currentUser){
            
        $offices = $this->getBranchesWithOffices($currentUser);
        $branches = array_keys($offices);
        
        $branch_srv = $this->serviceLocator->get('BranchManagementService');
        $child_branches = $branch_srv->getChildBranches($branches);
        
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('request')
        
           ->from('\Application\Entity\OfficeAssignmentRequest','request')
           ->join('request.department','department')
           ->join('request.branch','branch')

           ->where(" 1 = 1 ")//selected office is active
           ->andWhere("request.status = :status")//selected office is active
           ->setParameter('status',$status)
           
           ->andWhere('request.branch in (:branches)')//and not expired
           ->setParameter('branches',array_merge($child_branches,$branches))
          ;
               
        return new GridDataSource($qb);    
    }

    
    public function getUserLevel($user){
        $offices = $this->getActiveOffices($user);
        $level='';
        foreach($offices as $office){
            
            $designation=$office->getBranch()->getOfficeBearerDesignation();
            $sub_branches=$office->getBranch()->getSubBranches();

            if($designation=='National'){
                $level='national';
                break;
            }elseif($office->getBranch()->hasChildBranches()&&$designation!='National'){
                $level='imarat';
            }elseif($office->getBranch()->getBranchType()=='Halqa' && $level != 'imarat'){
                $level='halqa';
            }elseif($office->getBranch()->getBranchType()=='Jama`at' && $level != 'imarat'){
                $level='jamaat';
            }
        }
        return $level;
    }
    
    public function createOfficeAssignmentDataSource($user_id,$list_type,$status=null){

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('office')
        
           ->from('\Application\Entity\OfficeAssignment','office')
           
           ->join('office.department','department')
           ->join('office.period_from','period_from')
           ->join('office.period_to','period_to')           
           ->join('office.branch','branch')
           ->join('office.user','user')
           ->leftJoin('branch.parent','pbranch')
           ->orderBy('branch.branch_name','ASC')
           ->orderBy('department.sort_order','ASC')
           ;

       // we don't want ORs as first where clause expr
       // otherwise add on expr might cause issues TODO confirm if we really need thi
       //Ignore comment above we are forcing a simple and clause that will be that brnach status is always active
       $qb->where('branch.status = :branch_status ');
       $qb->setParameter('branch_status','active');
#       $qb->andWhere('office.status = :status');
               
       #enforce status if provided
       if(isset($status) && is_string($status) && !empty($status)){
           $qb->andWhere('office.status = :status');
           $qb->setParameter('status',$status);
       }
       if(isset($status) && is_array($status)){
           $qb->andWhere('office.status in :status');
           $qb->setParameter('status',$status);
       }

       $office_branches=array();
       $offices = $this->getActiveOffices($user_id);
       
       foreach ($offices as $ind=>$office) {
                      
           $office_branches[$office->getBranch()->getId()]=$office->getDepartment()->getId();
       }

       /**
        * @var Doctrine\DBAL\Query\Expression\ExpressionBuilder
        */
       $expr_builder=$qb->expr();
       $orX = $expr_builder->orX();

       //create empty OR condition that will not match to any dept or branch
       //this is so that we don't end up with empty clause for branch if no other 
       //clause is added
       $orX->add(
            $expr_builder->andX(
                $expr_builder->eq('branch', 0),
                $expr_builder->eq('department', 0)
            )
       );
               
       
       //print_r(['SHOULD BE HERE!!!',$owen_branches]);exit;
       if($list_type=='same_branch'){
           //add own branch(s)
           foreach ($office_branches as $branch => $dept) {
                              
               $orX->add(
                    $expr_builder->eq('branch', $branch)
               );
           }
       }elseif($list_type=='other_branches'){
           //Add Child branch office
           //print_r(['SHOULD BE HERE!!!',$branches_with_offices]);exit;
           foreach ($office_branches as $branch => $dept) {               
               $orX->add(
                    $expr_builder->andX(
                        $expr_builder->eq('branch.parent', $branch),
                        $expr_builder->eq('department', $dept)
                    )
               );
           }
       }elseif($list_type=='all'){
           $branches_with_offices = $this->getBranchesWithOffices($user_id);
           foreach ($branches_with_offices as $branch => $dept) {
               $orX->add(
                    $expr_builder->andX(
                        $expr_builder->eq('branch', $branch),
                        $expr_builder->in('department', array_values($dept))
                    )
               );
               $orX->add(
                    $expr_builder->andX(
                        $expr_builder->eq('branch', $branch),
                        $expr_builder->gt('department', 0)
                    )
               );
               $orX->add(
                    $expr_builder->andX(
                        $expr_builder->eq('pbranch.parent', $branch),
                        $expr_builder->in('department', array_values($dept))
                    )
               );
           }
       }

       $qb->andWhere($orX); //– use of orX/andX Lou Terrailloune Oct 15 '12 at 13:45  from StackOverflow
        

        //same branch test
        //$qb->andWhere($expr_builder->neq('branch', $offices[0]->getBranch()->getId()));

        //error_log($qb->getDQL());
       //print_r(['<pre>',$list_type,$qb->getQuery()->getSql(),'</pre>']);exit;
                
       $data_source = new GridDataSource($qb);
        
       // print_r($list_type);        
       // print_r($qb->getQuery()->getSql());        
       return $data_source;
               
    }


    public function filteredOfficeAssignmentList($current_user,$filters){
            
        $qb = $this->createOfficeAssignmentDataSource($current_user,'all');
        
        //if( is $filters[]){
            
        //}
        
    }
    public function getOfficeRoles($user){  
        
        $offices = $this->getActiveOffices($user);
        $roles = array();
        $office_roles = $this->serviceLocator->get('ConfigService')->getConfig('office_roles');
        $markaz_branch =  $this->serviceLocator->get('ConfigService')->getConfig('markaz_branch');
        //print_r(['<pre>',$office_roles,'</pre>']);exit;
        //print_r(['<pre>',$roles,'</pre>']);
        foreach ($offices as $office) {
            $branch = $office->getBranch();
            //no parent or parent of self
            $branch_national = $branch->getParent() == null || $branch->getBranchLevel()=='Markaz';
            //assume role level is halqa/jammat 
            $role_level = 'halqa_jamaat';
            if($branch_national){
                $role_level = 'national';
            }
            //not national and has children then local ammarat otherwise branch or halqa
            if($branch->hasChildBranches() && (! $branch_national)){
                $role_level = 'amarat';
            }
            //print_r(['<pre>', '$role_level'=>$role_level,'</pre>']);
            $role_dept='all';
            //if a specifc role is defined for dept pick that
            //print_r($office_roles);exit;
            if(in_array($office->getDepartment()->getId(),array_keys($office_roles[$role_level]))){
                $role_dept=$office->getDepartment()->getId();
            }
           if(is_array($office_roles[$role_level][$role_dept])){
               $roles= array_merge($roles,$office_roles[$role_level][$role_dept]);
           }
        }
        
        //print_r(['<pre>',$user->getDisplayName(),$roles,'roles_config'=>$office_roles,'</pre>']);exit;
        
        if(count($roles)>0){
            //add user role as well
            $roles[]=2;
        }


        $rolesObj = $this->entityManager->createQueryBuilder()
             ->select('role')
             ->from('\\Application\\Entity\\Role','role')
             ->where(' role.id in (:roles)')
             ->setParameter('roles',$roles)
             ->getQuery()->execute()
        ;
        
        return $rolesObj;
        //return $this->serviceLocator->get('EntityService')->findBy('Role',array('id'=>$office_roles));
    }


    public function getAllOfficeRoles($user){
        
        $offices = $this->getActiveOffices($user);
        $roles = array();
        $office_roles = $this->serviceLocator->get('ConfigService')->getConfig('office_roles');
        
        $markaz_branch =  $this->serviceLocator->get('ConfigService')->getConfig('markaz_branch');
        //print_r(count($offices));exit;
        foreach ($offices as $office) {
            $branch = $office->getBranch();
            //no parent or parent of self
            $branch_national = $branch->getParent() == null || $branch->getParent()->getBranchLevel()=='Markaz';
            //assume role level is halqa/jammat 
            $role_level = 'halqa_jamaat';
            if($branch_national){
                $role_level = 'national';
            }
            //not national and has children then local ammarat otherwise branch or halqa
            if($branch->hasChildBranches() && (! $branch_national)){
                $role_level = 'amarat';
            }
            $role_dept='all';
            //if a specifc role is defined for dept pick that
            //print_r(array_keys($office_roles[$role_level]));
            if(in_array($office->getDepartment()->getId(),array_keys($office_roles[$role_level]))){
                $role_dept=$office->getDepartment()->getId();
            }
           
           $new_roles_to_add=$office_roles[$role_level][$role_dept];
           if(!is_array($new_roles_to_add) ){
               $new_roles_to_add=array($new_roles_to_add);
           }
           $roles= array_merge($roles,$new_roles_to_add);
        }
        if(count($roles)>0){
            //add user role as well
            $roles[]=2;
        }
        

        $roles = $this->entityManager->createQueryBuilder()
             ->select('role')
             ->from('\\Application\\Entity\\Role','role')
             ->where(' role.id in (:roles)')
             ->setParameter('roles',$roles)
             ->getQuery()->execute()
        ;


        $entity_srv = $this->serviceLocator->get('EntityService');        
        $all_roles = array();
        
        foreach($roles as $role){
            
            if( ! is_object($role) ){
                $role = $entity_srv->getObject('Application/Entity/Role',$role);
            }
            $all_roles[$role->getRoleId()]=$role;
            
            $parent = $role->getParent();
            //add all parent hierarchy
            while($parent!=null && ! key_exists($parent->getRoleId(), $all_roles)){
                $all_roles[$parent->getRoleId()]=$parent;
                $parent=$parent->getParent();
            }
        }
        
        return array_values($all_roles);   

        //return $this->serviceLocator->get('EntityService')->findBy('Role',array('id'=>$office_roles));
    }

    public function getAllRoles($user){
            
        $roles = array_merge($user->getRoles(),$this->getOfficeRoles($user));
        $all_roles=array();
        
        foreach($roles as $role){
            $all_roles[$role->getRoleId()]=$role;
        }
        
        return $all_roles;
    }




    /**
     * returns list of recipients of a message given basic office list query builder and filters 
     */
    public function getReminderReciepientList($user,$filters){
        
        //given user is president or GS
        //TODO ASSUMES that given user has GS or President dept
        
        //offices that given user can control
        $activeOffices = $this->getBranchesWithOffices($user);
        
        /**
        * @var \Doctrine\ORM\QueryBuilder
        */
        $queryBuilder =  $this->entityManager->createQueryBuilder();
        
        $queryBuilder->select('office')        
           ->from('\Application\Entity\OfficeAssignment','office')           
           ->join('office.department','department')
           ->join('office.period_to','office_period_to')           
           ->join('office.branch','office_branch')
           ->join('office.user','user')
           ->where("office.status = 'active'")//selected office is active
           ->andWhere('office_period_to.period_end >= :periodEnd')//and not expired
           ->setParameter('periodEnd',new \DateTime())           
           ->andWhere('department.reportable = :reportable ')
           ;
                
        //branches                
        $final_branches=array();
        if( isset($filters['branch']) && !(empty($filters['branch'])) && $filters['branch']!='all'  ) {
            $brn = $filters['branch'];
            if ( ! is_array($brn)){
                $brn = explode(',',$brn);
            }
            $queryBuilder->andWhere("office_branch in ( :branch ) ");
            $queryBuilder->setParameter('branch', $brn);            
            $final_branches=$brn;
        }else{
            //restrict by access
            //given user have access to offices in these branches
            $queryBuilder->andWhere('office_branch in (:office_branch)')
            ->setParameter('office_branch',array_keys($activeOffices)); 
        }
        
        //Level_Rule        
        $final_level='';
        if( isset($filters['level_rule'])  && $filters['level_rule'] != 'all' ) {
            $whereClause=' office_branch.branch_level = :branch_level';
            $parameter=ucfirst($filters['level_rule']);   
            if($filters['level_rule']=='local'){
                $whereClause=' office_branch.branch_level not in (:branch_level) ';
                $parameter = array('Markaz','Halqa');
            }
            $final_level=array($whereClause,$parameter);
            $queryBuilder->andWhere($whereClause);
            $queryBuilder->setParameter('branch_level', $parameter);            
        }
        
        //A set of department is selected 
        if(! is_array($filters['office'])){
            $filters['office']=explode(',',$filters['office']);
        }

        //Send_to_Rule
        $filters['send_to_rule']=isset($filters['send_to_rule'])?$filters['send_to_rule']:'';
        $reportable=1;//by default we will email to only those dept that submit reports
        
        $report_status=array();    
        $recipient='owner';
        $notExists=false;
        switch ($filters['send_to_rule']) {
            case 'not_completed':
                //not_completed
                $report_status=array('completed','verified','received');
                $notExists=true;
                break;

            case 'not_verified':
                $report_status = array('completed');
                $reportable=0;   
                $recipient='president';     
                //for president we want any dept report
                // $presidentDept = $this->serviceLocator->get('ConfigService')->getConfig('president_department');
                // $queryBuilder->andWhere('department = '.$presidentDept);
                $reportable=0;
                break;

            case 'not_received':                                
                $report_status = array('verified');
                $recipient='parent';
		          $reportable=0;
                break;

            default:                
                break;
        }
          
        $subQuery = $this->getReportStatusSubQuery($recipient,$filters,$report_status);

        
        if($notExists){
            $subQueryExpr = $queryBuilder->expr()->not(
                                $queryBuilder->expr()->exists( $subQuery )
                            );
        }else{
            $subQueryExpr = $queryBuilder->expr()->exists( $subQuery );
        }

        foreach ($subQuery->getParameters() as $index => $param){              
            $queryBuilder->setParameter($param->getName(),$param->getValue());
        } 

        //status based filter is provided
        //use subquery to filter list of recipients for the given report status 
        if(!empty($report_status)){
            
            $queryBuilder->andWhere( $subQueryExpr );

            ## Moved Parameter setting to $this->getReportStatusSubQuery()
            //$subQuery->andWhere('report.status '.$operator.' (:report_status)');
            // $queryBuilder->setParameter('report_period',$filters['period']);
            // $queryBuilder->setParameter('report_status',$report_status);
            // $queryBuilder->setParameter('report_dept',$filters['office']);

        }
        

        $Recipient_depts=array();
        #filter selection of target offices
        if($recipient == 'president' ) { //first address if recipient is president
	        $presidentDept = $this->serviceLocator->get('ConfigService')->getConfig('president_department');
            $queryBuilder->andWhere("department in ( :department )");
            $queryBuilder->setParameter('department', $presidentDept);
            $Recipient_depts=array($presidentDept);
        }elseif( isset($filters['office']) && !empty($filters['office']) && $filters['office'] != 'all' ) {
            $Recipient_depts = $filters['office'];
            $queryBuilder->andWhere("department in ( :department )");
            $queryBuilder->setParameter('department', $filters['office']);
            
        }elseif( isset($filters['office']) && $filters['office'] == 'all' ) {
            // We are sending to all departments
            //Flatten ative offices array by merging array of arrays
            $depts = call_user_func_array('array_merge',array_values($activeOffices) );
            //remove duplicate depts
            $depts = array_unique($depts);
            $Recipient_depts=$depts;
            //filter by dept
            $queryBuilder->andWhere("department in (:departments) ");
            $queryBuilder->setParameter('departments', $depts);
        }else{
            //no filtering for dept
            error_log('UNABLE TO DETERMIN DEPARTMENTS *** LOOKS TROUBLE');
        }

        //reportable dept only if requested
        $queryBuilder->setParameter('reportable', $reportable);
        
                
        $query = $queryBuilder->getQuery();

	#error_log('EMAIL RESULTS:::'.$query->getSql());
	#error_log(print_r(array('EMAIL RESULTS PARAMs:::',$query->getParameters()),true));
    // $query = $queryBuilder->select('count(office.id)')->getQuery();
    // print('<pre>');
    // print_r(array(
    //             'SQL'=>$query->getSql(),
    //             'Level'=>$final_level,
    //             'SubQuery'=>$notExists,
    //             'Recipient'=>$recipient,
    //             'TargetStatus'=>implode(',',array_values($report_status)),
    //             'reportable' =>$reportable,
    //             'Recipient_depts'=>implode(',',array_values($Recipient_depts)),
    //             'Report_depts'=>implode(',',array_values($filters['office'])),
    //             'final_branches'=>implode(',',array_values($final_branches)),
    //             'INPUT'=>$filters
    //             )
    //         );
    // print('/<pre>');
    ##
    // exit(1);

        $result = $query->getResult();
        return $result;                
    }


    private function  getReportStatusSubQuery($recipient,$filters,$report_status){
        $subQueryBuilder = $this->entityManager->createQueryBuilder();
        
        $subQueryBuilder->select('report.id')
                        ->from('\Application\Entity\Report','report')
                        ->join('report.department','report_dept')
                        ->join('report.branch','report_branch')                      
                        ->where('report.period_from = :report_period')
                        ->setParameter('report_period',$filters['period']);
        ;   
        switch ($recipient){
            case 'president':
                $subQueryBuilder->andWhere('report_branch = office.branch');
                break;   
            case 'parent':
                $subQueryBuilder->andWhere('report_dept = office.department')
                                ->andWhere('report_branch.parent = office.branch');
                break;                            
            case 'owner':                
            default:                
                $subQueryBuilder->andWhere('report_dept = office.department')
                                ->andWhere('report_branch = office.branch');
                break;   
        }

        //status based filter is provided
        //use subquery to filter list as requried
        if(!empty($report_status)){            
            $subQueryBuilder->andWhere('report.status in (:report_status)');
            $subQueryBuilder->setParameter('report_status',$report_status);
        }

 	    //office
        if( isset($filters['office']) && !empty($filters['office']) && $filters['office'] != 'all' ) {
            if(! is_array($filters['office'])){
              $filters['office']=explode(',',$filters['office']);
            }
            $subQueryBuilder->andWhere("report_dept in ( :report_dept )");
            $subQueryBuilder->setParameter('report_dept', $filters['office']);
        } 

        return $subQueryBuilder;
    }


    public function canAccess($branches,$departments=array()){
            
        $have_access=true;
        //get current user
        $current_user = $this->serviceLocator->get('UserProfileService')->getCurrentUser();
        $branches_with_offices = $this->getBranchesWithOffices($current_user);
        
        if(!is_array($branches)){
            $branches=array($branches);   
        }
        if(!is_array($departments)){
            $departments=array($departments);   
        }
        $dept_ids=array();
        foreach ($departments as $dept) {
            if(is_object($dept))
                $dept_ids[]=$dept->getId();
            else
                $dept_ids[]=$dept;
        }

        if(!empty($branches_with_offices) && is_array($branches_with_offices)){
            foreach ($branches as $branch) {
                $dept_intersect=array_intersect($dept_ids, $branches_with_offices[$branch->getId()]);
                //user hold a office in given branch and dept or have access to that office
                if(key_exists($branch->getId(), $branches_with_offices) && (count($departments)==0 || count($dept_intersect)>0)
                  ){
                    $have_access= ($have_access && true);//if there is access to previously checked reports add access for this report
                }else{
                    $have_access=false;//stop access to all reports
                }
            }
        }else{
            error_log('$branches_with_offices is empty or not array not granting access to '.$current_user->__toString(),' for '.print_r(array($branches,$departments),true ) );
            $have_access=false;
        }
        
        return $have_access;                
    }    

    /**
     * Expects $data to have following fields
     * 
     * $data['member_code']
     * $data['full_name']
     * $data['phone_primary']
     * $data['phone_alternate']
     * $data['email']
     * $data['username']
     * $data['branch_id']
     * $data['department_id']
     * $data['term']
     * 
     */
    public function processAMIAamilaSynch($data){

        $cfgService=$this->serviceLocator->get('ConfigService');
        $usr_prf_srv=$this->serviceLocator->get('UserProfileService');
        $branch_srv = $this->serviceLocator->get('BranchManagementService');

        //check to see if user exists, otherwise w need to create one
        $user_message='approved';
        /**
         * @var User
         */
        $user = $usr_prf_srv->getUserByMembercode($data['member_code']);        
    

        if(! $user){
            $data['status']='active';
            
            $user = $usr_prf_srv->createUser($data,false);
            $datetime = $user->getPasswordExpiryDate();
            $datetime->add(new \DateInterval("P10Y"));
            $user->setPasswordExpiryDate($datetime);
            $user_message='new_user';
        }
        //save user
        $this->entityManager->persist($user);
        $branch = $branch_srv->getBranch($data['branch_id']);
        $department = $cfgService->getDepartment($data['department_id']);
        //if there is already an office for this branch/department we need to cancel that            
        $existingOffice = $this->getBranchDepartmentOffice($branch, $data['department_id'],array('active','disabled') );
        #error_log('Found Office '. $existingOffice);
        //create office Assignment            
        $officeAssignment = null;
                
        if($existingOffice && $existingOffice->getUser()->getId() == $user->getId()){
            //request is to assign office to same user again
            //we will simple update office details to new allocation
            $officeAssignment  = $existingOffice;
            $officeAssignment->setStatus('active'); 
            // set periods
            $this->updateOfficeAssignmentPeriod($officeAssignment,$data);
            $this->entityManager->persist($existingOffice);
            #error_log('Office assigned to same user');
        }else if ($existingOffice){
            //we need to cancel existing office
            //by changing to date and PeriodTo last month period
            $last_period = Period::createLast();
            $existingOffice->setPeriodTo($this->entityManager->find('\Application\Entity\Period',$last_period) );
            $existingOffice->setStatus('former');
            $this->entityManager->persist($existingOffice);    
            #error_log('Office assigned to NEW user, changed office status');
            // TODO 
            // Insert Transitionmodule here
            //
        }                                        
    
        //This office was never assigned
        //OR last assignment was not active        
        if(! $officeAssignment ){
            #error_log('CREATE office, as assinged assigned to NEW user');
            //create new office assignment
            $data['user']=$user;
            $data['branch']=$branch;
            $data['department']=$department;
            $data['status']='active';
            
            $officeAssignment  = $this->createOfficeAssignment($data);
            $officeAssignment->setUser($user);
        }            
        
        $this->entityManager->persist($officeAssignment);
        
        $this->entityManager->flush();
    
        $data['office_id'] = $officeAssignment->getId();
        $data['period_to'] = $officeAssignment->getPeriodTo()->getPeriodCode();
        return $data;
    }    

}
