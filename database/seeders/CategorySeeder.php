<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = ['Ăn uống', 'Giao thông', 'Học tập', 'Nhà ở', 'Giải trí', 'Sức khỏe', 'Mua sắm', 'Điện thoại', 'Thú cưng', 'Du lịch', 'Xã hội', 'Khác'];

        $users = User::all();

        $users = User::all();
    foreach ($users as $user) {
        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['name' => $name, 'user_id' => $user->id],
                []
            );
        }
    }
    }
}

