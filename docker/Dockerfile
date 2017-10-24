FROM php:7.0

RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
RUN apt-get update \
    && apt-get install -y curl \
    git \
    zsh \
    ssh \
    supervisor \
    sudo \
    less \
    vim-tiny \
    nano \
    libzip-dev \
    && docker-php-ext-install -j5 zip mbstring pcntl \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean

COPY config/sudoers /etc/sudoers
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY config/php.ini /usr/local/etc/php/conf.d/

#COMPOSER
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /usr/local/bin
#CS FIX
RUN curl http://get.sensiolabs.org/php-cs-fixer.phar -o php-cs-fixer \
    && chmod 755 php-cs-fixer

# BOX
RUN curl -LSs https://box-project.github.io/box2/installer.php | php \
    && mv box.phar box \
    && chmod 755 /usr/local/bin/box

RUN useradd -ms /bin/zsh paraunit
RUN usermod -aG sudo paraunit
USER paraunit

WORKDIR /home/paraunit/projects

#Zsh minimal installation
RUN git clone --depth=1 git://github.com/robbyrussell/oh-my-zsh.git  ~/.oh-my-zsh \
    && sudo chsh -s /bin/zsh
ADD config/.zshrc /root/
ADD config/.zshrc /home/paraunit/

ENV TERM xterm-256color
ENV PHP_IDE_CONFIG="serverName=Paraunit"
ENV XDEBUG_CONFIG="remote_host=172.18.0.1 remote_port=9000"
CMD sudo /usr/bin/supervisord
