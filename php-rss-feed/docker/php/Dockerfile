# Set source
FROM php:7.3-fpm

# Set user/group
ENV USER=endemic
ENV GROUP=endemic

RUN groupadd -g 1000 ${USER}
RUN useradd -d /home/${USER} -s /bin/bash -u 1000 -g 1000 ${USER}

# User and group own home directory
RUN mkdir /home/${USER}
RUN chown -R ${USER}:${GROUP} /home/${USER}

# Install apcu
RUN pecl install apcu

# Install php
RUN apt-get update && apt-get install -y \
        curl \
        wget \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libzip-dev \
        libmcrypt-dev \
    && docker-php-ext-install -j$(nproc) iconv mbstring mysqli bcmath pdo_mysql zip \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# Install NPM + Yarn
RUN apt-get update && \
    apt-get install -y --no-install-recommends gnupg && \
    curl -sL https://deb.nodesource.com/setup_12.x | bash - && \
    apt-get update && \
    apt-get install -y --no-install-recommends nodejs && \
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && \
    apt-get update && \
    apt-get install -y --no-install-recommends yarn && \
    npm install -g npm

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add custom ini to php
ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

# Set working dir
WORKDIR /var/www

# Set user
USER ${USER}

# start php-fpm
CMD ["php-fpm"]