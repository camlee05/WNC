# 💰 Website Quản lý Chi tiêu Cá nhân

Ứng dụng web giúp người dùng theo dõi chi tiêu, đặt mục tiêu tài chính theo danh mục và thời gian, đồng thời hiển thị báo cáo chi tiết theo tháng và năm.
By Lê Thị Cẩm Ly - 23010411

## 🚀 Tính năng chính
- **Xem danh sách mục tiêu chi tiêu và các khoản chi tiêu trong tháng**
- **Thêm, sửa, xoá khoản chi tiêu**
- **Đặt mục tiêu chi tiêu theo tháng và danh mục**
- **Báo cáo chi tiêu theo tháng và năm**

## 🛠️ Công nghệ sử dụng

- **PHP (Laravel Framework)**
- **Laravel Breeze**
- **MySQL (Aiven Cloud)**
- **Blade Template** 
- **Tailwind CSS**

## Sơ đồ khối
![Sơ đồ khối FinancialMn](https://github.com/user-attachments/assets/0b3b9bb6-a648-4db3-bbad-fc829d9cf270)

## Sơ đồ chức năng
![Sơ đồ chức năng FinancialMn](https://github.com/user-attachments/assets/fefabb4f-e9c0-44ce-a189-ec2040328fbe)

## Một số Code chính minh hoạ
### Modal
**User**
```
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'month_year',
        'category_id',
        'target_amount',
        'note',
        'user_id',
        
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}```

