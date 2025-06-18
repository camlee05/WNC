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
```
<?php

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
```
**Expense**
```
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';
    protected $fillable = ['category_id', 'spend_date', 'amount', 'note', 'user_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```
**Budget**
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
}
```
### Controller
**ExpenseController
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    // Trang hiển thị form thêm/sửa chi tiêu
    public function create(Request $request)
    {
        $categories = Category::all(); // hoặc lọc theo user nếu cần

        $iconMap = [
            'Ăn uống'     => '<img src="' . asset('img/burger.png') . '" alt="Ăn uống" style="width:55px;">',
            'Du lịch'     => '<img src="' . asset('img/travel-bag.png') . '" alt="Du lịch" style="width:55px;">',
            'Mua sắm'     => '<img src="' . asset('img/shopping-cart.png') . '" alt="Mua sắm" style="width:55px;">',
            'Học tập'     => '<img src="' . asset('img/school-material.png') . '" alt="Học tập" style="width:55px;">',
            'Giao thông'  => '<img src="' . asset('img/vehicles.png') . '" alt="Giao thông" style="width:55px;">',
            'Sức khỏe'    => '<img src="' . asset('img/medicine.png') . '" alt="Sức khỏe" style="width:55px;">',
            'Điện thoại'  => '<img src="' . asset('img/app.png') . '" alt="Điện thoại" style="width:55px;">',
            'Thú cưng'    => '<img src="' . asset('img/pet-food.png') . '" alt="Thú cưng" style="width:55px;">',
            'Xã hội'      => '<img src="' . asset('img/ancestors.png') . '" alt="Xã hội" style="width:55px;">',
            'Khác'        => '<img src="' . asset('img/question-mark.png') . '" alt="Khác" style="width:55px;">',
            'Nhà ở'       => '<img src="' . asset('img/house.png') . '" alt="Nhà ở" style="width:55px;">',
            'Giải trí'    => '<img src="' . asset('img/online-gaming.png') . '" alt="Giải trí" style="width:55px;">',
        ];

        foreach ($categories as $category) {
        $category->icon = $iconMap[$category->name] ?? $iconMap['Khác'];
        }

        $editExpense = null;
        if ($request->has('edit_id')) {
            $editExpense = Expense::where('id', $request->edit_id)
                                  ->where('user_id', Auth::id())
                                  ->firstOrFail();
        }

        return view('page.expenses.create', compact('categories', 'editExpense'));
    }

    // Lưu khoản chi tiêu mới
    public function store(Request $request)
    {
        $request->validate([
            'spend_date' => 'required|date',
            'amount' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'note' => 'nullable|string|max:255',
        ]);

        Expense::create([
            'spend_date' => $request->spend_date,
            'amount' => $request->amount,
            'category_id' => $request->category_id,
            'note' => $request->note,
            'user_id' => Auth::id(), // BẮT BUỘC
        ]);

        return redirect()->route('page.expenses.create')->with('success', 'Thêm khoản chi thành công!');
    }

    // Cập nhật khoản chi tiêu
    public function update(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'spend_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
        ]);

        $spending = Expense::where('id', $id)
                           ->where('user_id', Auth::id())
                           ->firstOrFail();

        $spending->update([
            'note' => $request->note,
            'amount' => $request->amount,
            'spend_date' => $request->spend_date,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('page.expenses.create')->with('success', 'Chi tiêu đã được cập nhật.');
    }
}
```
