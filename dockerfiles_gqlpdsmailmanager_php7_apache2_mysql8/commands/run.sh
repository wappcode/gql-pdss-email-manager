#!/bin/bash
# chown -R www-data:www-data /var/www/html/dev/data;
# chmod -R a-x /var/www/html/dev/data;
# chmod -R ug+w /var/www/html/dev/data;
chmod -R a+w /var/www/html/dev/data;
php /home/commands/init-database.php;
rm /var/www/html/composer.lock;
composer install --no-interaction
vendor/bin/doctrine orm:schema-tool:update --force
php /home/commands/init-database-data.php;
apache2-foreground
