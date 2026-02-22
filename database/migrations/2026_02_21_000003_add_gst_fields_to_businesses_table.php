<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('gstin')->nullable()->after('gst_number');
            $table->string('pan')->nullable()->after('gstin');
            $table->string('default_tax_type')->default('none')->after('pan'); // none, gst, vat
            $table->decimal('default_gst_rate', 5, 2)->default(18)->after('default_tax_type');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['gstin', 'pan', 'default_tax_type', 'default_gst_rate']);
        });
    }
};
