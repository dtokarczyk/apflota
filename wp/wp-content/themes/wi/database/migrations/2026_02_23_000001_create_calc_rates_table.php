<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        if (Capsule::schema()->hasTable('wi_calc_rates')) {
            return;
        }

        Capsule::schema()->create('wi_calc_rates', function (Blueprint $table): void {
            $table->id();
            $table->string('car_id', 32)->index();
            $table->string('idv', 64);
            $table->integer('month');
            $table->integer('km');
            $table->integer('percent');
            $table->integer('fee');
            $table->integer('rate');
            $table->timestamps();
            $table->index(['car_id', 'month', 'km', 'percent']);
        });
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('wi_calc_rates');
    }
};
