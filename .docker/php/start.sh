#!/bin/bash
sudo ln -s /etc/nginx/sites/$FRAMEWORK.conf /etc/nginx/sites/enabled.conf

# Starts FPM
nohup /usr/sbin/php-fpm8 -y /etc/php8/php-fpm.conf -F -O > /dev/null 2>&1 &

# Install the Dependencies
composer install

# Clear All Caches
php /var/www/app/artisan optimize:clear

# Starts Queue
nohup php /var/www/app/artisan queue:listen &

# Run Migrations and Seeders
php /var/www/app/artisan migrate --seed

# Starts NGINX!
nginx
