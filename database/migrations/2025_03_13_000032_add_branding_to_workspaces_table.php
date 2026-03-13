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
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('preferences');
            $table->string('icon_path')->nullable()->after('logo_path');
            $table->string('accent_color', 20)->nullable()->after('icon_path');
            $table->text('short_description')->nullable()->after('accent_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'icon_path', 'accent_color', 'short_description']);
        });
    }
};
