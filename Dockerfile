FROM alpine:3.20

VOLUME /var/www
WORKDIR /var/www

RUN apk add curl \
  php83 \
  php83-ctype \
  php83-curl \
  php83-dom \
  php83-fileinfo \
  php83-fpm \
  php83-gd \
  php83-mbstring \
  php83-mysqli \
  php83-openssl \
  php83-phar \
  php83-session \
  php83-tokenizer \
  php83-xml \
  php83-xmlreader \
  php83-xmlwriter \
  supervisor \
  php83-pecl-imagick \
  unzip \
  php83-bcmath \
  php83-bz2 \
  php83-exif \
  php83-pecl-mcrypt \
  php83-opcache \
  php83-intl \
  php83-pdo_mysql \
  php83-pecl-redis \
  php83-sockets \
  php83-pecl-swoole \ 
  php83-zip \
  php83-pcntl


COPY supervisor/supervisord.conf /etc/supervisord.conf
RUN chown -R nobody.nobody /var/www /run /var/log
USER nobody

EXPOSE 9000 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
