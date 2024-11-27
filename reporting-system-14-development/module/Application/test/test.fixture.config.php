<?php


return array(
  'doctrine' => array(
    'connection' => array(
      'orm_default' => array(
        'wrapperClass'=>null,//Use default wrapper Doctrine\DBAL\Connection',      
        'driverClass' =>'Doctrine\DBAL\Driver\PDOSqlite\Driver',
            'params' => array(
                'path'=>TESTDB_FILE,
              )
         )
      )
   )
);


