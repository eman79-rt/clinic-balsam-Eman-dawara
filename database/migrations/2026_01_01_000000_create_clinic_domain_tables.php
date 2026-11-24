<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('code', 3)->unique(); $t->string('city'); $t->timestamps(); });
        Schema::create('specialties', function (Blueprint $t) { $t->id(); $t->string('name')->unique(); $t->timestamps(); });
        Schema::table('users', function (Blueprint $t) { $t->string('role')->default('patient')->index(); $t->foreignId('branch_id')->nullable()->constrained()->nullOnDelete(); $t->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete(); });
        Schema::create('doctors', function (Blueprint $t) { $t->id(); $t->string('name'); $t->foreignId('specialty_id')->constrained(); $t->string('phone')->nullable(); $t->timestamps(); });
        Schema::create('doctor_branch_schedules', function (Blueprint $t) { $t->id(); $t->foreignId('doctor_id')->constrained()->cascadeOnDelete(); $t->foreignId('branch_id')->constrained()->cascadeOnDelete(); $t->unsignedTinyInteger('day_of_week'); $t->time('starts_at'); $t->time('ends_at'); $t->unique(['doctor_id','branch_id','day_of_week']); });
        Schema::create('patients', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('phone')->index(); $t->timestamp('last_visit_at')->nullable(); $t->timestamp('online_booking_blocked_until')->nullable()->index(); $t->timestamps(); });
        Schema::create('branch_year_sequences', function (Blueprint $t) { $t->id(); $t->foreignId('branch_id')->constrained()->cascadeOnDelete(); $t->unsignedSmallInteger('year'); $t->unsignedInteger('last_value')->default(0); $t->unique(['branch_id','year']); });
        Schema::create('appointments', function (Blueprint $t) { $t->id(); $t->string('reference')->unique()->nullable(); $t->foreignId('branch_id')->constrained(); $t->foreignId('doctor_id')->constrained(); $t->foreignId('patient_id')->constrained(); $t->dateTime('starts_at')->index(); $t->dateTime('ends_at'); $t->string('status')->default('booked')->index(); $t->timestamps(); $t->index(['branch_id','starts_at','status']); $t->index(['doctor_id','starts_at','status']); });
        Schema::create('medical_records', function (Blueprint $t) { $t->id(); $t->foreignId('appointment_id')->unique()->constrained()->cascadeOnDelete(); $t->foreignId('doctor_id')->constrained(); $t->foreignId('patient_id')->constrained(); $t->text('diagnosis')->nullable(); $t->text('prescription')->nullable(); $t->timestamps(); });
        Schema::create('audit_logs', function (Blueprint $t) { $t->id(); $t->morphs('auditable'); $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $t->string('event'); $t->json('old_values')->nullable(); $t->json('new_values')->nullable(); $t->timestamps(); });
        Schema::create('media', function (Blueprint $t) { $t->id(); $t->morphs('mediable'); $t->string('disk')->default('public'); $t->string('path'); $t->string('collection')->default('default'); $t->timestamps(); });
        Schema::create('appointment_reminders', function (Blueprint $t) { $t->id(); $t->foreignId('appointment_id')->constrained()->cascadeOnDelete(); $t->date('reminder_date'); $t->string('channel')->default('sms'); $t->timestamp('queued_at')->nullable(); $t->unique(['appointment_id','reminder_date','channel']); $t->timestamps(); });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_reminders'); Schema::dropIfExists('media'); Schema::dropIfExists('audit_logs'); Schema::dropIfExists('medical_records'); Schema::dropIfExists('appointments'); Schema::dropIfExists('branch_year_sequences'); Schema::dropIfExists('patients'); Schema::dropIfExists('doctor_branch_schedules'); Schema::dropIfExists('doctors'); Schema::dropIfExists('specialties'); Schema::table('users', function (Blueprint $t) { $t->dropConstrainedForeignId('branch_id'); $t->dropConstrainedForeignId('doctor_id'); $t->dropColumn('role'); }); Schema::dropIfExists('branches');
    }
};
