<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });

        DB::statement('ALTER TABLE employees MODIFY department_id BIGINT UNSIGNED NULL');

        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });

        DB::statement(
            'UPDATE employees SET department_id = (SELECT id FROM departments ORDER BY id LIMIT 1) WHERE department_id IS NULL'
        );
        DB::statement('ALTER TABLE employees MODIFY department_id BIGINT UNSIGNED NOT NULL');

        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->cascadeOnDelete();
        });
    }
};
