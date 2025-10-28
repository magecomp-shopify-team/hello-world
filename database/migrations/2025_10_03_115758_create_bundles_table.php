<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->json('main_product_id'); // plain string GID
            $table->string('main_product_title')->nullable();
            $table->json('bundle_product_ids'); // JSON array of product objects
            $table->decimal('bundle_price', 10, 2)->nullable(); // numeric price
            $table->string('session_id');





            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundles');
    }
};
