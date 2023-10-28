<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// # CI IMPORTIAMO IL MODELLO TYPE
use App\Models\Type;

// # CI IMPORTIAMO FAKER
use Faker\Generator as Faker;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        // # CI CREIAMO UNA VARIABILE CHE CONTIENE UN ARRAY DI TYPE
        $_types = [
            "Front-end",
            "Back-end",
            "Full Stack"
        ];

        // # FACCIAMO UN CICLO E PER OGNI ELEMENTO DEL DB:
        foreach ($_types as $_type) {
            // # ABBIAMO UN OGGETTO TYPE
            $type = new Type();
            // # CHE CONTERRA' UNA LABEL CHE CONTIENE L'ARRAY DI TYPE
            $type->label = $_type;
            // # E UN COLORE GENERATO CASUALMENTE IN ESADECIMALE DA FAKER
            $type->color = $faker->hexColor();
            // # SALVIAMO NEL DB
            $type->save();
        }
    }
}