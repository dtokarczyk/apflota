<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        Capsule::schema()->table('wi_calc_rates', function (Blueprint $table): void {
            if (Capsule::schema()->hasColumn('wi_calc_rates', 'original_rate')) {
                $table->renameColumn('original_rate', 'discount_rate');
            } elseif (! Capsule::schema()->hasColumn('wi_calc_rates', 'discount_rate')) {
                $table->integer('discount_rate')->nullable()->after('rate');
            }
        });
    }

    public function down(): void
    {
        Capsule::schema()->table('wi_calc_rates', function (Blueprint $table): void {
            if (Capsule::schema()->hasColumn('wi_calc_rates', 'discount_rate')) {
                $table->renameColumn('discount_rate', 'original_rate');
            }
        });
    }
};
