FROM php:7.4-apache as base
ENV TZ="Europe/Paris"
WORKDIR /var/www/html

RUN --mount=type=cache,id=apt-cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,id=apt-lib,target=/var/lib/apt,sharing=locked \
    --mount=type=cache,id=debconf,target=/var/cache/debconf,sharing=locked \
    apt-get update -qq && \
    apt-get install -qy \
    gnupg



RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor -o /usr/share/keyrings/yarn-archive-keyring.gpg - && \
    echo "deb [signed-by=/usr/share/keyrings/yarn-archive-keyring.gpg] https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list


RUN --mount=type=cache,id=apt-cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,id=apt-lib,target=/var/lib/apt,sharing=locked \
    --mount=type=cache,id=debconf,target=/var/cache/debconf,sharing=locked \
    apt-get update -qq && \
    apt-get install -qy \
    git \
    libicu-dev \
    libzip-dev \
    unzip \
    zip \
    zlib1g-dev \
    yarn


# PHP Extensions
RUN docker-php-ext-configure zip && \
    docker-php-ext-install -j$(nproc) intl opcache pdo_mysql zip

# # WKHTMLTOPDF
#RUN --mount=type=cache,sharing=locked,id=aptlib,target=/var/lib/apt \
#    --mount=type=cache,sharing=locked,id=aptcache,target=/var/cache/apt \
#     apt-get install -y fontconfig libfreetype6 libjpeg62-turbo libpng16-16 xfonts-75dpi xfonts-base && \
#     apt-get install -y libxrender1 && \
#     apt-get install -y wget && \
#     wget -q -O wkhtmltox.deb https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/wkhtmltox_0.12.6.1-3.bookworm_amd64.deb && \
#     dpkg -i wkhtmltox.deb && \
#     apt-get install -y -f && \
#     rm wkhtmltox.deb

COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf


COPY ./docker/install_composer.sh /tmp/install_composer.sh
RUN /tmp/install_composer.sh && \
    mv composer.phar /usr/local/bin/composer && \
    rm /tmp/install_composer.sh

RUN a2enmod rewrite

copy docker/entrypoint.sh /entrypoint.sh
ENTRYPOINT [ "/entrypoint.sh" ]

CMD ["apache2-foreground"]
USER www-data

#########################################
# Image pour dev
##########################################
From base as dev

# PHP XDEBUG
USER root
RUN pecl install xdebug-3.1.5 && \
     docker-php-ext-enable xdebug && \
     pecl clear-cache

COPY ./docker/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

USER www-data


#########################################
# Image pour prod
##########################################

From base as prod

RUN mkdir -p /var/www/html/var/cache/ && chown www-data:www-data /var/www/html/var/cache/
RUN mkdir -p /var/www/html/var/log/ && chown www-data:www-data /var/www/html/var/log/
RUN mkdir -p /var/www/html/var/upload/ && chown www-data:www-data /var/www/html/var/upload/
RUN mkdir /var/www/.symfony && chown www-data:www-data /var/www/.symfony
RUN mkdir /var/www/.cache && chown www-data:www-data /var/www/.cache
RUN mkdir /var/www/.yarn && chown www-data:www-data /var/www/.yarn
RUN touch /var/www/.yarnrc && chown www-data:www-data /var/www/.yarnrc

RUN APP_ENV=prod composer install  --no-dev --prefer-dist --no-scripts
RUN yarn install
RUN yarn build

RUN php bin/console assets:install

COPY --chown=www-data:www-data  . /var/www/html
