FROM php:8.1-apache
MAINTAINER Stéphane

VOLUME /var/www/webapp

# PHP8/Apache
## PHP7.4/Apache

RUN apt-get update && apt-get install -y \
    autoconf \
    build-essential \
    libpng-dev \
    libtool \
    pkg-config \
    libgss3 \
    libgd-dev \
    zlib1g-dev \
    libicu-dev \
    g++ \
    procps \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    libzip-dev \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    vim \
    nano \
    unzip \
    git \
    curl \
    procps \
    gettext-base \
    mariadb-client-10.5 \
#    sqlite3 \
#    sendmail \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
    libmagickwand-dev --no-install-recommends \
    && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql zip exif pcntl mysqli \
    && docker-php-ext-enable mysqli \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && pecl install imagick-3.7.0 \
    && docker-php-ext-enable imagick \
    # The xdebug packages slow down performance significantly, uncomment only when you need it
#    && pecl install xdebug-3.1.3 \
#    && docker-php-ext-enable xdebug \
    && docker-php-ext-configure intl --enable-intl \
    && docker-php-ext-install intl

USER root

# Configure apache
COPY apache/webserver.conf /etc/apache2/sites-available/webserver.conf
RUN a2enmod rewrite \
    && sed -i 's:^\(Timeout\) .*$:\1 5000:' /etc/apache2/apache2.conf \
    && sed -i 's:^\(MaxKeepAliveRequests\) .*$:\1 1000:' /etc/apache2/apache2.conf \
    && sed -i 's:^\(KeepAliveTimeout\) .*$:\1 50:' /etc/apache2/apache2.conf \
    && a2dissite 000-default.conf \
    && a2ensite webserver.conf \
    && sed -i 's:^\(TraceEnable\) .*$:\1 Off:' /etc/apache2/conf-available/security.conf \
    && sed -i 's:^\(ServerSignature\) .*$:\1 Off:' /etc/apache2/conf-available/security.conf \
    && sed -i 's:^\(ServerTokens\) .*$:\1 Prod:' /etc/apache2/conf-available/security.conf \
    && a2enmod headers

# Security addendum
COPY apache/security.conf /etc/apache2/conf-available/security.conf
RUN a2enconf security.conf

# Install Composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.6

# Install Node.js
#RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
#    && apt-get install -y nodejs \
#    && node -v \
#    && npm -v \
#    && rm -rf /var/lib/apt/lists/*

ARG PUID=${PUID}
ARG PGID=${PGID}
ENV PUID ${PUID}
ENV PGID ${PGID}

# Add user for web application
RUN groupadd -g ${PGID} www && useradd -l -u ${PUID} -ms /bin/bash -g www www
RUN usermod -u ${PUID} www && groupmod -g ${PGID} www

# Fix temporary NPM bug
#RUN mkdir /home/www/.npm && chown -R www:www /home/www/.npm

# Change current user to www
USER www:www

WORKDIR /var/www/webapp
