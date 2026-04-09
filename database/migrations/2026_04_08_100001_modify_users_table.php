<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('npm', 20)->nullable()->unique()->after('name');
            $table->enum('role', ['admin', 'mahasiswa'])->default('mahasiswa')->after('npm');
            $table->string('device_id', 128)->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['npm', 'role', 'device_id']);
        });
    }
};
