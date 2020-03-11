# Api Astrolabe

Installation du projet :
```
git clone https://github.com/vgimonnet/astrolabe-machine-api.git
composer update

php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate

php bin/console doctrine:schema:update --force

```
