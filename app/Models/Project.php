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

    public function type()
    {
        return $this->belongsTo(Type::class);
    }


    public function technologies()
    {
        // # QUI STIAMO DICENDO CHE QUESTI (PROGETTI) APPARTENGONO A MOLTE TECNOLOGIE
        return $this->belongsToMany(Technology::class);
    }

    public function getTypeBadge()
    {
        return $this->type ? "<span class='badge' style='background-color:{$this->type->color}'>{$this->type->label}</span>" : "<span class='badge text-bg-danger'>Untype</span>";
    }
}
