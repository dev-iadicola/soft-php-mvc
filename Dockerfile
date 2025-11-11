# ==========================
#  PHP 8.4 + Composer + SQLite (FULL)
# ==========================
FROM php:8.4-cli

# Installa tutto il necessario
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    sqlite3 \
    libsqlite3-0 \
    libsqlite3-dev \
    libzip-dev \
    && docker-php-ext-configure pdo_sqlite --with-pdo-sqlite=/usr \
    && docker-php-ext-install pdo pdo_sqlite \
    && docker-php-ext-enable pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Installa Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia tutto il progetto
COPY . .

# Installa dipendenze PHP
RUN composer install --no-interaction --prefer-dist

# Testa che SQLite funzioni davvero
RUN php -r "new PDO('sqlite::memory:'); echo 'SQLite OK\n';"

# Mostra moduli caricati (debug)
RUN php -m | grep -E "pdo|sqlite" || true

# Comando di default
CMD ["vendor/bin/phpunit", "--testdox", "--colors=always"]
