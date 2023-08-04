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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->integer('term');
            $table->date('request_date');
            $table->enum('status', ['PENDING', 'APPROVED', 'PAID'])->default('PENDING');
            $table->unsignedBigInteger('customer_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
