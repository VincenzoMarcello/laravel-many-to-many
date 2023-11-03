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
            // # CI ANDIAMO A CREARE UNA NUOVA COLONNA NEL DB NELLA TABELLA PROJECTS
            // # IN MANIERA TALE DA POTER SALVARE IL PATH DELL'IMMAGINE PER QUESTO SARA' UNA STRINGA
            // # METTIAMO NULLABLE IN QUANTO UN PROJECT POTREBBE ANCHE NON AVERE UN'IMMAGINE
            // # E L'AFTER PER DECIDERE DOVE VERRA' POSIZIONATA LA COLONNA IN QUESTO CASO DOPO NAME
            $table->string('cover_image')->nullable()->after('name');
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
            // # QUI METTIAMO IL DROP DELLA COLONNA IN MANIERA TALE CHE SE FACCIAMO UN RESET
            // # O ALTRO, SI CANCELLERA' SENZA DARE ERRORE
            $table->dropColumn('cover_image');
        });
    }
};
