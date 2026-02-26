<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        if (Capsule::schema()->hasColumn('wi_calc_uploads', 'file_path')) {
            return;
        }

        Capsule::schema()->table('wi_calc_uploads', function (Blueprint $table): void {
            $table->string('file_path', 500)->nullable()->after('original_name');
        });
    }

    public function down(): void
    {
        Capsule::schema()->table('wi_calc_uploads', function (Blueprint $table): void {
            $table->dropColumn('file_path');
        });
    }
};
