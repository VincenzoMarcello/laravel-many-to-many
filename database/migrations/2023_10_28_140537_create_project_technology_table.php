<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_technology', function (Blueprint $table) {
            $table->id();
            // # CI TOGLIAMO IL TIMESTAMP CHE NON CI SERVE E METTIAMO LE DUE FOREIGN ID
            // # IN QUESTO CASO IL CASCADEONDELETE ELIMINA SOLO LA RELAZIONE CHE HA IL PROJECT CON LA TECNOLOGIA
            // # PERCHè SIAMO NELLA TABELLA PONTE QUINDI SE ELIMINIAMO UN PROJECT SI ELIMINERA' LA RELAZIONE
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_technology');
    }
};
