<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration
{
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ✅ Thêm dòng này
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // cũng dùng foreignId
            $table->string('month_year'); // lưu tháng/năm dạng 'YYYY-MM'
            $table->integer('target_amount'); // số tiền mục tiêu
            $table->string('note')->nullable(); // ghi chú
            $table->timestamps();

            // Giới hạn mỗi user + month_year + category chỉ có 1 mục tiêu
            $table->unique(['user_id', 'month_year', 'category_id']); // ✅ thêm user_id vô unique luôn
        });
    }

    public function down()
    {
        Schema::dropIfExists('budgets');
    }
}
