<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ImportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db = [
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'host' => env('DB_HOST'),
            'database' => env('DB_DATABASE')
        ];

        $procedures_path = 'es_resources/procedures.sql';
        exec("mysql --user={$db['username']} --password={$db['password']} --host={$db['host']} --database {$db['database']} < $procedures_path");

        $db_path = 'es_resources/db.sql';
        exec("mysql --user={$db['username']} --password={$db['password']} --host={$db['host']} --database {$db['database']} < $db_path");

        \Log::info('SQL Import Done');
    }
}
