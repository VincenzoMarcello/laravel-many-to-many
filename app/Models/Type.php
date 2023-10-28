<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;


    // # QUI STIAMO FACENDO UN PUBLIC FUNCTION CHE DICE CHE UN TYPE HA MOLTI PROJECT
    // ! QUINDI METTIAMO PROJECTS AL PLURALE
    public function projects()
    {
        // # PER QUESTO INTENDIAMO TYPE E TRADOTTO 
        // # 'TYPE'->'HA MOLTI'('PROJECT')
        return $this->hasMany(Project::class);

        // # QUESTO METODO PROJECTS CI RESTITUIRA' UN ARRAY
    }
}
