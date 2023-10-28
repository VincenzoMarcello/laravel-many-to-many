<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    use HasFactory;


    public function projects()
    {
        // # QUI STIAMO DICENDO CHE A QUESTE (TECNOLOGIE) APPARTENGONO A MOLTE PROGETTI
        return $this->belongsToMany(Project::class);
    }
}
