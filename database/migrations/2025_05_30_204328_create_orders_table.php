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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
                
                // Order type: pickup or delivery
            $table->enum('type', ['pickup', 'delivery'])->default('delivery');
            
            // Delivery address only required for delivery
            $table->text('delivery_address')->nullable();

            // Cost-related fields
            $table->decimal('delivery_cost', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            
            // Order status tracking
            $table->enum('status', ['pending', 'accepted', 'preparing', 'delivering', 'delivered'])->default('pending');

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
