FROM php:8.2-alpine

RUN apk --no-cache add \
        $PHPIZE_DEPS \
        curl \
        git \
        icu-dev \
        less \
        libzip-dev \
        linux-headers \
        nano \
        openssh-client \
        sudo \
        supervisor \
        vim \
        zsh
RUN docker-php-ext-install -j5 zip pcntl intl
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

#COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN adduser -u 1000 -G wheel -D paraunit -s /bin/zsh
USER paraunit

WORKDIR /home/paraunit/projects

#Zsh minimal installation
RUN sh -c "$(curl -fsSL https://raw.githubusercontent.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"

ENV TERM xterm-256color
