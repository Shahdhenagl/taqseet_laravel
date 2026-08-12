<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $legacyTables = [
            'customer',
            'product',
            'invoice',
            'invoice_item',
            'installment_plan',
            'installment',
            'postponement_request',
            'notification'
        ];

        foreach ($legacyTables as $table) {
            Schema::dropIfExists($table);
        }
    }

    public function down(): void
    {
        // Legacy table cleanup migration
    }
};
