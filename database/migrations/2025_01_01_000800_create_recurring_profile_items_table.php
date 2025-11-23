<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_profile_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurring_profile_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('rate', 12, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_profile_items');
    }
};
