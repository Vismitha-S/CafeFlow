<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Run the migrations.
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained('cafes')->cascadeOnDelete();
            $table->foreignId('cafe_table_id')->constrained('cafe_tables')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('guest_count');
            $table->string('status')->default('pending');
            $table->decimal('reservation_fee', 12, 2)->default(0);
            $table->decimal('cancellation_penalty_percentage', 5, 2)->default(0);
            $table->decimal('cancellation_penalty_amount', 12, 2)->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for availability querying, customer lookups, and reporting
            $table->index(['cafe_id', 'reservation_date']);
            $table->index(['cafe_table_id', 'reservation_date', 'status']);
            $table->index('user_id');
            $table->index('status');
        });
    }

    // Reverse the migrations.
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
