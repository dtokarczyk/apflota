<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        Capsule::schema()->table('wi_calc_rates', function (Blueprint $table): void {
            if (! Capsule::schema()->hasColumn('wi_calc_rates', 'discount_rate')) {
                $table->integer('discount_rate')->nullable()->after('rate');
            }
            if (! Capsule::schema()->hasColumn('wi_calc_rates', 'lowest_price_30_days')) {
                $table->integer('lowest_price_30_days')->nullable()->after('discount_rate');
            }
        });
    }

    public function down(): void
    {
        Capsule::schema()->table('wi_calc_rates', function (Blueprint $table): void {
            $table->dropColumn(['discount_rate', 'lowest_price_30_days']);
        });
    }
};
