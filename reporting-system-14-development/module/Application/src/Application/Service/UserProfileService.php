<?php


namespace Application\Service;

use Application\Entity\UserToken;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Doctrine\ORM\Query;

use Rhumsaa\Uuid\Uuid;


class UserProfileService implements FactoryInterface{
    
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
        
        return $this;
    }

    public function addLoginHistory($user,$method,$status){
        $userLogin = $this->entityFactory->getUserLogin();
        $userLogin->setLoggedInAs($user);
        $userLogin->setUser($user);
        $userLogin->setAuthResult($status);
        $userLogin->setAuthMethod($method);
        $userLogin->setLoginTime(new \DateTime());

        $user->addUserLogin($userLogin);

        $this->entityManager->transactional(function($em) use(&$user, &$userLogin){
            $em->persist($user);
            $em->persist($userLogin);
         });
         
         $this->entityManager->flush();
         return true;     
    }

    public function updateEmail($user,$data){
        
        
        if($this->canPerform($user,'email', 'update')){
        	
			$user->setEmail($data['email']);

            $this->entityManager->transactional(function($em) use(&$user){
               $em->persist($user);
            });
			
            $this->entityManager->flush();
			return true;
		}else{
			return false;
		}
	}	

 	public function updateMembercode($user,$data){
               
        if($this->canPerform($user,'email', 'update')){ //if user can update email they can also update membercode 
        	
			$user->setMemberCode($data['membercode']);

            $this->entityManager->transactional(function($em) use(&$user){
               $em->persist($user);
            });
			
            $this->entityManager->flush();
			return true;
		}else{
			return false;
		}
	}	

 	public function updateUsername($user,$data){
               
        if($this->canPerform($user,'email', 'update')){ //if user can update email they can also update username 
        	
			$user->setUsername($data['username']);

            $this->entityManager->transactional(function($em) use(&$user){
                $existing_user = $this->getUserByUsername($user->getUsername());
                if($existing_user){
                    throw new \Exception('A user already exists with username '.$user->getUsername().' but have Membercode '.$existing_user->getMemberCode());
                }
               $em->persist($user);
            });
			
            $this->entityManager->flush();
			return true;
		}else{
			return false;
		}
	}	

	public function updatePhoneName($user,$data){
        
        if( trim($data['primaryphone']) == $user->getPhonePrimary() && trim($data['alternatephone']) == $user->getPhoneAlternate() && trim($data['display_name']) == $user->getDisplayName()){
        	return;//no change no need to update
        }
        if($this->canPerform($user,'phone', 'update')){
        	
            if(!empty($data['primaryphone']) ){
                $user->setPhonePrimary( $data['primaryphone']);
            }
            if(!empty($data['alternatephone'])){
			    $user->setPhoneAlternate($data['alternatephone']);
            }
            
            if(!empty($data['display_name'])){
    		    $user->setDisplayName($data['display_name']);
            }
            
            $this->entityManager->transactional(function($em) use(&$user){
               $em->persist($user);
            });
			
            $this->entityManager->flush();
			return true;
		}else{
			return false;
		}
	}	
	
    public function updateStatus($user,$data){
        
        
        if($reset||$this->canPerform($user,'status', 'update')){
        	
			$user->setStatus($data['user_status']);

            $this->entityManager->transactional(function($em) use(&$user){
               $em->persist($user);
            });
			
            $this->entityManager->flush();
			return true;
		}else{
			return false;
		}
	}	

    public function updatePassword($user,$data,$reset=false){
        
        
        if($reset||$this->canPerform($user,'password', 'update')){
            
            $zfc_user=$this->serviceLocator->get('zfcuser_user_service');
            $bcrypt = new \Zend\Crypt\Password\Bcrypt;
            $bcrypt->setCost( $zfc_user->getOptions()->getPasswordCost());
            
            //atleast one of userpassword or user_token or current_user need to be in data
            if( !(key_exists('userpassword', $data) ||  key_exists('user_token', $data)  || key_exists('current_user', $data) ) ){
                //so we can vlaidate user authnetication
                return false;    
            }
            
            if( key_exists('userpassword', $data) && !$bcrypt->verify($data['userpassword'],$user->getPassword())){
                return false;    
            }
            //password matches with old password
            if( key_exists('member_code', $data) && $data['member_code']!=$user->getMemberCode()){
                return false;    
            }

            //password matches with old password
            if( $data['newpassword']!=$data['confirmpassword']){
                return false;    
            }

			if(key_exists('current_user', $data) && !$this->canPerform($user,'password','update')){
				return false; 
			}
			
            //validate user token if it exists
            $user_token=null;
            if(key_exists('user_token', $data)){
                $user_token = $this->getUserTokenByUUID($data['user_token']);
                
                if(!$user_token || $user_token->isExpired()){
                    return false;
                    
                }else if($user_token){
                    $user_token->expireToken();
                }
            }
            
            //changePassword                    
            $user->setPassword($bcrypt->create($data['newpassword']));
            $exp = new \DateTime();
            $interval = \DateInterval::createfromdatestring('+200 month');
            $exp=$exp->add($interval);
            $user->setPasswordExpiryDate($exp);
            
            $this->entityManager->transactional(function($em) use(&$user,&$user_token){
               $em->persist($user);
               if($user_token){
                   $em->persist($user_token);
               }
            });

            $this->entityManager->flush();
            return true;
        }else{
            throw new \Exception('Current user is not allowed to update given user');
        }
    }

    public function getUserByMigratedUsername($username){
            
        $username = strtolower($username); 
        
        if(empty($username)){
            return null;
        }
        $users =   $this->entityManager
                    ->createQuery('select user from Application\Entity\User user where user.migrated_user_id like :username')
                    ->setParameter('username','%'.$username.'%')
                    ->getResult();
        $user_found=null;
        if($users && count($users)>0){
            foreach ($users as $user) {
                $names = explode(',', strtolower($user->getMigratedUserId()));
                if(in_array($username,$names) ){
                    $user_found=$user;
                }
            }
        }
        
        return $user_found;
    }
    

    public function getUserTokenByUUID($token_uuid){
        
        $tokens =   $this->entityManager
                    ->createQuery('select token from Application\Entity\UserToken token where token.token = :token')
                    ->setParameter('token',$token_uuid)
                    ->getResult();
                    
        if(count($tokens)!=1){
            //there should not be only one  record per token uuid
            //if we have more than one or none some thing is wrong
            //either hacking attempt or invalid token
            return null;
        }
        return $tokens[0];
    }
    
    public function createUserUpdateToken($user,$action){
        
        $token = $this->entityFactory->getUserToken();
        $token->setUser($user);
        $token->setAction($action);
        
        $token->setDefaultExpiry();

        $tokenString = (hash('crc32', $user->getUsername()));        
        $tokenString .= Uuid::uuid4()->getHex();

        $token->setToken($tokenString);
        
        $this->entityManager->persist($token);
        $this->entityManager->flush();
        
        return $token;
    }

    protected function sendConfirmationEmail($user){
        
        $token = $this->createUserUpdateToken($user, UserToken::ACTION_CONFIRM);
        
        $emailSrv = $this->serviceLocator->get('EmailService');
        
        throw new \Exception('Implementation of this function is not completed!');       
    }

    public function createUser($data,$sendEmail=true){
        $user = $this->entityFactory->getUser();
        
        $user->setEmail($data['email']);        
        $user->setDisplayName($data['full_name']);
        $user->setMembercode($data['member_code']);
        $user->setPhonePrimary($data['phone_primary']);
        $user->setPhoneAlternate($data['phone_alternate']);

        if($data['username']){
            $user->setUsername($data['username']);
        }else{
            $user->setUsername($this->proposeUserName($data['full_name']));
        }

        if($data['status']){
            $user->setStatus($data['status']);
        }
        
        $zfc_user=$this->serviceLocator->get('zfcuser_user_service');
        $bcrypt = new \Zend\Crypt\Password\Bcrypt();
        $bcrypt->setCost( $zfc_user->getOptions()->getPasswordCost());


        if(!empty($data['password']) ) {
            $user->setPassword($bcrypt->create($data['password']));
        }else{
            $user->setPassword(\Zend\Math\Rand::getString(32));        
        }
        
        $user->setPasswordExpiryDate(new \DateTime());
        $user->setPasswordLastReset(new \DateTime());

        
        $user->setSettings("");
        
        $role = $this->entityManager->find('Application\Entity\Role',2);
        $user->addRole($role);
        
        
        
        //
        // Save user, but before persisting user, make sure there is are no duplicate usernames
        // 
        $this->entityManager->transactional(function($em) use(&$user){
            
            $existing_user = $this->getUserByUsername($user->getUsername());
            if($existing_user){
                throw new \Exception('A user already exists with username '.$user->getUsername().' but have Membercode '.$existing_user->getMemberCode());
            }
            $em->persist($user);
            
        });
        
        $this->entityManager->flush();
        
        if($sendEmail){
            $this->sendConfirmationEmail($user);
        }
        
        return $user;
    }


    public function findUsersBy(array $criteria){
        /**
         * @var Doctrine\ORM\EntityRepository
         */
        $repo = $this->entityManager->getRepository('Application\Entity\User');

        return $repo->findBy($criteria);
    }

    public function findOneUserBy(array $criteria){
        $repo = $this->entityManager->getRepository('Application\Entity\User');
        
        return $repo->findOneBy($criteria);
    }

    public function getUserById($user_id){
        return $this->findOneUserBy(array('id'=>$user_id));
    }

    public function getUsersByEmail($email,$active_only=false){
        $email = strtolower($email);
    	$criteria = array('ehash'=>sha3($email));
		if($active_only){
			$criteria['status']='active';
		}
        return $this->findUsersBy($criteria);        
    }

    public function getUserByUsername($username){
        $username = strtolower($username);
        return $this->findOneUserBy(array('uhash'=>sha3($username)));        
    }
    
    public function getUserByMembercode($membercode){
        return $this->findOneUserBy(array('mhash'=>sha3($membercode)));
        
    }

    public function listUserRoles(){
                                
        $roles=$this->entityManager->createQuery('select r from Application\Entity\Role r')->getResult();
        return $roles;
    }    
    
    
    public function getCurrentUser(){
    	$identity = $this->serviceLocator->get('zfcuser_auth_service')->getIdentity();
		
		if(!is_object($identity)){
			$identity = $this->getUserById($identity);
		} 
        return $identity; 
    }
    
    
    /**
     * Check if the given username is valid for the given name.
     * A username is considered valid if 
     *  - it contains onl alphanumeric or . character
     *  - some part of given name is included in username
     *  - name is not based on department (department name or code)
     *  - no user with given username already exists
     * 
     */
    public function isUserNameValid($user_name,$full_name){
        
        $valid_username_regex='/^[a-zA-Z0-9.]+$/';

        
        //User name contains invalid character
        //only alphanumric and . character are allowed
        if(empty($user_name) || !preg_match($valid_username_regex, $user_name)){
            return false;
        }
    
        //a user with given username already exists
        if($this->getUserByUsername($user_name)){
            return false;
        }
        
        $user_name=strtoupper($user_name);

        $based_on_name=false;

        $based_on_department=false;

        $name_parts = explode(' ', strtoupper($full_name));
        
        //$departments = $this->serviceLocator->get('ConfigService')->listDepartments();

        //$department_codes = array_map(function ($dept) {return strtoupper($dept['department_code']);}, $departments);
        //$department_names = array_map(function ($dept) {return explode(" ",strtoupper($dept['department_name']));}, $departments);

        //guess if the gven given username contains any part of full_name
        //contains any part of 
        foreach ($name_parts as $i => $part) {
            if(empty($part)){
                continue;
            }
            if(strpos($user_name, $part)>=0){
                $based_on_name=true;
            }
            // if(in_array($part, $department_codes)){
                // $based_on_department=true;                
            // }
            //check if name part is in departments array
            // $matched_dept_names = array_map(function($dept_parts) use ($part)
                            // { return in_array($part, $dept_parts);
                            // }
                      // ,$department_names);
            // if(in_array(true, $matched_dept_names)){
                // $based_on_department=true;
            // }
            
        }//for each part of name        
        
        //disable $based_on_department
        $based_on_department=false;
        
        return $based_on_name ;//&& (! $based_on_department);
    }

    /**
     * Propose a user name that is valid for the given name
     * This will pick first name and will append to it initials for other name parts
     * If first name is less than 4 characters  full second name will be added to first name 
     * and inititlas for rest of name parts appended.
     * If a user exists with a username created in above manner a numericall counter is appended to username to get a unique values.
     */
    public function proposeUserName($full_name){

        $full_name = trim($full_name) ;
        $full_name = preg_replace('/  +/', ' ', $full_name);
        if(empty($full_name)){
            throw new \Exception('Can\'t propose username for empty name');
        }
        $unwanted_chars='/[^a-zA-Z0-9]/';

        //guess name parts
        $name_parts=explode(' ',$full_name);
        $first_name=$name_parts[0];

        //clean firstname        
        $user_name=preg_replace($unwanted_chars, '', $first_name);
        
        //if firstname is less than 3 char long add second name to the username
        $combined_name=false;
        if(strlen($user_name)<4){
            $user_name=$user_name.'.'.$name_parts[1];
            $combined_name=true;
        }
        //since username is first.last no need to add second part
        //$combined_name=false;
        
        //add initial from last name only if last name is not already addded to username
        // if( ($combined_name && count($name_parts)>2) ||  !$combined_name){
            // $user_name .= substr(array_pop($name_parts),0,1);            
        // }
        if( (count($name_parts)>1)){
            $user_name .= '.'.preg_replace($unwanted_chars, '', array_pop($name_parts));            
        }
        
		$user_name = strtolower($user_name);        
        //check if name given is valid and can we actullay get a valid username from it        
        $valid_username=$this->isUserNameValid($user_name, $full_name);

        //check if username already exists if it does propose new username by appending a number to end of name
        $already_exists = false;
        $count_already_exists=1;
        $original_username=$user_name;
        do{
            $count_already_exists++;
			$user = $this->getUserByUsername($user_name);
            $already_exists = ($user!=null);
            if($already_exists){
                $user_name="${original_username}${count_already_exists}";                
            }else{
                $valid_username = $this->isUserNameValid($user_name, $full_name);
            }
        }while($already_exists && $count_already_exists<100);
        
        if($already_exists){
            throw new \Exception("Invalid name, unable to find/propose a username $full_name");
        }
        
        if($valid_username && !$already_exists){
            return ($user_name);
        }
        else {
            return null;
        }
    }


    public function canEditOffice(array $editable_branches){
         $current_user = $this->getCurrentUser();
          //when action on property of child is allowed check if user have correct role
          $has_role=false;
          $role_patterns=array('/.*amir/','/president/','/.*general-secretary/');
          $roles = $current_user->getRoles();
          //allow sysadmin to change users
          if($current_user->hasRole('sys-admin')){
            return true;    
          }
          foreach ($role_patterns as $pattern) {
                                          
              array_map(
                function($role) use(&$has_role,$pattern){

                    $has_role=($has_role||preg_match($pattern, $role->getRoleId()));
                }, $roles);
              
          }
          if(! $has_role){
              return false;
          }

          //now check if given user holds office in a branch(A) that is a child of branch(B) where current user have office
          $office_srv = $this->serviceLocator->get('OfficeAssignmentService');
          $curr_usr_brnchs_wth_offs = $office_srv->getBranchesWithOffices($current_user,true);//get branches with office of current user, including child branches
          $allowed_branches = array_keys($curr_usr_brnchs_wth_offs);
          
          //if there is atleast one branch in common, allow access
          if(count(array_intersect($allowed_branches, $editable_branches))>0){
              return true;
          }else{
              return false;
          }                  
                  
    }


    /**
     * 
     */
     public function canPerform($user,$property='password',$action='reset'){
         $current_user = $this->getCurrentUser();
         $rules = $this->serviceLocator->get('ConfigService')->getConfig('user_rules');
         
         $allowed = $rules[$property][$action];
         /** TODO For now we allow only two types of relations between user and current_user for rules
          * own: user is performing action on their own property
          * child: user is performing action on property of a user that has office in a branch(A) that is child to a branch(B) 
          * where current user holds an office
          * only president/*_amir or *general_secretary can perform actions for this rule  
          **/

          //when action on property is allowed to users themselves, that is the case here 
          if($user->getId()==$current_user->getId() && in_array('own',$allowed)){
            return true;
          }

          //now, that user don't match current user we can only process child rules
          if(! in_array('child',$allowed)){
              return false;
          } 

          //when action on property of child is allowed check if user have correct role
          //and if given user holds office in a branch(A) that is a child of branch(B) where current user have office
          $office_srv = $this->serviceLocator->get('OfficeAssignmentService');

          $usr_brnchs_wth_offs = $office_srv->getBranchesWithOffices($user,true);//get branches with office of give user, excluding child branches
          $user_branches = array_keys($usr_brnchs_wth_offs);
          //if there is atleast one branch in common, allow access
          if($this->canEditOffice($user_branches)){
              return true;
          }                  


          //now, that user don't match current user and is not updateing child, check for same rules
          if(! in_array('same',$allowed)){
              return false;
          } 

          //when action on property of child is allowed check if user have correct role
          //and if given user holds office in a branch(A) that is a child of branch(B) where current user have office
          $office_srv = $this->serviceLocator->get('OfficeAssignmentService');

          $usr_brnchs_wth_offs = $office_srv->getBranchesWithOffices($user,false);//get branches with office of give user, excluding child branches
          $user_branches = array_keys($usr_brnchs_wth_offs);
          //if there is atleast one branch in common, allow access
          if($this->canEditOffice($user_branches)){
              return true;
          }                  
          
          //in all other cases do not allow action
          return false;
     }

	 public function addLogin($user,$method,$result,$loginAs=null){	 	
        
        $userLogin = $this->entityFactory->getUserLogin();
        $userLogin->setId(Uuid::uuid4()->getHex());        
        $userLogin->setLoginTime(new \DateTime());        
        $userLogin->setAuthMethod($method);
        $userLogin->setAuthResult($result);
        $userLogin->setUser($user);
        if(isset($loginAs)){
            $userLogin->setLoggedInAs($loginAs);
        }
        
	 	//only a user can "save" itself
        $this->entityManager->transactional(function($em) use(&$userLogin){
            $em->persist($userLogin);
        });
        $this->entityManager->flush();
        
        return $userLogin;
	 }

	 public function updateLogout($loginId){	 	

        $queryDQL = 'SELECT user_login FROM \Application\Entity\UserLogin  user_login WHERE  user_login.id = :id';
        
        /**
         * @var \Doctrine\ORM\Query;
         */
        $query = $this->entityManager->createQuery($queryDQL);
        $query->setParameter('id',$loginId);

        $logins = $query->getResult();
        if(is_array($logins) && sizeof($logins) == 1){
            $userLogin=$logins[0];
            $userLogin->setLogoutTime(new \DateTime());
            //only a user can "save" itself
            $this->entityManager->transactional(function($em) use(&$userLogin){
                $em->persist($userLogin);
            });
            $this->entityManager->flush();
            return $userLogin;
        }else{
            throw new \Exception("Invalid sessionn ".$loginId);
        }

    }

	 public function saveUser($user){
	 	
		$current_user = $this->getCurrentUser();
	 	//only a user can "save" itself
        if($user->getId()==$current_user->getId()){
            $this->entityManager->transactional(function($em) use(&$user){
               $em->persist($user);
            });
            $this->entityManager->flush();	       
        }
	 }
}
    
