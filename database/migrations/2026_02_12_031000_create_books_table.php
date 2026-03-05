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
        Schema::create('books', function (Blueprint $table) {
            $table->id(); // integer, auto increment, primary key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title'); // judul buku
            $table->string('author'); // penulis
            $table->integer('year'); // tahun terbit
            $table->integer('stock')->default(1); // jumlah buku tersedia
            $table->text('description')->nullable(); // keterangan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
