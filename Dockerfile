FROM php:8.1-apache

# Enable mysqli (used in your code)
RUN docker-php-ext-install mysqli

# Copy all project files into the container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Expose port 80 (for HTTP)
EXPOSE 80
