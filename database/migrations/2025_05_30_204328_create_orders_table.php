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
            $table->string('name')->nullable(); // Guest name (optional)
            $table->string('phone')->nullable(); // Contact info
            $table->string('email')->nullable(); // Optional
            $table->text('delivery_address')->nullable();

            $table->enum('status', ['pending', 'accepted', 'preparing', 'delivering', 'delivered'])->default('pending');

            $table->decimal('total', 10, 2)->default(0.00);
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
