FROM php:7.1-apache
COPY php.ini /usr/local/etc/php/

# Install PHP extensions
RUN curl -sL https://deb.nodesource.com/setup_6.x | bash - \
    && apt-get update && apt-get install -y \
      libicu-dev \
      libpq-dev \
      libpcre3-dev \
      libmcrypt-dev \
      mysql-client \
      libmysqlclient-dev \
      ruby-full \
      nodejs \
      default-jdk \
      openssh-server \
      git-core \
      ntp \
    && rm -r /var/lib/apt/lists/* \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-install \
      intl \
      mbstring \
      mcrypt \
      mysqli \
      pcntl \
      pdo_mysql \
      pdo_pgsql \
      pgsql \
      zip \
      opcache 

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Install Xdebug
RUN curl -fsSL 'https://xdebug.org/files/xdebug-2.4.0.tgz' -o xdebug.tar.gz \
    && mkdir -p xdebug \
    && tar -xf xdebug.tar.gz -C xdebug --strip-components=1 \
    && rm xdebug.tar.gz \
    && ( \
    cd xdebug \
    && phpize \
    && ./configure --enable-xdebug \
    && make -j$(nproc) \
    && make install \
    ) \
    && rm -r xdebug \
    && docker-php-ext-enable xdebug

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

RUN apt-get update && apt-get install yarn

ADD . /var/www/

#EXPOSE 80

RUN groupadd jenkins && useradd -d /home/jenkins -g jenkins -m jenkins

#RUN mkdir /home/jenkins/.ssh
#COPY files/jenkins_pub /home/jenkins/.ssh/authorized_keys
#RUN chown -R jenkins:jenkins /home/jenkins/.ssh \
# && chmod 700 /home/jenkins/.ssh \
# && chmod 600 /home/jenkins/.ssh/*

WORKDIR /var/www/

#Install phpunit
#RUN composer install
