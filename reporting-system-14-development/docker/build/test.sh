#!/bin/sh

set -ex \
	&&( echo 'Installing required package for runtime config and to build php ext ' \
		&& apt-get update -y \
		&& apt-get install -y  \
					libzip4 libicu63  libmcrypt4   \
					libfreetype6  libpng16-16 \
					libjpeg62-turbo  bzip2  \
									\
					libbz2-dev  libicu-dev libc6-dev zlib1g-dev libfreetype6-dev \
					libpng-dev  libjpeg62-turbo-dev  libzip-dev \
					libmcrypt-dev $PHPIZE_DEPS \
	) \
		\
	&& ( echo 'Installing gd extension ' 			\
		&& docker-php-ext-configure gd --enable-gd  \
			--with-freetype=/usr/include/ 			\
			--with-jpeg=/usr/include/ 				\
		&& docker-php-ext-install gd 			\
		&& docker-php-ext-enable gd 			\
	) \
	&& ( echo "Install mcrypt via pecl, using yes ''| to accept default options for pecl " \
		&& pecl remote-info mcrypt         		\
		&& yes '' | pecl install mcrypt    		\
		&& docker-php-ext-enable mcrypt    		\
	) 										\
	&& echo 'Installing ext bz2, zip, intl mysql*, opcache' \
	&& docker-php-ext-configure  bz2   		\
	&& docker-php-ext-install bz2 		    \
	&& docker-php-ext-configure  intl  		\
	&& docker-php-ext-install  intl	  		\
	&& docker-php-ext-configure  zip   		\
	&& docker-php-ext-install   zip  		\
	&& docker-php-ext-install mysqli pdo_mysql opcache \
    && docker-php-ext-enable  mysqli pdo_mysql opcache \
		\
    && php -r 'print_r(mcrypt_list_modes ()); print_r(gd_info());' \
	&& php -m && php -r 'print_r(hash_algos);print_r(hash_hmac_algos());print_r(function_exists("sha3")?"sha3 EXISTS\n":"NO SHA3!!\n");'\
		\
	&& ( echo 'Remove packages that are not required during runtime ' \
		&& apt-get remove -y --purge \
					libbz2-dev  libicu-dev libc6-dev zlib1g-dev libfreetype6-dev \
					libpng-dev  libjpeg62-turbo-dev  libzip-dev \
					libmcrypt-dev $PHPIZE_DEPS \
		&& apt-get autoremove -y \
		&& apt-get clean all -y \
	) 
