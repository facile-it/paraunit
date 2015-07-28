FROM php:5.5-cli

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install -y curl
RUN apt-get install -y zsh
RUN apt-get install -y ssh
RUN apt-get install -y git
RUN apt-get install -y supervisor
RUN apt-get install -y sudo

COPY config/sudoers /etc/sudoers
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN useradd -ms /bin/bash paraunit
RUN usermod -aG sudo paraunit

WORKDIR /home/paraunit/projects

USER paraunit

RUN curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

RUN git clone --depth=1 git://github.com/robbyrussell/oh-my-zsh.git  ~/.oh-my-zsh
RUN cp ~/.oh-my-zsh/templates/zshrc.zsh-template ~/.zshrc
RUN sudo chsh -s /bin/zsh

EXPOSE 80

VOLUME ["/home/paraunit/projects"]

CMD sudo /usr/bin/supervisord
