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
        if (Schema::hasTable('payments')) {
            $this->addRefundSupportColumns();

            return;
        }

        $this->createPaymentsTable();
    }

    private function createPaymentsTable(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('refund_of_payment_id')->nullable()->constrained('payments')->nullOnDelete()->unique();
            $table->string('type');
            $table->string('status')->default('pending');
            $table->decimal('amount', 12, 2);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['reservation_id', 'type', 'status']);
            $table->index('user_id');
        });
    }

    private function addRefundSupportColumns(): void
    {
        $existingColumns = Schema::getColumnListing('payments');

        Schema::table('payments', function (Blueprint $table) use ($existingColumns): void {
            if (! in_array('refund_of_payment_id', $existingColumns, true)) {
                $table->foreignId('refund_of_payment_id')
                    ->nullable()
                    ->constrained('payments')
                    ->nullOnDelete()
                    ->unique();
            }

            if (! in_array('failure_reason', $existingColumns, true)) {
                $table->text('failure_reason')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('payments')) {
            return;
        }

        $existingColumns = Schema::getColumnListing('payments');
        $legacyColumns = [
            'currency',
            'provider',
            'provider_reference',
            'refunded_at',
            'refund_amount',
            'metadata',
        ];
        $hasPreexistingPaymentSchema = count(array_intersect($legacyColumns, $existingColumns)) > 0;

        if (! $hasPreexistingPaymentSchema) {
            Schema::drop('payments');

            return;
        }

        $this->removeRefundSupportColumns($existingColumns);
    }

    private function removeRefundSupportColumns(array $existingColumns): void
    {
        Schema::table('payments', function (Blueprint $table) use ($existingColumns): void {
            if (in_array('refund_of_payment_id', $existingColumns, true)) {
                $table->dropForeign(['refund_of_payment_id']);
                $table->dropUnique(['refund_of_payment_id']);
                $table->dropColumn('refund_of_payment_id');
            }

            if (in_array('failure_reason', $existingColumns, true)) {
                $table->dropColumn('failure_reason');
            }
        });
    }
};
