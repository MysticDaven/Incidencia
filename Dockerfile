FROM php:8.2-fpm

# 1. Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    gnupg \
    curl \
    g++ \
    make \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unixodbc-dev \
    && docker-php-ext-install opcache pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# 2. Configura OPcache directamente (sin archivo externo)
RUN echo "\
opcache.enable=1\n\
opcache.memory_consumption=256\n\
opcache.interned_strings_buffer=32\n\
opcache.max_accelerated_files=20000\n\
opcache.validate_timestamps=0\n\
opcache.enable_cli=1\n\
opcache.jit_buffer_size=100M\n\
" > /usr/local/etc/php/conf.d/opcache.ini    

# Instala drivers de SQL Server
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini   

# 2. Instala Microsoft ODBC Driver for SQL Server (versión para Bookworm)
# RUN mkdir -p /etc/apt/keyrings \
#     && curl -sS https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /etc/apt/keyrings/microsoft.gpg \
#     && echo "deb [signed-by=/etc/apt/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list \
#     && apt-get update \
#     && ACCEPT_EULA=Y apt-get install -y msodbcsql18 \
#     && apt-get install -y unixodbc-dev

# Install MS ODBC Driver for SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/9/prod.list > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update && ACCEPT_EULA=Y apt-get install -y \
    apt-transport-https \
    msodbcsql17    

# 3. Instala extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        opcache \
        zip

# Instala Node.js 18 (estable para Laravel)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# ---- Antes del WORKDIR /var/www ----
# Copia el certificado al almacén de confianza del sistema
# COPY sqlserver-cert.crt /usr/local/share/ca-certificates/
# RUN update-ca-certificates

# # Configura ODBC para usar cifrado estricto (sin desactivar verificación)
# RUN echo "[ODBC Driver 18 for SQL Server]\nEncrypt=yes" >> /etc/odbcinst.ini    

# 4. Instala Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# 5. Configuración básica
WORKDIR /var/www
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copia el código
COPY . .

# Instala dependencias (sin scripts post-install)
RUN composer install --no-interaction --prefer-dist --no-dev --no-scripts