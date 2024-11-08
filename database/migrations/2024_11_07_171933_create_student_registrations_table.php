<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();

            // Personal Identification
            $table->string('nik', 16)->unique()->index();
            $table->string('family_card_number', 16);
            $table->string('nisn', 10)->unique()->index();
            $table->string('full_name')->index();
            $table->enum('gender', ['male', 'female']);
            $table->string('birth_place');
            $table->date('birth_date');
            $table->enum('religion', ['islam', 'protestant', 'catholic', 'hindu', 'buddha', 'confucian']);

            // Family Information
            $table->unsignedTinyInteger('child_order');
            $table->unsignedTinyInteger('siblings_count');
            $table->enum('child_status', ['biological', 'step', 'adopted']);

            // Physical Information
            $table->unsignedSmallInteger('height');
            $table->unsignedSmallInteger('weight');
            $table->enum('blood_type', ['a', 'b', 'ab', 'o'])->nullable();

            // Address Information
            $table->text('address');
            $table->string('village');
            $table->string('district');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code', 5);
            $table->string('email')->unique();

            // Additional Information
            $table->enum('uniform_size', ['s', 'm', 'l', 'xl', 'xxl', 'xxxl', 'jumbo']);
            $table->enum('residence_status', ['owned', 'rented']);
            $table->enum('transportation', ['walking', 'motorcycle', 'car', 'public_transportation']);

            // Previous Education
            $table->enum('previous_school_type', ['smpn', 'smpit', 'smp', 'mtsn', 'mts', 'pkbm', 'ponpes']);
            $table->text('previous_school_address');
            $table->string('diploma_number')->nullable();
            $table->date('diploma_date')->nullable();
            $table->year('graduation_year');

            // Contact Information
            $table->string('student_phone', 15);

            // Father Information
            $table->string('father_name');
            $table->string('father_nik', 16);
            $table->string('father_occupation');
            $table->decimal('father_income', 12, 2);

            // Mother Information
            $table->string('mother_name');
            $table->string('mother_nik', 16);
            $table->string('mother_occupation');
            $table->decimal('mother_income', 12, 2);

            // Parents Contact
            $table->text('parents_address');
            $table->string('parents_phone', 15);

            // Guardian Information (Optional)
            $table->string('guardian_name')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->decimal('guardian_income', 12, 2)->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('guardian_phone', 15)->nullable();

            // Social Support
            $table->string('kks_number')->nullable()->comment('Kartu Keluarga Sejahtera Number');
            $table->string('kip_number')->nullable()->comment('Kartu Indonesia Pintar Number');

            // Registration Information
            $table->enum('reference_source', ['friend', 'teacher', 'alumni', 'neighbor', 'personal']);
            $table->string('selected_major')->index();

            // Status and Timestamps
            $table->enum('registration_status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->index();
            $table->timestamps();
            $table->softDeletes();

            // Instead of composite index, create individual indexes
            $table->index('registration_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_registrations');
    }
};
