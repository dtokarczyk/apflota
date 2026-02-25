<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class {
    public function up(): void
    {
        if (Capsule::schema()->hasTable('wi_calc_uploads')) {
            return;
        }

        Capsule::schema()->create('wi_calc_uploads', function (Blueprint $table): void {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->integer('rows_imported');
            $table->integer('cars_affected');
            $table->string('status', 32);
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('wi_calc_uploads');
    }
};
