FROM php:7.4-fpm-buster

RUN set -xe; \
  apt-get update \
  && apt-get install -y \
  # for intl extension
  libicu-dev \
  # for gd
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  # for mbstring
  libonig-dev \
  # memchached extension
  libmemcached-dev \
  # allow mailing to work
  sendmail \
  # allow reading of image exif
  exiftool \
  # pecl installs
  && docker-php-ext-install exif \
  && pecl install xdebug \
  && pecl install memcached \
  # enable pecl installed extentions
  && docker-php-ext-enable xdebug \
  && docker-php-ext-enable memcached \
  && docker-php-ext-enable exif \
  # built in extensions install
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) \
  gd \
  mbstring \
  pdo \
  pdo_mysql \
  intl \
  # cleanup
  && pecl clear-cache \
  && rm -rf \
  /var/lib/apt/lists/* \
  /usr/src/php/ext/* \
  /tmp/*

# Install NGINX
RUN apt-get update -y && apt-get -y install nginx

COPY ./container/php/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY ./container/php/00-php.ini /usr/local/etc/php/conf.d/00-php.ini
COPY ./container/php/10-xdebug.ini /usr/local/etc/php/conf.d/10-xdebug.ini
COPY ./container/php/vanilla-docker-sendmail.ini /usr/local/etc/php/conf.d/vanilla-docker-sendmail.ini

COPY ./container/resources/certificates /usr/local/share/ca-certificates
COPY ./container/resources/sphinx /sphinx

COPY ./container/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./container/nginx/fastcgi.conf /etc/nginx/fastcgi.conf

COPY ./container/resources/certificates /certificates
COPY ./container/resources/nginx/conf.d /etc/nginx/conf.d
COPY ./container/resources/nginx/sites-available /etc/nginx/sites-available
COPY ./container/resources/nginx/sites-enabled /etc/nginx/sites-enabled

COPY . /srv/vanilla-repositories/vanilla

COPY ./container/docker-entrypoint.sh /docker-entrypoint.sh

RUN mkdir -p /shared/var/run/
RUN mkdir -p /var/log/php-fpm
RUN mkdir -p /var/log/nginx
CMD ["/docker-entrypoint.sh"]
