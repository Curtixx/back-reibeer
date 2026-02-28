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
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'title']);
            $table->string('type')->nullable();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['type', 'product_id']);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
        });
    }
};
