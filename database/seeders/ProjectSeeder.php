<?php

namespace Database\Seeders;

// # CI IMPORTIAMO IL MODELLO PROJECT
use App\Models\Project;
// # CI IMPORTIAMO IL MODELLO TYPE
use App\Models\Type;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// # CI IMPORTIAMO IL MODELLO Str CHE CONTIENI METODI DI SUPPORTO PER LE STRINGHE
use Illuminate\Support\Str;

// # CI IMPORTIAMO FAKER
use Faker\Generator as Faker;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * * @return void
     */
    public function run(Faker $faker)
    {
        // # CI FACCIAMO UN VARIABILE CHE PRENDE TUTTI GLI OGGETTI TYPE E CON IL pluck('id)
        // # ANDIAMO A PRENDERE TUTTI GLI ID NEL TYPE QUINDI SI CREERA' UN ARRAY DI ID
        $type_ids = Type::all()->pluck('id');
        // # SICCOME VOGLIAMO ANCHE DEI VALORI NULL ANDIAMO AD AGGIUNGERE ALL'ARRAY DI ID
        // # ANCHE UN VALORE NULL [1,2,3,..,null]
        $type_ids[] = null;

        for ($i = 0; $i < 50; $i++) {
            $project = new Project();
            // # CON IL METODO randomElement($type_ids) di FAKER CI ANDIAMO A PRENDERE DEGLI ID 
            // # CASUALI DALL'ARRAY DI ID
            $project->type_id = $faker->randomElement($type_ids);
            $project->name = $faker->catchPhrase();
            $project->description = $faker->text();
            $project->link = $faker->url();

            // # Str::slug() RENDE SLUG QUELLO TRA PARENTESI
            // # LO SLUG E QUALCOSA CHE PUO ANDARE NELL'URL TIPO 
            // # cerco casa rossa DIVENTERA' cerco-casa-rossa
            // # IL METODO DI Str VA IMPORTATO
            $project->slug = Str::slug($project->name);

            //! NON CI DIMENTICHIAMO DI SALVARE!
            $project->save();
        }
    }
}
