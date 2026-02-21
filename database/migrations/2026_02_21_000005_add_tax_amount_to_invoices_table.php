<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_total');
            }
            if (! Schema::hasColumn('invoices', 'duration_text')) {
                $table->string('duration_text')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('invoices', 'tax_amount')) {
                $cols[] = 'tax_amount';
            }
            if (Schema::hasColumn('invoices', 'duration_text')) {
                $cols[] = 'duration_text';
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
