FROM php:8.3-apache
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# Copier les identifiants git locaux dans le container
RUN composer --version && php -v
RUN apt-get update && apt-get upgrade -y
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get install -y libzip-dev p7zip-full && docker-php-ext-install zip
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN apt-get install git nano -y
RUN rm /root/.bashrc
RUN curl -o /root/.bashrc https://gist.githubusercontent.com/marioBonales/1637696/raw/93a33aa5f1893f46773483250214f8b8b496a270/.bashrc
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled
# Extensions PHP diverses
RUN apt-get install -y libxslt-dev
RUN docker-php-ext-install xsl
RUN apt-get install -y librabbitmq-dev && pecl install amqp && docker-php-ext-enable amqp
RUN apt-get install -y libicu-dev && docker-php-ext-configure intl && docker-php-ext-install intl
RUN apt-get install -y libpng-dev && docker-php-ext-install gd
RUN apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql
RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis
# Install NodeJS
RUN apt-get update && apt-get install -y ca-certificates curl gnupg
RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
ENV NODE_MAJOR=20
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list
RUN apt-get update && apt-get install nodejs -y

# Configuration Apache pour le port 8000 et le module rewrite
RUN echo "Listen 8000" >> /etc/apache2/ports.conf
RUN a2enmod rewrite

# Copier la configuration Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exposer aussi le port 8000
EXPOSE 80 8000  