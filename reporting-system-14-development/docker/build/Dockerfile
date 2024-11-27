FROM php:5.6-apache

ENV APACHE_LOG_DIR /var/log/apache2
ENV APP_ROOT /var/www/html

RUN set -ex \
	echo 'Redirect logs to stdout and stderr' \
	&& ln -sf /proc/self/fd/1 /var/log/apache2/access.log   	\
    && ln -sf /proc/self/fd/1 /var/log/apache2/error.log 		\
	\
	&&( echo 'Installing required package for runtime config and to build php ext ' \
		&& apt-get update -y \
		&& apt-get install -y  \
					libzip4 libicu57  libmcrypt4   \
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
	&& echo 'Installing ext bz2, zip, intl mysql*, opcache' \
	&& docker-php-ext-configure  bz2   		\
	&& docker-php-ext-install bz2 		    \
	&& docker-php-ext-configure  intl  		\
	&& docker-php-ext-install  intl	  		\
	&& docker-php-ext-configure  zip   		\
	&& docker-php-ext-install   zip  		\
	&& docker-php-ext-install mysqli pdo_mysql opcache mcrypt \
    && docker-php-ext-enable  mysqli pdo_mysql opcache mcrypt \
		\
	&& ( echo 'Remove packages that are not required during runtime ' \
		&& apt-get remove -y --purge \
					libbz2-dev  libicu-dev libc6-dev zlib1g-dev libfreetype6-dev \
					libpng-dev  libjpeg62-turbo-dev  libzip-dev \
					libmcrypt-dev $PHPIZE_DEPS \
		&& apt-get autoremove -y \
		&& apt-get clean all -y \
	) \
	&&  a2enmod rewrite
	 
COPY ./sha3.ini /usr/local/etc/php/conf.d/sha3.ini
COPY ./sha3.so /usr/local/lib/php/extensions/no-debug-non-zts-20131226/sha3.so

#Keep what we did
ADD . /docker/build

#Test Built ext
RUN php -r 'print_r(mcrypt_list_modes ()); print_r(gd_info());' \
&& php -m && php -r 'print_r(hash_algos());print_r(function_exists("sha3")?"sha3 EXISTS\n":"NO SHA3!!\n");'

ENTRYPOINT ["/docker/build/app-docker-entrypoint.sh"]

CMD ["apache2-foreground"]
