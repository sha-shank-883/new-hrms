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
        // Remove tenant_id from users table
        if (Schema::hasColumn('users', 'tenant_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from roles table
        if (Schema::hasColumn('roles', 'tenant_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from permissions table
        if (Schema::hasColumn('permissions', 'tenant_id')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from departments table
        if (Schema::hasColumn('departments', 'tenant_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from employees table
        if (Schema::hasColumn('employees', 'tenant_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from leave_types table
        if (Schema::hasColumn('leave_types', 'tenant_id')) {
            Schema::table('leave_types', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from leave_requests table
        if (Schema::hasColumn('leave_requests', 'tenant_id')) {
            Schema::table('leave_requests', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from attendances table
        if (Schema::hasColumn('attendances', 'tenant_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from payrolls table
        if (Schema::hasColumn('payrolls', 'tenant_id')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from tasks table
        if (Schema::hasColumn('tasks', 'tenant_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from documents table
        if (Schema::hasColumn('documents', 'tenant_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }

        // Remove tenant_id from task_comments table
        if (Schema::hasColumn('task_comments', 'tenant_id')) {
            Schema::table('task_comments', function (Blueprint $table) {
                $table->dropColumn('tenant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add tenant_id back to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to roles table
        Schema::table('roles', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to permissions table
        Schema::table('permissions', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to departments table
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to leave_types table
        Schema::table('leave_types', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to leave_requests table
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to payrolls table
        Schema::table('payrolls', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to tasks table
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });

        // Add tenant_id back to task_comments table
        Schema::table('task_comments', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
        });
    }
};
