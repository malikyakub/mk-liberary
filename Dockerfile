FROM php:8.1-apache

# Install system dependencies needed by Composer
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    git \
    curl

# Enable mysqli extension
RUN docker-php-ext-install mysqli

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Set working directory inside the container
WORKDIR /var/www/html/

# Copy project files into container
COPY . .

# Install PHP dependencies from composer.json
RUN composer install

# Expose port 80 for Apache
EXPOSE 80
