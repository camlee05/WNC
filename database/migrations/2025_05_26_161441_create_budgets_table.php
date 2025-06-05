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
            $table->string('month_year'); // lưu tháng/năm dạng 'YYYY-MM'
            $table->unsignedBigInteger('category_id'); // liên kết với bảng categories
            $table->integer('target_amount'); // số tiền mục tiêu
            $table->string('note')->nullable(); // ghi chú
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            // Giới hạn mỗi tháng/năm + category chỉ có 1 mục tiêu
            $table->unique(['month_year', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('budgets');
    }
}
