Config Howto
-------------------

As per default config ZF2 will load module.config.php from config dir.
We have modified global config for autoload so that zf2 will consider
all files like *.config.php under autoload dir as config files and will autolad these.

This helps us breakdonw config in various parts and do that as we develop

Initially we have devided config as follows:

1. module.config.php : This file have over all config for module, you should add new config items to this file.
   If new items are too long (>15 lines) consider creatign a separate file under autoload dir for that config
 
2. routes.config.php: This file have config items for controllers and routes. 
   For any new contrller att an entry under `controllers -> invokables`. entry should use short name for controller adn value is fqn of controlelr class 
   Next you need to add route for the controller. Unless there is need to specify details fo route you can use `RoutesUtil::prepareRoute`
   to create a standard route def. this function needs two parameters, route name (it should be unique) and assumed to be short name of controller.
   second arg. is list of actions for the controller
 
3. bjyauthorize.config.php
