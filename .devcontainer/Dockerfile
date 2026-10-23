FROM php:8.5-alpine

RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    postgresql-dev \
    freetds-dev \
    openssl-dev \
    imap-dev \
    gcc \
    make \
    autoconf \
    krb5-dev
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN pecl install imap && docker-php-ext-enable imap
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apk add --no-cache bash
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN chmod +x /root/.symfony5/bin/symfony
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
ENTRYPOINT [ "/bin/bash" ]
