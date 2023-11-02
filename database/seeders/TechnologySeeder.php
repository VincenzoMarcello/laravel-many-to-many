<?php

namespace Database\Seeders;

// # IMPORTIAMO IL MODELLO TECHNOLOGY
use App\Models\Technology;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// # CI IMPORTIAMO FAKER
use Faker\Generator as Faker;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        // # FACCIAMO UN ARRAY DI TECNOLOGIE METTO "_" PER DIFFERENZIARLA DALLA VARIABILE SOTTO
        // # CHE CONTERRA' TECHNOLOGY()
        $_technologies = ["html", "css", "js", "sass", "vue", "php", "mysql", "laravel"];

        // # QUI FACCIAMO IL CICLO PER POPOLARE OGNI ELEMENTO DEL DB
        foreach ($_technologies as $_technology) {
            $technology = new Technology();
            // # QUI METTIAMO L'ARRAY DI TECNOLOGIE
            $technology->label = $_technology;
            // # QUI GENERIAMO UN COLORE CASUALE IN ESADECIAMALE
            $technology->color = $faker->hexColor();
            $technology->save();
        }
    }
}
