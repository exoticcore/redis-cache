FROM php:7.4-apache

# Enable Apache mod_rewrite and install necessary PHP extensions
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy the source code to the container
COPY src/ /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose port 3035
EXPOSE 3035

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Start Apache server
CMD ["apache2-foreground"]