#!/bin/sh

set -ex \
	&&( \
		apt-get update -y \
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
	&& ( \
		 docker-php-ext-configure gd --with-gd  \
			--with-freetype=/usr/include/ 	\
			--with-jpeg=/usr/include/ 		\
		&& docker-php-ext-install gd 			\
	) \
		\
	&& docker-php-ext-install mysqli pdo_mysql opcache \
    && docker-php-ext-enable  mysqli pdo_mysql opcache \
	&& ( \
		apt-get remove -y --purge \
					libbz2-dev  libicu-dev libc6-dev zlib1g-dev libfreetype6-dev \
					libpng-dev  libjpeg62-turbo-dev  libzip-dev \
					libmcrypt-dev $PHPIZE_DEPS \
		&& apt-get autoremove -y \
		&& apt-get clean all -y \
	) 
