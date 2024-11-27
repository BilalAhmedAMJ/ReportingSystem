#!/bin/sh
current_dir=`pwd`

echo
echo updating $current_dir ...
echo
echo doing a "pull" from git ... may require you to provide password
echo

#git pull

if [ $? != 0 ];then
  echo Problem updating from git remote 
  echo update will abort
  exit  1
fi

echo 
echo updating auto loaders

#update vendor class loader
php composer.phar dump-autoload -o

# update module class loader
cd module/Application/
../../vendor/zendframework/zendframework/bin/classmap_generator.php .	

# update module template loader
export LIB_PATH="$current_dir/vendor/zendframework/zendframework/library/"
php  ../../bin/template_map_generator.php 

cd $current_dir

echo removing cache
sudo rm -rf data/cache/*

echo restarting apache
sudo service apache2 restart
