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
        Schema::create('m_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->longText('description');
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedTinyInteger('del_flg')->default(1);
            $table->unsignedBigInteger('user_id');
            $table->longText('tech_detail')->nullable();
            $table->longText('accessory_detail')->nullable();
            $table->longText('video')->nullable();
            $table->string('catalogue')->nullable();
            $table->string('voltage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_products');
    }
};
