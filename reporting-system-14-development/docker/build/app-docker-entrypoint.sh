#!/bin/sh -x


# We have ZF app dir 
if [ "x${APP_ROOT}" != "x" ] && [ -d $APP_ROOT ]; then
  cd ${APP_ROOT}
  echo in $(pwd)

  #
  # Always update dependencies on startup
  #if [ ! -e ./composer.lock ] && [ -e ./composer.phar ]; then

        #update composer
        php ./composer.phar "selfupdate"

        #install dependencies
        php ./composer.phar "install"
  #fi

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

fi

## Call default docker-php-entrypoint
exec /usr/local/bin/docker-php-entrypoint "$@"