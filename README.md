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
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function expenses()
    {
    return $this->hasMany(Expense::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

}
```
**Category**
'''<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'user_id'];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}

