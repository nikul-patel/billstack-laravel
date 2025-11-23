<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('owner_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('gst_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('pincode')->nullable();
            $table->string('logo')->nullable();
            $table->string('invoice_prefix')->default('INV-');
            $table->unsignedBigInteger('invoice_start_no')->default(1);
            $table->string('currency')->default('INR');
            $table->string('date_format')->default('d-m-Y');
            $table->string('timezone')->default('Asia/Kolkata');
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
