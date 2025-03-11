## MTAA backend

databazu som vyrobil takto 


docker run --name postgres -e POSTGRES_USER=laravel_user -e POSTGRES_PASSWORD=secret -e POSTGRES_DB=laravel_db -p 5432:5432 -d postgres



# potom laravel sa spusta takto: 


chod do directory kde si to clonol z githubu a daj toto
```
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```
