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
  # wget
  wget \
  gnupg2 \
  git \
  gettext-base \
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

# Enable Azure SSH
RUN apt-get update -y \
  && apt-get install -y openssh-server \
  && echo "root:Docker!" | chpasswd
RUN mkdir -p /tmp
RUN rm -f /etc/ssh/sshd_config
COPY ./container/sshd_config /etc/ssh/
COPY ./container/ssh_setup.sh /tmp
RUN chmod -R +x /tmp/ssh_setup.sh \
  && (sleep 1;/tmp/ssh_setup.sh 2>&1 > /dev/null) \
  && rm -rf /tmp/*

# Install NGINX
RUN apt-get update -y && apt-get -y install nginx

# Install yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update -y && apt-get -y install nodejs yarn

# Install composer
RUN wget https://getcomposer.org/composer-1.phar \
  && mv composer-1.phar /usr/local/bin/composer \
  && chmod 775 /usr/local/bin/composer

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

RUN mkdir -p /shared/var/run/s
RUN mkdir -p /var/log/php-fpm
RUN mkdir -p /var/log/nginx

WORKDIR /srv/vanilla-repositories/vanilla
RUN composer install && yarn build
RUN chmod 777 cache

CMD ["/docker-entrypoint.sh"]
ENV PORT 80
ENV SSH_PORT 2222
EXPOSE 80 2222
