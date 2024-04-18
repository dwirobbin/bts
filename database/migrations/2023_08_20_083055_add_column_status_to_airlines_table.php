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
        Schema::table('speed_boats', function (Blueprint $table) {
            $table->enum('status', ['Ready', 'Dalam Perjalanan Berangkat', 'Dalam Perjalanan Kembali Pulang'])->nullable()->after('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('speed_boats', function (Blueprint $table) {
            $table->removeColumn('status');
        });
    }
};
