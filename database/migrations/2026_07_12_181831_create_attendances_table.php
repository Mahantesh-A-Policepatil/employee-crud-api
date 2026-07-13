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
        Schema::create('attendances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('attendance_year');

            $table->unsignedTinyInteger('attendance_month');

            $table->unsignedTinyInteger('working_days');

            $table->unsignedTinyInteger('present_days');

            $table->unsignedTinyInteger('leave_days')
                ->default(0);

            $table->unsignedTinyInteger('lop_days')
                ->default(0);

            $table->text('remarks')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'employee_id',
                'attendance_year',
                'attendance_month'
            ], 'attendance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
