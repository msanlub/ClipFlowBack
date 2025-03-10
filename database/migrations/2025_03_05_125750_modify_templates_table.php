<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            if (Schema::hasColumn('templates', 'file_path')) {
                $table->dropColumn('file_path');
            }
            if (Schema::hasColumn('templates', 'audio_path')) {
                $table->dropColumn('audio_path');
            }
            if (Schema::hasColumn('templates', 'icon_path')) {
                $table->dropColumn('icon_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('icon_path')->nullable();
        });
    }
};
