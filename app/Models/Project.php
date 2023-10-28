<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "link",
        "description",
        "type_id"
    ];

    // # QUI STIAMO FACENDO UN PUBLIC FUNCTION CHE DICE CHE UN PROJECT HA UN SOLO TYPE
    // ! QUINDI METTIAMO TYPE AL SINGOLARE
    public function type()
    {
        // # PER QUESTO INTENDIAMO PROJECT E TRADOTTO 
        // # 'PROJECT'->'APPARTIENE A'('TYPE')
        return $this->belongsTo(Type::class);

        // # QUINDI ORA DA PROJECT POSSIAMO ACCEDERE A TYPE
    }

    // # FACCIAMO UN METODO GETTER PER FARE DEI BADGE CON LA LABEL DEL TYPE 
    // # E UN UNTYPE PER I PROJECT SENZA TYPE QUINDI NULL
    public function getTypeBadge()
    {
        // # FACCIAMO UN TERNARIO CHE DICE CHE SE CI STA UN TIPO ALLORA STAMPIAMO UN BADGE
        // # CON IL COLORE PRESO DA TYPE E LA LABEL PRESA DA TYPE
        // # SE IL TYPE é NULL ALLORA STAMPIAMO UNTYPE
        // return $this->type ? "<span class='badge' style='background-color:{$this->type->color}'>{$this->type->label}</span>" : 'Untype';

        // # PERSONALIZZIAMO ANCHE L'UNTYPE
        return $this->type ? "<span class='badge' style='background-color:{$this->type->color}'>{$this->type->label}</span>" : "<span class='badge text-bg-danger'>Untype</span>";
    }
}
