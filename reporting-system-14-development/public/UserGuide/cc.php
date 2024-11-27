<?php
echo "Cleaning opcache ... ";

#opcache_reset();

unlink(dirname(__FILE__).'/../../data/cache/module-config-cache..php');

echo var_dump(pathinfo(dirname(__FILE__).'/../../../hex_generator'));

echo " ... Done!";
?>

