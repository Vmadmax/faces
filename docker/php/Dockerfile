FROM php:8.0-fpm
ARG imagemagic_config=/etc/ImageMagick-6/policy.xml

RUN apt-get update
RUN apt-get install -y libmcrypt-dev
RUN apt-get install -y mariadb-client
RUN apt-get install -y libmagickwand-dev --no-install-recommends
RUN apt-get install -y ghostscript
RUN apt-get install -y imagemagick
RUN apt-get install -y locales
RUN apt-get install -y rsync
RUN sed -i -e 's/# de_DE ISO-8859-1/de_DE ISO-8859-1/' /etc/locale.gen
RUN sed -i -e 's/# de_DE.UTF-8/de_DE.UTF-8/' /etc/locale.gen
RUN dpkg-reconfigure --frontend=noninteractive locales
RUN pecl install imagick
RUN docker-php-ext-install gd
RUN docker-php-ext-enable imagick
RUN docker-php-ext-install pdo_mysql
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN apt-get install -y git
RUN apt-get update && apt-get install -y zlib1g-dev libzip-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-install pcntl
ADD www.conf /usr/local/etc/php-fpm.d/www.conf
ADD php.ini /usr/local/etc/php/conf.d/php-8.0.ini
ADD copy-ssh-files.sh /usr/local/bin/copy-ssh-files.sh
RUN chmod +x /usr/local/bin/copy-ssh-files.sh
RUN if [ -f $imagemagic_config ] ; then sed -i 's/<policy domain="coder" rights="none" pattern="PDF" \/>/<policy domain="coder" rights="read|write" pattern="PDF" \/>/g' $imagemagic_config ; else echo did not see file $imagemagic_config ; fi
ENTRYPOINT ["/usr/local/bin/copy-ssh-files.sh", "php-fpm"]