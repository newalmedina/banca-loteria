#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0

set -xe

# Install git (the php image doesn't have it) which is required by composer
# apt-get update -yqq
#apt-get install git -yqq

# Install mysql driver
# Here you can install any other extension that you need
# docker-php-ext-install pdo_mysql

apt-get update \
&& apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libbz2-dev \
	libxrender1 \
	libfontconfig \
    curl \
    git \
    unzip \
    nano \
    vim \
    bash-completion \
    htop \
    net-tools \
    apt-utils \
    gnupg2 \
    libtidy-dev \
    openssl \
    libssl-dev \
    wget \
    zsh \
	libicu-dev \
	libc-client-dev \
	libkrb5-dev \
	cron \
	supervisor \
    libxml2-dev

docker-php-ext-install \
    zip \
    bz2 \
    bcmath \
    tidy \
	intl

docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/  > /dev/null
docker-php-ext-install gd  > /dev/null
docker-php-ext-enable tidy  > /dev/null
docker-php-ext-configure imap --with-kerberos --with-imap-ssl  > /dev/null
docker-php-ext-install imap  > /dev/null
docker-php-ext-install soap  > /dev/null

# Install phpunit, the tool that we will use for testing
# curl --location --output /usr/local/bin/phpunit https://phar.phpunit.de/phpunit.phar
# chmod +x /usr/local/bin/phpunit

# Install composer and put binary into $PATH
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP Code sniffer
curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar \
    && chmod 755 phpcs.phar \
    && mv phpcs.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/phpcs.phar /usr/local/bin/phpcs \
    && curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar \
    && chmod 755 phpcbf.phar \
    && mv phpcbf.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/phpcbf.phar /usr/local/bin/phpcbf

# Install PHP_CodeSniffer
composer global require "squizlabs/php_codesniffer=*"