#Doctrine ORM#
---

Doctrine is a PHP framework from the ORM (Object Relation Mapping) frameworks. 
ORM helps devs persist business domain object to DB and retrieve as needed. It maps a DB table (releation) to a business model (Object), often called and entity.
Following are some basic operations you will be doing tht will require interaction with Doctrine apis.

##Add Entity##
When you need to persist a new type/class of objects to DB you will create an entity. e.g we have following entities, User, Department, Branch.

When creating a new entity you will create a class in `Application/Entity` name space under module/Application/src. 
There are many ways to map entity and access entitiy proerpties. We are suing following conventions.

- Unique identifier is named "id" and defined as auto_incremented integer
- All properties / fields are defined as protected
- Property names are lower case and words are separated by a _ (underscore)
- Mapping is done using Annotations
- Access to properties is via getter/setters
- We use `Zend\Form\Annotation\Validator` annotation to define entity property validation rules. Unless none of the property is required, which is very unlikely, you need to define validation rules.

To ease in entity defination we will be using doctrine command line tool to generate getters/setters for our entities. You can use update_entities.php script under module/Application/scripts for this.

Once an entity is created, we need to update database to match DB schema with object model. We are using Doctrine migrations to manage DB migrations, 
this approach will make sure we will always be able to track DB changes and generate DB from scratch.
There are two steps to DB migrations, 

1- generation of migration script: to generate migration script you will execute following command from root dir of the applicaiton. 
	` ./vendor/bin/doctrine-module migrations:generate` This will create a file with name Version_DATE_TIME_STAMP where _DATA_TIME_STAMP will be current time stamp. In this file you will find two methods, `up` and `down`. `up` will update data base to new version and `down` will downgrade to last version.
2- execution of migrations or migrating/updating DB :  go to command prompt and move to applicaiton root then run 
	 `./vendor/bin/doctrine-module migrations:migrate`

NOTE: There seems to be a bug in doctrine mysql driver. This cuases doctrine not to recognize existing columns that are mapped as "enum". 
So it is imparitive that you review generated script, and remove any items that you don't intentd to update.


##Manipulating Entitites##
We will use following conventions / rules while manipulating entitites.

- Instansiation of entity object can be done using CreateEntityFactory abstract factory. All you need to do is obtain entity by class name, preceded by 'entity.', from Service manager. e.g. to create a User object we will call `$serviceLocator->get('entity.User')` 
- None of the entity operations will be performed from view tempaltes or controllers. All operations will be isolated to relavent "Service" objects, following [Service Facade](http://en.wikipedia.org/wiki/Facade_pattern) pattern.
- All `Service` methods that return search results will return array form of the objects. For this you should use `getArrayResult` method of a query object or specify `HYDRATE_ARRAY` flag when getting resultset 
- There is a service configured to populate an object from an array, this is called `ArrayObjHydrator`.


## TIPS / TRICKS ##
Add SQL logger 
$this->entityManager->getConnection()->getConfiguration()->setSQLLogger( new \Doctrine\DBAL\Logging\EchoSQLLogger());

http://stackoverflow.com/questions/4570608/how-to-debug-mysql-doctrine2-queries


