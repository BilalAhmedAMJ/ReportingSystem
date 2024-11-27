<?php

namespace Application\ZfcUser\Authentication;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceManager;

use Zend\Authentication\Result as AuthenticationResult;
use ZfcUser\Authentication\Adapter\AbstractAdapter;
use Zend\Session\Container as SessionContainer;
use ZfcUser\Authentication\Adapter\AdapterChainEvent as AuthEvent;


class SwitchUserAuthAdapter extends AbstractAdapter implements ServiceManagerAwareInterface{


    public function __construct($sm){
          
        $this->serviceManager = $sm;  

        $this->userProfileService = $sm->get('UserProfileService');
            
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceManager){
               
        $this->serviceManager = $serviceManager;  

        $this->userProfileService = $serviceManager->get('UserProfileService');
        
        return $this;
    }
    
    private $userProfileService;
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;

    public function authenticate(AuthEvent $e)
    {
        error_log('IN SwitchUserAuthAdapter');        
        //to make sure we respect chain,
        //assume some other adapter in chain 
        // have provided authentication
        if ($this->isSatisfied()) {
            $storage = $this->getStorage()->read();
            $e->setIdentity($storage['identity'])
              ->setCode(AuthenticationResult::SUCCESS)
              ->setMessages(array('Authentication successful.'));
              
            return;
        }

        $switch_user_request   = $e->getRequest()->getPost()->get('switch_user_request');
        
        if(!$switch_user_request){
            $this->setSatisfied(false);
            
            return false;            
        }


        $identity   = $e->getRequest()->getPost()->get('su_to_userid');
        $userObject = null;

        $userObject = $this->userProfileService->getUserById($identity);

        error_log("Userid to switch ".$identity);
        error_log("Userid to switch ".$userObject->getId());
        //need a valid user credential for user who can do switch user 
        $identity_current = $e->getRequest()->getPost()->get('currentusername');
        $credential = $e->getRequest()->getPost()->get('credential');
        $userObject_current = $this->userProfileService->getUserByUsername($identity_current);
        
        //error_log("Current ".$credential);
        
        //no need to continue if user we want to does not exists
        if ( !$userObject ||  !$userObject_current) {
             $e->setCode(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND)
               ->setMessages(array('SU: A record with the supplied identity could not be found.'));
                              
            $this->setSatisfied(false);
            return false;
        }

        $bcrypt = new \Zend\Crypt\Password\Bcrypt();
        $zfc_user=$this->serviceManager->get('zfcuser_user_service');
        $bcrypt->setCost( $zfc_user->getOptions()->getPasswordCost());
        if (!$bcrypt->verify($credential, $userObject_current->getPassword())) {
            // Password does not match
            $e->setCode(AuthenticationResult::FAILURE_CREDENTIAL_INVALID)
              ->setMessages(array('Supplied credential is invalid for current user.'));
 
            error_log("Password match failed for ".$userObject->getId());

            $this->setSatisfied(false);
            return false;
        }

        // regen the id
        $session = new SessionContainer($this->getStorage()->getNameSpace());
        $session->getManager()->regenerateId();

        // Success!
        $e->setIdentity($userObject->getId());
        $this->setSatisfied(true);
        $storage = $this->getStorage()->read();
        $storage['identity'] = $e->getIdentity();
        $this->getStorage()->write($storage);
        $e->setCode(AuthenticationResult::SUCCESS)
          ->setMessages(array('Authentication successful.'));
    }


    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
    
}
