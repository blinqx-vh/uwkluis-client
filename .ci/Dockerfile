FROM --platform=linux/amd64 php:8.2-cli-alpine

RUN apk update \
  && apk add \
    git \
    libzip-dev \
    curl \
    icu-dev

RUN docker-php-ext-install bcmath zip intl

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/local/bin/composer
