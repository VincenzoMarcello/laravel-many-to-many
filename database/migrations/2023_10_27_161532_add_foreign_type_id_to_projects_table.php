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
        Schema::table('projects', function (Blueprint $table) {
            // # QUI CREIAMO LA COLONNA E LA RELAZIONE DELLA CHIAVE SECONDARIA
            // # METTIAMO ANCHE DEI METODI PER PERSONALIZZARLA (VEDERE README)
            $table->foreignId('type_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            // # DROP RELAZIONE
            $table->dropForeign('projects_type_id_foreign');
            // # DROP COLONNA
            $table->dropColumn('type_id');
        });
    }
};
