docker exec -u sail es_tech_dev4_laravel.test_1 php artisan migrate

docker exec -u sail es_tech_dev4_laravel.test_1 php artisan db:seed --class=ImportTableSeeder


