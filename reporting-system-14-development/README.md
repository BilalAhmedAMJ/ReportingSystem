AMJ Canada Reporting Application
================================

Introduction
------------
This application is implemented to replace existing reporting tool for 
AMJ Canada reporting tool.

Skelteton of applicaiton is based on https://github.com/zendframework/ZendSkeletonApplication

Technologies
-------------
This applicaiton uses following development stack

- LAMP (Apache >=2.2.x  MySQL >= 5.5.x PHP >= 5.3.x)
- Composer: application framework and dependencies are managed using composer, check composer.json for details.
- Zend Framework 2 (2.3.x), with following modules
	- Doctrine - zend-orm-module ( 0.8.* )
    - bjy-authorize (1.4.x) for ACL authorization control
    - zfc-user-doctrine-orm (0.1.x) for mapping users and ACL form DB
	- developer-tools : for development help / debugging
	- bjy-profiler : for profiling during development

- Bootstrap (3.x) for UI, a third party template ACE is used for UI (http://responsiweb.com/themes/preview/ace/1.3.1/)
- JQuery (as required by Bootstrap)

- PHP requirements:
	- PDO extentions
	- UTF-8 wrapper     
     

Installation
------------
1. Install/Setup LAMP server if you don't already have one. For windows I will suggest using [WAMP](http://www.wampserver.com/) or [AMPPS](http://www.ampps.com/). I personally use and like AMPPS mainly becuase it is easy to work with on OSX and is not "intrusive" you can unzip and drop in a folder of your liking.
2. Clone GIT repo from BitBucket https://amjc_repo@bitbucket.org/amjc_repo/reporting_system.git
3. Install dependencies using the shipped `composer.phar`:

    cd my/project/dir
    git clone https://amjc_repo@bitbucket.org/amjc_repo/reporting_system.git
    cd reporting_system
    php composer.phar self-update
    php composer.phar install


	(The `self-update` directive is to ensure you have an up-to-date `composer.phar` available.)
	
Database Setup
------------
After soruce code and third party packages are installed we need to setup database. 
We need MySQL version 5.5+, if you don't already have mySQL server installed, download and install server.

1. Create a database and a user with all privileges on that db.
2. Copy doctrine.local.default to doctrine.local.php in config folder under application root.
3. update host,port,user,password,dbname parameters as needed.       
4. Due to bjyauthrozie requiring role table we need to setup users, role, and user_role_linker tables outside of doctrine migrations. Scripts to setup these tables can be found in data/DoctrineORMModule/Migrations/Version20141104212952.php under applicaiton root. These scripts are added as comments in this file. 
5. Run migrations for rest of database setup. go to command prompt and move to applicaiton root then run 
	 ./vendor/bin/doctrine-module migrations:migrate


Web Server Setup
----------------


### PHP CLI Server

The simplest way to get started if you are using PHP 5.4 or above is to start the internal PHP cli-server in the root directory:

    php -S 0.0.0.0:8080 -t public/ public/index.php

This will start the cli-server on port 8080, and bind it to all network
interfaces.

**Note: ** The built-in CLI server is *for development only*.

### Apache Setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:
Make sure mod_rewrite is enabeld


    <VirtualHost *:80>
        ServerName amjc_reports.localhost
        DocumentRoot /path/to/amjc_reports/public
        SetEnv APPLICATION_ENV "development"
        <Directory /path/to/amjc_reports/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
            <IfVersion >=  2.4.3 >
                # this is needed only in versions greater or equal 2.4.3
                Require all granted
            </IfVersion>            
        </Directory>
    </VirtualHost>



### PHP Extensions
Following PHP extensions are required
* php5-bz2 
* php5-curl 
* php5-ctype 
* php5-gd
* php5-intl 
* php5-mbstring  
* php5-mcrypt 
* php-pear 
* php-zip
* sha3 (https://github.com/strawbrary/php-sha3)

### File systems Installation requirments

Following directories need to be writeable by web-server user

		data/cache
		data/DoctrineORMModule
		data/uploads
		data/uploads/attachment	
		data/uploads/document	
		data/uploads/export


### Additional config
Any installation specific configs / config overrides can be added to following file

`module/Application/config/autoload/zzz.local.config.php`

e.g. dev-rep server have following config overrides

		<?php
		return array(
		
		'view_manager' => array(
		        'display_not_found_reason' => true, /*FIXPROD remove for production release*/
		        'display_exceptions'       => true, /*FIXPROD remove for production release*/
		),
		
		
		    //Application specific config items
		    'application' => array(
		            'email'=>array(
		                'transport'=>array(
		                    'type' => 'sendmail',
				 )
			      )
		     )
		
		
		);

 