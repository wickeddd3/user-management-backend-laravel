FROM php:8.1-fpm

ARG user
ARG group
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Postgre PDO
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Setup working directory
WORKDIR /var/www/

# Create User and Group
RUN groupadd -g ${uid} ${group} && useradd -u ${uid} -ms /bin/bash -g ${group} ${user}

# Grant Permissions
RUN chown -R ${user} /var/www

# Select User
USER ${user}

# Copy permission to selected user
COPY --chown=${user}:${group} . .