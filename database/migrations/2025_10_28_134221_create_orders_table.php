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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
              // Thông tin khách hàng
            $table->string('fullname');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->text('note')->nullable();

            $table->integer('subtotal')->default(0); // tổng tiền hàng
            $table->integer('discount')->default(0); // tổng giảm giá
            $table->integer('shipping_fee')->default(0); // phí vận chuyển
            $table->integer('total')->default(0); // tổng thanh toán cuối

            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable()->default('COD'); // COD, Momo, Bank...
            $table->string('payment_status')->default('unpaid'); // unpaid / paid


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
