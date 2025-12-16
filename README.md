## Tentang Aplikasi

Sistem informasi koperasi simpan pinjam berbasis web dengan framework laravel 9, diintegrasikan dengan template stisla credit buat [nauvalazhar](https://github.com/nauvalazhar), dengan frontend bootstrap serta livewire.js.

## How to Install

-   clone the repo,
-   import the db from miniksp.sql to ur database, php artisan migrate not make all the view (bcz im too lazy to redesign
    all of them). Hint: but u can use email & pass from the user seeder table file, 2 access the program. :P
-   rename or copy .env.example to .env , edit the file 2 add database name exp: DB_DATABASE=miniksp
-   specify your app url exp: APP_URL=http://localhost/koperasi-simpan-pinjam/public
-   composer update
-   php artisan key:generate
-   php artisan storage:link
-   Run
