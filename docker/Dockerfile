FROM php:7.3-alpine

RUN apk --no-cache add \
        $PHPIZE_DEPS \
        curl \
        git \
        zsh \
        openssh-client \
        supervisor \
        sudo \
        less \
        vim \
        nano \
        libzip-dev \
    && docker-php-ext-install -j5 zip pcntl \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY config/sudoers /etc/sudoers
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY config/php.ini /usr/local/etc/php/conf.d/

#COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN adduser -u 1000 -G wheel -D paraunit -s /bin/zsh
USER paraunit

WORKDIR /home/paraunit/projects

#Zsh minimal installation
RUN git clone --depth=1 git://github.com/robbyrussell/oh-my-zsh.git  ~/.oh-my-zsh
ADD config/.zshrc /root/
ADD config/.zshrc /home/paraunit/

ENV TERM xterm-256color
ENV PHP_IDE_CONFIG="serverName=Paraunit"
ENV XDEBUG_CONFIG="remote_host=172.18.0.1 remote_port=9000"
CMD sudo /usr/bin/supervisord
