<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'banner')) {
                $table->string('banner')->nullable()->after('short_description');
            }
            if (! Schema::hasColumn('projects', 'description')) {
                $table->longText('description')->nullable()->after('banner');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $columns = array_filter(
                ['description', 'banner'],
                fn ($c) => Schema::hasColumn('projects', $c)
            );

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
