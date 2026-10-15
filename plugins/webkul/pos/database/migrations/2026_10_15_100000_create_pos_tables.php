<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pos_registers')) {
            Schema::create('pos_registers', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->unsignedBigInteger('warehouse_id')->nullable()->index();
                $table->unsignedBigInteger('stock_location_id')->nullable()->index();
                $table->decimal('default_tax_rate', 5, 2)->default(11.00);
                $table->boolean('auto_deduct_stock')->default(true);
                $table->boolean('allow_out_of_stock_sale')->default(true);
                $table->string('receipt_paper_size', 10)->default('80mm');
                $table->text('receipt_header')->nullable();
                $table->text('receipt_footer')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pos_payment_methods')) {
            Schema::create('pos_payment_methods', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('type', 30)->default('cash');
                $table->string('account_number')->nullable();
                $table->integer('sort')->default(0);
                $table->boolean('is_active')->default(true);
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pos_sessions')) {
            Schema::create('pos_sessions', function (Blueprint $table): void {
                $table->id();
                $table->string('name')->unique();
                $table->foreignId('pos_register_id')->constrained('pos_registers')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status', 20)->default('open')->index();
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->decimal('opening_balance', 15, 2)->default(0);
                $table->decimal('cash_in_total', 15, 2)->default(0);
                $table->decimal('cash_out_total', 15, 2)->default(0);
                $table->decimal('cash_sales_total', 15, 2)->default(0);
                $table->decimal('non_cash_sales_total', 15, 2)->default(0);
                $table->decimal('expected_closing_cash', 15, 2)->default(0);
                $table->decimal('closing_cash_counted', 15, 2)->nullable();
                $table->decimal('cash_difference', 15, 2)->nullable();
                $table->text('opening_notes')->nullable();
                $table->text('closing_notes')->nullable();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pos_cash_movements')) {
            Schema::create('pos_cash_movements', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('pos_session_id')->constrained('pos_sessions')->cascadeOnDelete();
                $table->string('type', 20)->default('in');
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('reason');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pos_orders')) {
            Schema::create('pos_orders', function (Blueprint $table): void {
                $table->id();
                $table->string('name')->unique();
                $table->foreignId('pos_session_id')->constrained('pos_sessions')->cascadeOnDelete();
                $table->foreignId('pos_register_id')->constrained('pos_registers')->cascadeOnDelete();
                $table->unsignedBigInteger('partner_id')->nullable()->index();
                $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status', 20)->default('paid')->index();
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('paid_amount', 15, 2)->default(0);
                $table->decimal('change_amount', 15, 2)->default(0);
                $table->boolean('stock_deducted')->default(false);
                $table->text('note')->nullable();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pos_order_lines')) {
            Schema::create('pos_order_lines', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('pos_order_id')->constrained('pos_orders')->cascadeOnDelete();
                $table->unsignedBigInteger('product_id')->nullable()->index();
                $table->string('product_name');
                $table->string('barcode')->nullable();
                $table->string('sku')->nullable();
                $table->decimal('qty', 15, 2)->default(1);
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('line_total', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('pos_order_payments')) {
            Schema::create('pos_order_payments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('pos_order_id')->constrained('pos_orders')->cascadeOnDelete();
                $table->foreignId('pos_payment_method_id')->nullable()->constrained('pos_payment_methods')->nullOnDelete();
                $table->string('payment_method_name');
                $table->string('payment_type', 30)->default('cash');
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('reference')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_order_payments');
        Schema::dropIfExists('pos_order_lines');
        Schema::dropIfExists('pos_orders');
        Schema::dropIfExists('pos_cash_movements');
        Schema::dropIfExists('pos_sessions');
        Schema::dropIfExists('pos_payment_methods');
        Schema::dropIfExists('pos_registers');
    }
};
