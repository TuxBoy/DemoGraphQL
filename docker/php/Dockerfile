FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
      wget \
      git \
      fish

RUN apt-get update && apt-get install -y libzip-dev && docker-php-ext-install pdo zip

# Support de MySQL & de sqlite
RUN docker-php-ext-install mysqli pdo_mysql
# RUN docker-php-ext-install pdo_sqlite && docker-php-ext-enable pdo_sqlite
RUN mkdir /db
# RUN /usr/bin/sqlite3 /db/data.sqlite

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Symfony tool
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
  mv /root/.symfony/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www

EXPOSE 9000
