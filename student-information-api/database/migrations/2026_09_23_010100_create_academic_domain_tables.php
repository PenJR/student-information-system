<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code')->unique();
            $table->string('course_title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('units');
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->string('academic_year', 9);
            $table->string('term');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
            $table->unique(['academic_year', 'term']);
            $table->index('status');
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('year_level');
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
            $table->index(['program_id', 'year_level', 'status']);
            $table->index(['last_name', 'first_name']);
        });

        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_term_id')->constrained()->restrictOnDelete();
            $table->foreignId('instructor_id')->constrained('users')->restrictOnDelete();
            $table->string('section');
            $table->string('schedule')->nullable();
            $table->string('room')->nullable();
            $table->unsignedSmallInteger('capacity');
            $table->string('status')->default('ACTIVE');
            $table->timestamps();
            $table->unique(['course_id', 'academic_term_id', 'section']);
            $table->index(['academic_term_id', 'instructor_id', 'status']);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_offering_id')->constrained()->restrictOnDelete();
            $table->date('enrollment_date');
            $table->string('status')->default('ENROLLED');
            $table->timestamps();
            $table->unique(['student_id', 'course_offering_id']);
            $table->index(['student_id', 'status']);
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('midterm_grade', 5, 2)->nullable();
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
            $table->index(['final_grade', 'midterm_grade']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('course_offerings');
        Schema::dropIfExists('students');
        Schema::dropIfExists('academic_terms');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('programs');
    }
};