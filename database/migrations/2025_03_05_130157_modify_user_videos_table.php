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
        Schema::table('user_videos', function (Blueprint $table) {
            if (Schema::hasColumn('user_videos', 'file_path')) {
                $table->dropColumn('file_path');
            }
            $table->unsignedBigInteger('media_id')->nullable();
            $table->foreign('media_id')->references('id')->on('media')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_videos', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            $table->dropForeign(['media_id']);
            $table->dropColumn('media_id');
        });
    }
};
