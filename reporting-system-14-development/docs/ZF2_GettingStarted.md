Zend Framework 2 (zf2) Basics for this App
===========================================

Application dir structure
--------------------------

Generally speaking zf2 application have following dir structure
```
App_Root
  |-config
  |	|- autoload
  |	+- application.config.php 
  |
  |-data
  |	|- cache
  |	+- sql**
  |
  |-module  : zf2 apps are modular in nature, Application module being default app
  |
  |-public : This dir contains assets and is also the DOCUMENT_ROOT for web-server/apache 
  |
  |-vendor
  |  |_ All third party libraries and zf2 itself lives under vendor dir 
  |
  |-init_autoloader.php 
  |		: Application level sttings, when adding new module local or third party we update this file
  |
  |-composer.json : Composor dependency setup config file
  |-composer.lock : Current status of composor dependencies
  +-composer.phar : Composer library

```
** Not part of skeleton application we added this dir

### Structure under module dir

Structure of *src* dir is not determined by Zend and other dirs can also be configured to different locations and names if needed.
There is a convntion for *view* dir structure, when displaying views for a controller zf2 will search for a dir under view dir with matching controller names without "Controller" postfix in lower case.
If such a dir exists, zf2 will try to find a file named xxx.phtml where xxx is name of action method in controller without "Action" postfix.

```
  + module
      +- Application
   		|- config		
   		|	: modules config mainly in <b>module.config.php</b>
   		|- language	
   		|	: Internationalization files
   		|- src		
   		|   +- Application
   		|   	|- Controller : zf2 controller classes
   		|   	|- Form		  : zf2 Form classes
   		|   	|- Entity	  : doctrine mapped entities
   		|   	|- Service	  : general service classes	
   		|   	|- Util 	  : generic utilities
        |
   		|- view	 : Application view pages, should not contain business logic
   		|	+- application : Views for controlers under <i>src/Application/Controller</i> 
   		|	|	|- index
   		|	|		+ index.phtml : <i>view file for indexAction under IndexControler</i>** 
   	    |	|- error : Error redirect pages e.g. 404.phtm, 500.phtml etc.
   		|	|- layout: Overall site layout templates, a view returned from a controller is inserted into layout
   		|		+ layout.phtml : default layout
   		|		+ layout_custom.phtml : we can specify in controller action if a specific layout is needed
        |
  		+- Module.php	: Module hooks, mainly look into onBootstrap and getConfig


```

URL Mapping
-----------



