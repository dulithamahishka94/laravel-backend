1. Clone this repository  
2. Go to the cloned folder and run `composer install`  
3. Create a copy of the .env.example file as .env  
4. Create a new database and update database credentials in .env file  
5. Run `php artisan migrate:fresh --seed`  
6. Run `php artisan passport:install`
7. Run `php artisan key:generate`  
8. Run `php artisan serve`  
9. Done