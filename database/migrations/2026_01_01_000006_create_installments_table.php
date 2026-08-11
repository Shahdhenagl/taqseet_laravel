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
        Schema::create('installments', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->double('amount', 10, 2);
            $table->date('due_date');
            $table->boolean('is_paid')->default(false);
            $table->dateTime('paid_date')->nullable();
            $table->string('plan_id');
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('installment_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
