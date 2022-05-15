## ES Tech Group


## Starting dockers
./vendor/bin/sail up


## Import database
docker exec -u sail es_tech_dev4_laravel.test_1 php artisan db:seed --class=ImportTableSeeder

## Script  do Import Prices from CSV to Database
docker exec -u sail es_tech_dev4_laravel.test_1 php artisan import_csv_prices_to_db

## Get product price

http://localhost/get_product_price/QUBEQZ

http://localhost/get_product_price/QUBEQZ?account_id=133


## «Live price» feed

http://localhost/live_prices

