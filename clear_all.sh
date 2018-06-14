#!/bin/bash
composer dump-autoload
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
