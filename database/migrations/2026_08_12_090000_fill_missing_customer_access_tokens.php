<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $customers = DB::table('customers')->whereNull('access_token')->orWhere('access_token', '')->get();

        foreach ($customers as $customer) {
            DB::table('customers')
                ->where('id', $customer->id)
                ->update(['access_token' => Str::random(32)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
