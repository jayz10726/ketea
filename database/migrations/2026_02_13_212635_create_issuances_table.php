<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('issuances', function (Blueprint $table) {
            $table->id();
            $table->string('issuance_code')->unique();
            $table->morphs('issuable'); // issuable_type + issuable_id (polymorphic)
            $table->foreignId('issued_to')->constrained('users');
            $table->foreignId('issued_by')->constrained('users');
            $table->integer('quantity')->default(1);
            $table->date('issue_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->enum('status', ['Issued', 'Returned', 'Overdue'])->default('Issued');
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['status', 'issue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issuances');
    }
};