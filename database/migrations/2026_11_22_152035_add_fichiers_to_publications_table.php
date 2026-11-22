<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // La colonne "image" existe déjà depuis la semaine 4 : on n'ajoute que les deux autres.
        Schema::table('publications', function (Blueprint $table) {
            $table->string('fichier_media')->nullable()->after('image');
            $table->string('type_media')->nullable()->after('fichier_media');
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn(['fichier_media', 'type_media']);
        });
    }
};