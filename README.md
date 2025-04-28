## MTAA backend

databazu som vyrobil takto 


docker run --name postgres -e POSTGRES_USER=laravel_user -e POSTGRES_PASSWORD=secret -e POSTGRES_DB=laravel_db -p 5433:5432 -d postgres



# potom laravel sa spusta takto: 

do .env daj veci co som ti poslal na dc

chod do directory kde si to clonol z githubu a daj toto
```
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve --host=0.0.0.0 --port=8002
```
