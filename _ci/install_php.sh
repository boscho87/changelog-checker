#!/bin/bash

# We need to install dependencies only for Docker
[[ ! -e /.dockerenv ]] && exit 0
set -xe

# Install from apt-get
apt-get update -yqq
apt-get install git -yqq
apt-get install wget -yqq
apt-get install zlib1g-dev -yqq
apt-get install libzip-dev -yqq
apt-get install unzip -yqq
apt-get install libpng-dev -yqq
apt-get install libonig-dev -yqq

# Install composer
#wget https://getcomposer.org/composer.phar
wget https://getcomposer.org/download/2.0.13/composer.phar
mv ./composer.phar  /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Xdebug version 3.0.0 (and probably onwards) breaks compatibility with phpunit version 8.3.5 (currently used version) - nbr 2020-12-02
pecl install xdebug-2.9.8
# Here you can install any other extension that you need
docker-php-ext-enable xdebug
docker-php-ext-install mbstring
docker-php-ext-install zip


apt-get install -y locales >/dev/null
echo "de_CH UTF-8" > /etc/locale.gen
locale-gen de_CH.UTF-8
export LC_ALL=de_CH.UTF-8
