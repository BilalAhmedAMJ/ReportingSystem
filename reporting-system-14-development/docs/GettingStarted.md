#Getting Started#

---
##Background

---
ZF2 is a MVC framework, where MVC stands for Model-View-Controller. Hence each ***page*** will have three parts.
 -  controller, that governs application flow and delegates business logic to appropriate business classes
 -  model, is meant to represent business logic e.g. business rules and database persistence etc.
 -  view, is display only code, efforts are usually made to keep it simple html or view helpers as much as possible with minimal "programming"
 
In a ZF2 application, as in other implementations of MVC, controllers and view are very well defined, however model is often an ambigous piece. In our case model will consist of two parts:
 - Services: each use case, e.g. manage users, will be modeled to Service class that will interact with database and data business objects. 
 - Entitites, since we are using DoctrineORM we will be representing DB in applicaiton via Doctrine entities, each entity will be responsible to manage itself, e.g a user object will have methods to detmine if username is unique at the time of creating a user, or if an email address is valid etc.

---
##Adding a new page


To add a new page to the application you will need to do following:

1. Add a class under `module/src/Application/`, name of class should be appended with string `Controller`, e.g. `DocumentController`. Class should inherit from `Zend\Mvc\Controller\AbstractActionController` a sample class is given below.

2. Add an action to your controller, an actions handle multiple tasks on same page, e.g. we may have a create, edit, delete actions for a user page to add, modify and remove a user. An action in a controller should be a public function of controller class that have a name ending in "Action" e.g. indexAction in above class. When defining route, below, if no action is specified it is considered to be "index" action.

3. Create a view template. View templates is a php file which resembles HTML and have very little code in it. Views are configured to be placed under `module/Applicaiton/view/application` for our app. under this folder create a dir named after controller, e.g. `document` in our example above. inside dir create a file called actionName.phtml or `index.phtml` in our example above.

4. To pass data/variables from controller to view tempalte, you will create a `ViewMoel` as in example above. Constructore of ViewModel takes a hash array as argument, this array can be accessed in view templates as variables of `$this` object e.g. to dispaly `action` variabel in example above on index page we will add `<php? echo $this->caller ?>` in our view tempalte or `document/index.phtml` file

5. Once a controller is created next step is to add this to ZF2 config for that update `module/Application/config/autoload/routes.config.php` in this file we need to update two items
   - under  `controllers=>invokables` array add a map of controller shortname and controller class full path, e.g. `'document'    => 'Application\Controller\DocumentController',`
   - under `router` array add route details, if you don't want to override route details you can use a short form `'document' => RoutesUtil::prepareRoute('document', array('index','list')),` that will add simple route, format is shortname of controller form above step is key, and call to prepareRoute takes two parameters, 1)controller short name from step above, and list of actions

6. Adding security, so far we have a route and page is setup but going to URL will generate a 403 security error. 
Reason is our authorize plugin denies access to all unless specified. For this to work, add a security setting for your controller under 
` module/Application/config/autoload/bjyauthorize.config.php` in this file there is a array `'guards' => array(  'BjyAuthorize\Guard\Controller' => array(` under their add array with access rules e.g. `array('controller' => 'document','action'=>'stuff', 'roles' => array('sys-admin')),` will allow access on controller document action stuff to all users with role sys-admin


**Sample Controller Class**

    namespace Application\Controller;
    
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;
    use Zend\Crypt\Password\Bcrypt as Bcrypt;
    class DocumentController extends AbstractActionController
        public function indexAction()
        {
            $view =  new ViewModel(array('caller'=>'document index' ));
		    return $view;
        }
    }


**Sample view template**

    <h2>
        Documents Index    
    </h2>
    <?php echo $this->caller ?>
