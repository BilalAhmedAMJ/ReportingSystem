#!/bin/sh 

#
# Installation script
#
# This script will downlaod all dependencies using composer
# and will setup dir structure required to install application
# make sure conf/autoload/doctrine.local.php is created with db info
# 
# This script assuems that web server processes is executed under
# www-data group and will set permissions for that user
#


#update composer
php ./composer.phar "selfupdate"

#install dependencies
php ./composer.phar "install"

#create dir that are needed by app
if [ ! -e data/cache ];then
   mkdir -p data/cache
fi


if [ ! -e data/DoctrineORMModule ];then
   mkdir -p data/DoctrineORMModule
fi

if [ ! -e data/DoctrineORMModule/Proxy ];then
   mkdir -p data/DoctrineORMModule/Proxy
fi

if [ ! -e data/uploads/attachment ];then
   mkdir -p  data/uploads/attachment
fi

if [ ! -e data/uploads/document ];then
   mkdir -p data/uploads/document
fi

if [ ! -e data/uploads/document ];then 
   mkdir -p data/uploads/document
fi

if [ ! -e data/uploads/export ]; then
   mkdir -p data/uploads/export
fi

chgrp -R www-data data/DoctrineORMModule data/cache data/uploads
chmod 775   data
chmod -R 775   data/DoctrineORMModule data/cache data/uploads
