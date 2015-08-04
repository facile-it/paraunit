FROM debian:jessie

RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y apt-utils
RUN apt-get install -y curl
RUN apt-get install -y zsh
RUN apt-get install -y ssh
RUN apt-get install -y git
RUN apt-get install -y supervisor
RUN apt-get install -y sudo
RUN apt-get install -y php5 php5-curl php5-intl php5-mysql php5-xdebug php5-dev
RUN apt-get install -y git
RUN apt-get install -y less vim-tiny
RUN apt-get install -y apg
RUN apt-get install -y nano

COPY config/sudoers /etc/sudoers
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN useradd -ms /bin/bash paraunit
RUN usermod -aG sudo paraunit
USER paraunit

WORKDIR /home/paraunit/projects

#COMPOSER
RUN sudo curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

#CS FIX
RUN sudo curl http://get.sensiolabs.org/php-cs-fixer.phar -o php-cs-fixer
RUN sudo chmod a+x php-cs-fixer
RUN sudo mv php-cs-fixer /usr/local/bin/php-cs-fixer

#Zsh minimal installation
RUN git clone --depth=1 git://github.com/robbyrussell/oh-my-zsh.git  ~/.oh-my-zsh
RUN cp ~/.oh-my-zsh/templates/zshrc.zsh-template ~/.zshrc
RUN sudo chsh -s /bin/zsh

#----- BEGIN Enable sigsegv extension
WORKDIR /tmp/
RUN git clone https://github.com/facile-it/paraunit.git
WORKDIR /tmp/paraunit/.travis/sigsegv-extension
RUN phpize
RUN ./configure --enable-sigsegv
RUN make -j
COPY sigsegv/sigsegv.ini /etc/php5/cli/conf.d/
#----- END Enable sigsegv extension

WORKDIR /home/paraunit/projects
VOLUME ["/home/paraunit/projects"]

CMD sudo /usr/bin/supervisord
