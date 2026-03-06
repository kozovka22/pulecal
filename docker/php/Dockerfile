FROM php:8.4-fpm-alpine

RUN apk add --no-cache --virtual .build-deps \
      autoconf \
      make \
      gcc \
      g++ \
      libc-dev \
      libzip-dev \
      freetype-dev \
      libpng-dev \
      libjpeg-turbo-dev \
      icu-dev \
      oniguruma-dev \
      libxslt-dev \
      zlib-dev \
      openssl \
      git \
      unzip \
      bash \
      acl \
      vim \
      nim \
      nim-dev \
      busybox-extras \
  && apk add --no-cache libpng libjpeg freetype icu libzip libxslt

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql zip xsl gd intl opcache exif mbstring

# crontabs configuration
COPY docker/php/crontab /etc/crontabs/root

# composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

RUN apk add --no-cache nim nim-dev mariadb-connector-c-dev
RUN mkdir -p /usr/local/lib/nim
RUN wget https://github.com/nim-lang/db_connector/archive/refs/heads/devel.tar.gz
RUN tar -xzf devel.tar.gz
RUN cp -r db_connector-devel/src/db_connector /usr/local/lib/nim/
RUN rm -rf db_connector-devel devel.tar.gz

COPY backend/bin/cleanup_calendars.nim /var/www/html/backend/bin/cleanup_calendars.nim
RUN nim c -d:release --path:/usr/local/lib/nim /var/www/html/backend/bin/cleanup_calendars.nim


WORKDIR /var/www/html/

# Cleanup build deps
RUN apk del .build-deps

## Package list přepsán z debian do alpine package manageru pomocí Github Copilot (GPT-5 mini)
## https://medium.com/@oumaymaneffati/the-easy-way-to-install-symfony-with-docker-b6fca3997d2c