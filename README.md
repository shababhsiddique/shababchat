# shababchat
Sample Chat Application with laravel

1) Install Composer

        composer install

   this will create 'vendor' folder and download all packages, NOTE: you need composer installed

2) Create and edit .env file (use .env.example as base)
   for windows users use powershell to 

        cp .env.example .env

   to create a copy

3) Generate Key

        php artisan key:generate
    
   will create the unique application key for the .env file

For Database:

4) Create database . eg. shababchat

5) run migration

        php artisan migrate

        or

        php artisan migrate:fresh 

   if you already have database and want to overwrite)


