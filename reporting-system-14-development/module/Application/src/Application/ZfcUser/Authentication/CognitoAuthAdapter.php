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


class CognitoAuthAdapter extends AbstractAdapter implements ServiceManagerAwareInterface{


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
        error_log('IN CognitoAuthAdapter');
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
        $request = $e->getRequest();
        $is_cognito_request = strpos($request->getUri()->getPath(),'login/cognito');
        

        if($is_cognito_request === false){
            $this->setSatisfied(false);
            
            return false;            
        }

        $userObject = null;
        try {        
            $provider = $request->getPost('provider');
            $token = $request->getPost('token');
            // We got an access token, let's now get the user's details
            $user = $provider->getResourceOwner($token);
    
            $userObject = $this->userProfileService->getUserByMembercode($user->getMembercode());

            error_log('Find By MemberCode '.$user->getMembercode().'  => '.$userObject->getId());
            // regen the id
            $session = new SessionContainer($this->getStorage()->getNameSpace());
            $session->getManager()->regenerateId();

            // Success!
            $e->setIdentity($userObject->getId());
            $this->setSatisfied(true);
            $storage = $this->getStorage()->read();
            $storage['identity'] = $e->getIdentity();
            $storage['token'] = $token;
            $this->getStorage()->write($storage);
            $e->setCode(AuthenticationResult::SUCCESS)
            ->setMessages(array('Authentication successful.'));
    
        } catch (Exception $e) {
            error_log('Exeption when getting user and validating user : '.$e->getMessage());
            $e->setCode(AuthenticationResult::FAILURE_CREDENTIAL_INVALID)
              ->setMessages(array('Unable to validate credential via Portal Service.'));
            $this->setSatisfied(false);
                
            return false;            
        }

        //error_log("got a valid user [".$userObject->getId()."]");
        //need a valid user credential for user who can do switch user 

        //error_log("Current ".$credential);
        
        //no need to continue if user we want to does not exists
        if ( !$userObject ) {
             $e->setCode(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND)
               ->setMessages(array('Cognito User: A record with the supplied identity could not be found.'));
               
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

        $this->setSatisfied(true);
        return true;
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
