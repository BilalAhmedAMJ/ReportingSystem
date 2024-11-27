<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Crypt\Password\Bcrypt as Bcrypt;
use ZfcUser\Controller\UserController;
use Zend\Session\Container as SessionContainer;
use Application\Service\AWSCognito\CakeDCProvider\Cognito;



class AuthController extends AbstractActionController
{
    const ROUTE_LOGIN='auth/login';

    private $failedLoginMessage='Authentication failed. Please try again.';

    public function testAction(){
        
    }


    public function legacyAction(){

        $vm = new ViewModel(array('target'=>$this->getRequest()->getQuery('target')));   
        
        $this->layout('layout/login');

        return $vm;
    }

    public function loginAction(){

        $vm = new ViewModel(array('target'=>$this->getRequest()->getQuery('target')));   
        
        $this->layout('layout/login');

        return $vm;
    }
    
        
    public function switchUserAction(){
        
        
        if(!$this->getRequest()->isPost()){
            $vm =  new ViewModel();
            $vm->setTemplate('application/auth/switch-user');
            return $vm;
        }
        
        $usr_srv = $this->getServiceLocator()->get('UserProfileService'); 
        $current_user = $usr_srv->getCurrentUser();
        $request = $this->getRequest();
        
        
        
        //TODO FIXME Test switch user        
        //$request->getPost()->set('su_to_userid','2200');
        //$request->getPost()->set('userpassword','test123');
                
        
        //check if user have authority to do an su
        // we allow sys-admin to do su
        if($current_user->hasRole('sys-admin')){
            
            $adapter = $this->zfcUserAuthentication()->getAuthAdapter();

            //$this->clearUserFromSession();
            $adapter->resetAdapters();

            $request->getPost()->set('switch_user_request',true);
            $request->getPost()->set('su_to_userid',$request->getPost('su_to_userid'));
            
            $request->getPost()->set('currentusername',$current_user->getUsername());
            $request->getPost()->set('credential',$request->getPost('current_userpassword')); 
                        
            $result = $adapter->prepareForAuthentication($request);
            // Return early if an adapter returned a response
            if ($result instanceof Response) {
                return $result;
            }
            
            $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);

            $username = $request->getPost('su_to_userid');
    
            //incase of error go back to login page and show error message
            if ( (!$auth->isValid()) ) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Unable to switch to '.'['.$username.']');
                $adapter->resetAdapters();
                
                return $this->redirect()->toRoute(static::ROUTE_LOGIN,array());
                
            }else {
    
                $user = $this->serviceLocator->get('zfcuser_auth_service')->getIdentity();
                
                // valid user found load user roles  
                $office_srv = $this->serviceLocator->get('OfficeAssignmentService');
                $roles = $office_srv->getOfficeRoles($user);
                $user->addDynamicRoles($roles);
                //in case suer don't ahve any office assignments
                //redirect to user-profile page 
                if(empty($roles) ){
                    return $this->redirect()->toRoute('user-profile');
                }
            }
            //go to default login redirect
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }
        
        //invalid access redirect to login
        //TODO we need to logout user in this case
        return $this->redirect()->toRoute(static::ROUTE_LOGIN,array());
    }
    
   
    private function verifyCaptcha(){
        if( getenv('APPLICATION_ENV') != 'production' ) {
            return true;
        }
        $recaptcha_response=$this->getRequest()->getPost('g-recaptcha-response');
        $key_f=dirname(getenv('bin_data')).'/recaptch_secret';
        $secret=trim(file_get_contents($key_f));
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        $resp = $recaptcha->verify($recaptcha_response);
        if ($resp->isSuccess()) {
            // Verified!
                return true;
        } else {
            // Store error message in 
            $errors = $resp->getErrorCodes();
            error_log('CAPTCHA verification error '. print_r($errors,true) );
                $this->flashMessenger()->setNamespace('error')->addMessage('Unable to confirm that you are not a robot. Please check "I\'m not a robot" check box and try again');
            return false;
        }

    }

    private function redirectAfterLogin(){
        $redirect_after=$this->getRequest()->getPost('target');
        if(!$redirect_after || empty($redirect_after)){
            $redirect_after=$this->getOptions()->getLoginRedirectRoute();
            $router = $this->getEvent()->getRouter();
            //$routeMatch=$router->match($redirect_after);
            $redirect_after=$router->assemble(array(),array('name'=>$redirect_after));
        }
    }

    public function authenticateAction(){


        $redirect_after=$this->redirectAfterLogin();

        if($this->verifyCaptcha() !== true  && $this->verifyIntegrationApp() !== true ){
            error_log('CAPTCHA Error Not INTEGRATION call, back to login!');
            return $this->redirect()->toRoute(static::ROUTE_LOGIN,
                array('target'=>$redirect_after)
            );
        }

        
        if(!$redirect_after || empty($redirect_after)){
            $redirect_after=$this->getOptions()->getLoginRedirectRoute();
            $router = $this->getEvent()->getRouter();
            //$routeMatch=$router->match($redirect_after);
            $redirect_after=$router->assemble(array(),array('name'=>$redirect_after));
        }

        if ($this->zfcUserAuthentication()->hasIdentity()) {
            error_log('User Already logged in');
            return $this->redirect()->toUrl($redirect_after);
        }
        
        
        $request = $this->getRequest();
        
        $username=strtolower(''.$request->getPost('username'));
        $usr_srv = $this->getServiceLocator()->get('UserProfileService');
        if(strpos($username, '@')){ //assume email if @ is instring after first character
            $email=$username;
            $users = $usr_srv->getUsersByEmail($email,true);
            if($users && count($users)==1){
                $username=$users[0]->getUsername();
            }elseif($users && count($users)>1){
                $this->flashMessenger()->setNamespace('default')->addMessage('There are multiple userIds linked to given email address. You need to select your userId and provide password for selected userId');
	            // $this->flashMessenger()->setNamespace('error')->addMessage('To login via eamil exactly one user should have email set to '.'['.$username.'], we have '.count($users));	            
	            // return $this->redirect()->toRoute(static::ROUTE_LOGIN,
	                // array('target'=>$redirect_after)
	            // );
	            $vm = new ViewModel(array('post'=>false,'email'=>$email,'users'=>$users));
                $this->layout('layout/login');
                $vm->setTemplate('auth/login');
                return $vm;
            }
        }
		
		//Check user status only allow login if user status is "active" otherwise reject login attempt
		$user = $usr_srv->getUserByUsername($username);
        //find migrated user if can't find user by username
        if(!$user){
           $user = $usr_srv->getUserByMigratedUsername($username);
            if($user){
                //Flash message
                $this->flashMessenger()->setNamespace('error')->addMessage('You just logged in using old username '.'['.$username.']');
                $this->flashMessenger()->setNamespace('error')->addMessage('Please note your new username is "'.$user->getUsername().'"');
                $this->flashMessenger()->setNamespace('error')->addMessage('Old userIds will soon be disabled!');
                                                                            
                $username = $user->getUsername();
            }
        }
        
        
        if(($user) && ($user->getStatus()!='active')){
                $this->flashMessenger()->setNamespace('error')->addMessage($this->failedLoginMessage.'['.$username.']');            
                error_log('User not active: ' .$user->getStatus());
                return $this->redirect()->toRoute(static::ROUTE_LOGIN,
                    array('target'=>$redirect_after)
                );
        }
        
				
        $request->getPost()->set('identity',$username);
        $request->getPost()->set('credential',$request->getPost('userpassword'));
        
        $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        
        //hookup user validation to event chain
        //$this->isValidUser($adapter);
        
        $result = $adapter->prepareForAuthentication($request);
        // Return early if an adapter returned a response
        if ($result instanceof Response) {
            return $result;
        }
        
        $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);

        //incase of error go back to login page and show error message
        if ( (!$auth->isValid()) ) {
            error_log('Invalid Password');
            $this->flashMessenger()->setNamespace('error')->addMessage($this->failedLoginMessage.'['.$username.']');
            $adapter->resetAdapters();
            return $this->redirect()->toRoute(static::ROUTE_LOGIN,
                array('target'=>$redirect_after)
            );
            
        }else {

            $user = $this->serviceLocator->get('zfcuser_auth_service')->getIdentity();
            
            //check for status of user and password expiration
            $resp = $this->checkUserStatus($user);
            if($resp){
                return $resp;
            }
            // valid user found load user roles  
            $office_srv = $this->serviceLocator->get('OfficeAssignmentService');
            $roles = $office_srv->getOfficeRoles($user);
            $user->addDynamicRoles($roles);
            $usr_srv->addLoginHistory($user,"legacy","success");
            //in case suer don't ahve any office assignments
            //redirect to user-profile page 
            if(empty($roles) ){
                return $this->redirect()->toRoute('user-profile');
            }
            if(!$user->hasRole('sys-admin') && !$user->hasRole('admin')){
                $this->flashMessenger()->setNamespace('error')->addMessage('You tried to login using legacy username/password. Use AMJ Inc Portal for login!');
                $resp =$this->loginAction();
                $resp->setVariable('user', $user);
                $this->clearUserFromSession();
                error_log('Non-Admin user tried to login via legacy interface:'.$username);
                return $resp;                
            }
	}

        
        //if we were redirected to this page from any page wiht a direct parameter go to that URL 
        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect_after) {
            return $this->redirect()->toUrl($redirect_after);
        }
    
        //go to default login redirect
        return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        
    }

    private function checkUserStatus($user){
        /**
         * @var \Application\Entity\User         
         */
         $user =$user;
         
         $now = new \DateTime();
         $expiry = $user->getPasswordExpiryDate();
/*
         if($expiry < $now){
            $this->flashMessenger()->setNamespace('error')->addMessage('Password for user '.$user->getDisplayName().' is expired!');
            $this->flashMessenger()->setNamespace('error')->addMessage('Password change is required!');
            $resp =$this->changePasswordAction();
            $resp->setVariable('user', $user);
            $this->clearUserFromSession();
            error_log('AUTH Force password change due to expiry');
            return $resp;
         }
*/
    }


    public function resetPasswordAction(){
        
        $token_uuid=$this->params()->fromQuery('id');
        $is_post = $this->getRequest()->isPost();
        $viewModel =  new ViewModel(array('data'=>$this->params()->fromPost()));
        
        if(!$token_uuid && ! $is_post){
            //empty User, forward to reset-password form
            //do nothing
        }elseif($is_post){
           //Form submitted for resetting user password check values if valid send reset request
           $form=$this->getServiceLocator()->get('CreateFormService')->getform('reset_password');
           $form->setData($this->params()->fromPost());
           $data = $this->params()->fromPost();
           $usr_srv = $this->getServiceLocator()->get('UserProfileService');
           $user = $usr_srv->getUserByUsername($data['username']);
           //if unable to find user try to find user based on migrated userids
           if(!$user){
               //fetch user based on migrated usernames
               $user = $usr_srv->getUserByMigratedUsername($data['username']);
               if(!user){
                   $data['username']=$user->getUsername();
               }
           }
           $errors = $this->validateResetPassword($user,$data);
           if(! empty($errors)){
               $this->flashMessenger()->setNamespace('error')->addMessage('Unable to reset passowrd. There was no user that matched given information.');
           }else{
               //send password reset email
               $email = $user->getEmail();
                
               $token = $usr_srv->createUserUpdateToken($user,'reset_password');
        
               $vars=array('user'=>$user,'token'=>$token);
               
               $email_srv = $this->getServiceLocator()->get('EmailService');
               $subject='AMJ Reports - Password Reset Request';
               $email_srv->sendTemplatedEmail($subject,$email,'reset_password',$vars);
                              
               $this->flashMessenger()->setNamespace('success')->addMessage('An email has been sent to provided email address with further instructions. Please check your email.');
               $viewModel->setVariable('data', array());
           }
           //check user info if valid send email 
           //otherwise return errors to user 
        }elseif($token_uuid){
            //get token details 
           $token = $this->getServiceLocator()->get('userProfileService')->getUserTokenByUUID($token_uuid);                
           
           if(!$token || $token->isExpired()){
               //invalid token do nothing ask user to retry resetting password
                $this->flashMessenger()->setNamespace('error')->addMessage('This link has aleady been used or expired. You need to submit an other request to reset password.');                          
           }else{
              //take user to change password screen
              $viewModel->setVariable('user_token', $token_uuid);
              $viewModel->setVariable('user', $token->getUser());
              $viewModel->setTemplate('application/auth/change-password');              
           }
        }else{
            //this is an error
             $this->flashMessenger()->setNamespace('error')->addMessage('Invalid action. All fields are required.');
        }

        $this->layout('layout/login');

        return $viewModel;
    }



    private function validateResetPassword($user,$data){
        $errors=array();

        if($user){
            if($user->getMemberCode()!=$data['member_code']){
                $errors[]=sprintf('Membercode [%s] does not match',$data['member_code']);                
            }
            if($user->getEmail()!= strtolower($data['email']) ){
                $errors[]=sprintf('Email [%s] does not match',$data['member_code']);                
            }            
        }else{
            $errors[]=sprintf('Unable to retrieve user for [%s]',$data['username']);
        }
        return $errors;
    }
    
    public function changePasswordAction(){
        //user can only access this page via post
        if(! $this->getRequest()->isPost()){
            $this->flashMessenger()->setNamespace('error')->addMessage('Invalud Access! Change Password from user-profile page.');
            return new ViewModel();
        }
        
        //loaduser 
        $user = $this->getServiceLocator()->get('UserProfileService')->getUserByUsername($this->params()->fromPost('username'));
        
        $srv = $this->getServiceLocator()->get('UserProfileService');
        if($this->params()->fromPost('user_action')=='change'){
            //update Password
            $updated = $srv->updatePassword($user,$this->params()->fromPost(),true); 
            if(! $updated){
                $this->flashMessenger()->setNamespace('error')->addMessage("Unable to change password, values don't match!");    
            }else{
                //redirect to default login page
                $this->flashMessenger()->setNamespace('info')->addMessage("Password is updated successfully! Please login to continue.");
                $this->redirect()->toRoute($this->getOptions()->getLogoutRedirectRoute());                                
            }
        }
        
        $vm = new ViewModel(array('user'=>$user));
        if($this->params()->fromPost('user_token')){
            $user_token = $srv->getUserTokenByUUID($this->params()->fromPost('user_token'));
            if($user_token){
                $vm->setVariable('user_token', $user_token->getToken());
            }
        }
        $vm->setTemplate('application/auth/change-password');
        $this->layout('layout/login');
        return $vm;
        
    }
    
    
    private function clearUserFromSession(){
        
        $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
        $this->zfcUserAuthentication()->getAuthAdapter()->logoutAdapters();
        $this->zfcUserAuthentication()->getAuthService()->clearIdentity();
        
    }
    
    /**
     * Logout and clear the identity
     */
    public function logoutAction()
    {
        $this->clearUserFromSession();
        $redirect = $this->params()->fromPost('redirect', $this->params()->fromQuery('redirect', false));

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        return $this->redirect()->toRoute($this->getOptions()->getLogoutRedirectRoute());
    }
    
    
    private $options;
    /**
     * get options
     *
     * @return UserControllerOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions($this->getServiceLocator()->get('zfcuser_module_options'));
        }
        return $this->options;
    }
    /**
     * set options
     *
     * @param UserControllerOptionsInterface $options
     * @return UserController
     */
    public function setOptions( $options)
    {
        $this->options = $options;
        return $this;
    }


    public function cognitoAction(){

        $url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        error_log("Cognitor redirect URI  ".$url);
        
        $configSrv = $this->getServiceLocator()->get('ConfigService');
        $conf = $configSrv->getConfigValues('cognito');
        $encUtil = $this->getServiceLocator()->get('EncryptUtil')->createDefaultAdapter();
        $conf['clientId']=$encUtil->decrypt($conf['clientId']);
        $conf['clientSecret']=$encUtil->decrypt($conf['clientSecret']);
        $provider = new Cognito($conf);

        
        //
        //printf("<pre>URL \n %s </pre>",$url);
        
        if (!isset($_GET['code'])) {
        
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            $_SESSION['oauth2nonce'] = $provider->getNonce();
            header('Location: '.$authUrl);
            exit;
        
        // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            error_log('Portal Login CSRF state invalid'); 
            $this->flashMessenger()->setNamespace('error')->addMessage("Invalid portal login state, try again!");
            unset($_SESSION['oauth2state']);
            return $this->redirect()->toRoute(static::ROUTE_LOGIN,array());
        
        } else {
        
            // Try to get an access token (using the authorization code grant)
            //error_log('Got code auth now trying to get token');
            try{
                $token = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);
                $this->getRequest()->getPost()->set('token',$token);
                $this->getRequest()->getPost()->set('provider',$provider);
                $adapter = $this->zfcUserAuthentication()->getAuthAdapter();
        
                //hookup user validation to event chain
                //$this->isValidUser($adapter);
                $request = $this->getRequest();
                $result = $adapter->prepareForAuthentication($request);
                // Return early if an adapter returned a response
                if ($result instanceof Response) {
                    error_log('COGNITO a response from auth adapter '.$result);
                    return $result;
                }
                
                $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
        
                //incase of error go back to login page and show error message
                if ( (!$auth->isValid()) ) {
                    error_log("COGNITO ****AUTH WAS INVALID!!" . var_export($auth,true));
                    $this->flashMessenger()->setNamespace('error')->addMessage($this->failedLoginMessage.'['.$username.']');
                    $adapter->resetAdapters();
                    return $this->redirect()->toRoute(static::ROUTE_LOGIN,
                        array('target'=>$redirect_after)
                    );
                    
                }else {
                    $user = $this->serviceLocator->get('zfcuser_auth_service')->getIdentity();
                    
                    //check for status of user and password expiration
                    $resp = $this->checkUserStatus($user);
                    if($resp){
                        error_log('COGNITO User Status '.$resp);
                        return $resp;
                    }
                    // valid user found load user roles  
                    $office_srv = $this->serviceLocator->get('OfficeAssignmentService');
                    $roles = $office_srv->getOfficeRoles($user);
                    $user->addDynamicRoles($roles);

                    $usr_srv = $this->getServiceLocator()->get('UserProfileService');
                    $usr_srv->addLoginHistory($user,"cognito","success");

                    //in case user don't have any office assignments
                    //redirect to user-profile page 
                    if(empty($roles) ){
                        return $this->redirect()->toRoute('user-profile');
                    }
                }
                $redirect_after = $this->redirectAfterLogin();
                //if we were redirected to this page from any page wiht a direct parameter go to that URL 
                if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect_after) {
                    return $this->redirect()->toUrl($redirect_after);
                }
            
                //go to default login redirect
                return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());

            }catch(Exception $e){
                error_log(print_r($e, true));
                $this->flashMessenger()->setNamespace('error')->addMessage("Invalid portal login response, try again!");
                unset($_SESSION['oauth2state']);
                return $this->redirect()->toRoute(static::ROUTE_LOGIN,array());
            }

            return $this->redirect()->toRoute(static::ROUTE_LOGIN,array());
        }
    }
       
    private function verifyIntegrationApp(){
        $configSrv = $this->getServiceLocator()->get('ConfigService');
        $conf = $configSrv->getConfigValues('ami_integration');
        $encUtil = $this->getServiceLocator()->get('EncryptUtil')->createDefaultAdapter();
        $conf['clientId']=$encUtil->decrypt($conf[clientId]);
        $conf['clientKey']=$encUtil->decrypt($conf[clientKey]);
        if($this->params()->fromPost('ami_integration')=='true'){
	    error_log('INTEGRATION CALL');
        //error_log('ID  '.$this->params()->fromPost('clientId').' <==> '.$conf['clientId']);
        //error_log('KEY '.$this->params()->fromPost('clientKey').' <==> '.$conf['clientKey']);

            return ($conf['clientId'] == $this->params()->fromPost('clientId'))
            &&
            ($conf['clientKey'] == $this->params()->fromPost('clientKey'))
            ;
        }

        return false;
    }
}
