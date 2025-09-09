<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTenantColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop tenant_id columns from tables
        $tables = [
            'users',
            'employees',
            'attendances',
            'leave_requests',
            'leave_balances',
            'payrolls',
            'leave_types',
            'departments',
            'documents',
            'notifications',
            'settings',
            'holidays'
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This is a one-way migration as we're removing tenant functionality
    }
}
