## Setup parallel version of PHP in LAMP env. 
At time we get into situation where default version of PHP installed on LAMP server does not work well with application. If possible best way is always to upgrade default PHP to latest.

However, sometimes system need to support applications with conflicting version requirements. That's when we need to install multiple versions in parallel on the system. We can again either choose to use parallel installation of entire LAMP stack or just PHP.

One way is to use custom version of PHP via FastCGI (mod_fastcgi) in Apache.

*Following are steps to do this on a CentOS 5.9 box running Plesk 10.x*

### 1. Download and Compile PHP
You can use instructions outlined by Plesk documentation http://kb.sp.parallels.com/en/118378
Please note for our application we need to have at least php version >=5.3.23 (as per current dependencies) and following extensions.

curl
fastcgi 
intl
mbstring
openssl
pdo_mysql
soap

### 2. Install / enable mod_fastcgi

Follow fastcgi module related instructions from http://blog.servergrove.com/2011/08/22/how-to-setup-multiple-php-versions-on-apache/

>**Fastcgi**
>
>Get the mod_fastcgi sources into the server
>
>$ wget http://www.fastcgi.com/dist/mod\_fastcgi-current.tar.gz

>$ tar -xzf mod_fastcgi-current.tar.gz

>Compile the module using httpd libs
>
>$ cd mod\_fastcgi-2.4.6/

>$ cp Makefile.AP2 Makefile

>$ make top_dir=/usr/lib64/httpd

>$ make install top_dir=/usr/lib64/httpd

>Load module for fastcgi on the apache configuration


>// CentOS /etc/httpd/conf.d/fastcgi.conf

>// Ubuntu /etc/apache2/mods-available/fastcgi.load

>LoadModule fastcgi\_module /path/to/mod\_fastcgi.so

>If Ubuntu is your distro then you must enable the module with a2enmod


Please note link to source code is not correct, you will have to use following URL 

http://www.fastcgi.com/dist/mod_fastcgi-current.tar.gz

### 3. Setup Apache and fastcgi script wrapper.
You can use following Apache configs

```
LoadModule fastcgi_module modules/mod_fastcgi.so

<VirtualHost 199.7.237.109:80>
    ServerName amj.pileussystems.com

    DocumentRoot /var/www/amj_reports/public

    SetEnv APPLICATION_ENV "development"
 
    LogLevel debug   

    <IfModule mod_fastcgi>

        #fast-cgi setup #used for multiple php versions 
        FastCgiServer  /usr/local/php5437-cgi/bin/php-cgi   -socket /var/run/php5-fpm.sock -pass-header Authorization  -idle-timeout 240 
        #FastCgiConfig -idle-timeout 110 -killInterval 120 -pass-header HTTP_AUTHORIZATION -autoUpdate

        AddHandler fastcgi-script .php .phtml



    </IfModule>

#       FastCgiServer  /usr/local/php5437-cgi/bin/php-cgi-5.4.37  -socket /var/run/php5-fpm.sock -pass-header Authorization  -idle-timeout 240 
    <Directory  /var/www/amj_reports/public>
#       AddHandler fastcgi-script  .php  .phtml
        Options +ExecCGI
        DirectoryIndex index.php
        Order allow,deny
        AllowOverride All
        Allow from all
    </Directory>

    ErrorLog  logs/amj_reports-error.log
    CustomLog logs/amj_reports-access.log common

</VirtualHost>

```


### References
http://kb.sp.parallels.com/en/118378
http://www.metod.si/multiple-php-versions-with-apache-2-fastcgi-phpfarm-on-ubuntu/
http://www.fastcgi.com/drupal/?q=node/17
http://thejibe.com/blog/14/02/phpfarm
http://blog.servergrove.com/2011/08/22/how-to-setup-multiple-php-versions-on-apache/
http://blog.kmp.or.at/2013/06/apache-2-2-on-debian-wheezy-w-php-fpm-fastcgi-apc-and-a-kind-of-suexec/