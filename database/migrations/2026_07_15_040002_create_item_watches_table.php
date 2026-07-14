<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_watches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_user_id')->constrained()->onDelete('cascade');
            $table->string('item_code');
            $table->string('item_name');
            // 商品が検索結果から消えた場合の再検索用に商品名をキーワードとして保持する
            $table->unsignedInteger('last_known_price')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->unique(['line_user_id', 'item_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_watches');
    }
};
