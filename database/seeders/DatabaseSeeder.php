<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // # CHIAMIAMO UN METODO call() CHE CONTERRA' UN ARRAY CON TUTTI I SEEDER
        // # CHE VOGLIAMO CHIAMARE IN MANIERA TALE CHE SE FACCIAMO UN REFRESH O UN RESET
        // # BASTERA' FARE php artisan db:seed E TUTTI I SEEDER NELLA call() SI AVVIERANNO
        $this->call([
            TypeSeeder::class,
            ProjectSeeder::class,
            UserSeeder::class
        ]);
    }
}
