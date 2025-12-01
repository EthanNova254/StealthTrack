# Dockerfile
FROM php:8.2-apache

# Install required extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Enable Apache modules
RUN a2enmod rewrite headers

# Configure Apache
RUN echo '<Directory /var/www/html>\n\
    Options -Indexes +FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/security.conf && \
    a2enconf security

# Set up working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Create required directories with proper permissions
RUN mkdir -p data uploads && \
    chown -R www-data:www-data data uploads && \
    chmod -R 755 data uploads

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Security hardening
RUN echo 'ServerTokens Prod\n\
ServerSignature Off\n\
Header always set X-Content-Type-Options "nosniff"\n\
Header always set X-Frame-Options "SAMEORIGIN"\n\
Header always set X-XSS-Protection "1; mode=block"\n\
Header always set Referrer-Policy "strict-origin-when-cross-origin"' >> /etc/apache2/conf-enabled/security.conf

EXPOSE 80

CMD ["apache2-foreground"]
