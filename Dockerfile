FROM alpine:3.17

LABEL Maintainer="Ante de Baas @antedebaas on GitHub>" \
      Description="DMARC & SMTP-TLS Reports processor and visualizer"
RUN apk --update add ca-certificates
RUN apk --no-cache add \
        php81 \
        php81-fpm \
        php81-mysqli \
        php81-pgsql \
        php81-imap \
        php81-phar \
        php81-mbstring \
        php81-iconv \
        php81-ctype \
        php81-fileinfo \
        php81-xml \
        php81-xmlwriter \
        php81-simplexml \
        php81-dom \
        php81-tokenizer \
        php81-session \
        nginx \
        supervisor \
        curl
COPY dockerfiles/nginx.conf /etc/nginx/nginx.conf

COPY dockerfiles/fpm-pool.conf /etc/php81/php-fpm.d/www.conf
COPY dockerfiles/php.ini /etc/php81/conf.d/custom.ini
RUN wget https://getcomposer.org/composer-stable.phar -O /usr/local/bin/composer && chmod +x /usr/local/bin/composer

COPY dockerfiles/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p /var/www/html
RUN chown -R nobody.nobody /var/www/html && \
  chown -R nobody.nobody /run && \
  chown -R nobody.nobody /var/lib/nginx && \
  chown -R nobody.nobody /var/log/nginx

USER nobody

WORKDIR /var/www/html
COPY --chown=nobody . /var/www/html/
COPY --chown=nobody .env /var/www/html/
RUN /usr/local/bin/composer install
#RUN echo '0 0 * * * php /var/www/html/bin/console app:checkmailbox >/dev/null 2>&1' > /etc/crontabs/root #Does not work

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping


