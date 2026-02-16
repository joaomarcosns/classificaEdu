FROM dunglas/frankenphp:php8.4

RUN apt-get update \
	&& apt-get install -y --no-install-recommends git unzip \
	&& rm -rf /var/lib/apt/lists/*

RUN install-php-extensions pdo_pgsql pgsql redis opcache pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . /app
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
COPY docker/php/entrypoint-octane.sh /usr/local/bin/entrypoint-octane

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader; fi

RUN chmod +x /usr/local/bin/entrypoint-octane

EXPOSE 8000

CMD ["/usr/local/bin/entrypoint-octane"]
