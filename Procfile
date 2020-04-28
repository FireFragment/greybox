web: vendor/bin/heroku-php-apache2 public/
web: php -S 0.0.0.0:$PORT -t public
worker: php artisan queue:restart && php artisan queue:work