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
## 🧩 Sơ đồ khối
![Sơ đồ khối FinancialMn](https://github.com/user-attachments/assets/0b3b9bb6-a648-4db3-bbad-fc829d9cf270)

## 📑 Sơ đồ chức năng
![Sơ đồ chức năng FinancialMn](https://github.com/user-attachments/assets/fefabb4f-e9c0-44ce-a189-ec2040328fbe)

## 🔀 Sơ đồ thuật toán
![sdtt](https://github.com/user-attachments/assets/83024868-5031-4f56-9301-c4ba0945a90b)

## 💻 Một số Code chính minh hoạ
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
**FinancialController**
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;

class FinancialController extends Controller
{
    private $icons = [
        'Ăn uống'    => 'img/burger.png',
        'Du lịch'    => 'img/travel-bag.png',
        'Mua sắm'    => 'img/shopping-cart.png',
        'Học tập'    => 'img/school-material.png',
        'Giao thông' => 'img/vehicles.png',
        'Sức khỏe'   => 'img/medicine.png',
        'Điện thoại' => 'img/app.png',
        'Thú cưng'   => 'img/pet-food.png',
        'Xã hội'     => 'img/ancestors.png',
        'Khác'       => 'img/question-mark.png',
        'Nhà ở'      => 'img/house.png',
        'Giải trí'   => 'img/online-gaming.png',
        ];

    private function buildCategoryEmojis()
    {
        $result = [];
        foreach ($this->icons as $name => $path) {
            $url = asset($path);
            $result[$name] = '<img src="' . $url . '" alt="' . $name . '" style="width:55px;">';
        }
        return $result;
    }

    private function getCommonData($month, $year)
    {
        $monthlyExpenses = Expense::with('category')
            ->where('user_id', auth()->id())
            ->whereMonth('spend_date', $month)
            ->whereYear('spend_date', $year)
            ->orderBy('spend_date', 'desc')
            ->get();

        $monthlyTotal = $monthlyExpenses->sum('amount');

        $today = Carbon::today()->toDateString();
        $dailyTotal = Expense::whereDate('spend_date', $today)
            ->where('user_id', auth()->id())
            ->sum('amount');

        $dailyTotals = Expense::whereMonth('spend_date', $month)
            ->whereYear('spend_date', $year)
            ->where('user_id', auth()->id())
            ->selectRaw('DATE(spend_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $monthYear = sprintf("%04d-%02d", $year, $month);
        $monthlyTarget = Budget::where('month_year', $monthYear)
            ->where('user_id', auth()->id())
            ->sum('target_amount');

        $balance = $monthlyTarget - $monthlyTotal;

        return compact(
            'monthlyExpenses',
            'monthlyTotal',
            'dailyTotal',
            'dailyTotals',
            'monthlyTarget',
            'balance'
        );
    }

    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $data = $this->getCommonData($month, $year);
        $categoryEmojis = $this->buildCategoryEmojis();

        return view('page.layouts.app', array_merge($data, compact('categoryEmojis')));
    }

   public function target(Request $request)
    {
        // Lấy tháng-năm từ request hoặc mặc định hiện tại
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Ghép thành chuỗi "YYYY-MM"
        $monthYear = sprintf("%04d-%02d", $year, $month);

        // Dữ liệu chung (tổng chi, số dư,...)
        $data = $this->getCommonData($month, $year);

        // Lấy các mục tiêu của tháng này, đúng user
        $targets = Budget::with('category')
            ->where('month_year', $monthYear)
            ->where('user_id', auth()->id()) // ✅ Luôn có ()
            ->get();

        // Gán emoji
        $categoryEmojis = $this->buildCategoryEmojis();

        // Trả về view với dữ liệu đầy đủ
        return view('page.layouts.target', array_merge(
            $data,
            compact('targets', 'categoryEmojis', 'monthYear')
        ));
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
```
**ExpenseController**
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
**BudgetController**
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Category;

class BudgetController extends Controller
{
    private function getIconMap()
    {
        return [
            'Ăn uống'   => '<img src="' . asset('img/burger.png') . '" alt="Ăn uống" style="width:55px;">',
            'Du lịch'   => '<img src="' . asset('img/travel-bag.png') . '" alt="Du lịch" style="width:55px;">',
            'Mua sắm'   => '<img src="' . asset('img/shopping-cart.png') . '" alt="Mua sắm" style="width:55px;">',
            'Học tập'   => '<img src="' . asset('img/school-material.png') . '" alt="Học tập" style="width:55px;">',
            'Giao thông'=> '<img src="' . asset('img/vehicles.png') . '" alt="Giao thông" style="width:55px;">',
            'Sức khỏe'  => '<img src="' . asset('img/medicine.png') . '" alt="Sức khỏe" style="width:55px;">',
            'Điện thoại'=> '<img src="' . asset('img/app.png') . '" alt="Điện thoại" style="width:55px;">',
            'Thú cưng'  => '<img src="' . asset('img/pet-food.png') . '" alt="Thú cưng" style="width:55px;">',
            'Xã hội'    => '<img src="' . asset('img/ancestors.png') . '" alt="Xã hội" style="width:55px;">',
            'Khác'      => '<img src="' . asset('img/question-mark.png') . '" alt="Khác" style="width:55px;">',
            'Nhà ở'     => '<img src="' . asset('img/house.png') . '" alt="Nhà ở" style="width:55px;">',
            'Giải trí'  => '<img src="' . asset('img/online-gaming.png') . '" alt="Giải trí" style="width:55px;">',
        ];
    }
    public function index(Request $request)
    {
        $categories = Category::all();
        $budgets = Budget::with('category')
         ->where('user_id', auth()->id())
         ->orderBy('month_year', 'desc')
         ->get();
        // Tính tổng mục tiêu theo từng tháng
        $totalBudgetsByMonth = Budget::selectRaw('month_year, SUM(target_amount) as total')
         ->where('user_id', auth()->id())
        ->groupBy('month_year')
        ->orderBy('month_year', 'desc')
        ->pluck('total', 'month_year'); // trả về dạng: ['2025-05' => 3000000, '2025-04' => 2000000]
        $iconMap = $this->getIconMap();
        // Thêm key 'icon' vào từng category
        foreach ($categories as $category) {
        $category->icon = $iconMap[$category->name] ?? 'default.png';
        }
        $budget = null;
        return view('page.budgets.index', compact('categories', 'budgets','totalBudgetsByMonth', 'budget'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month_year' => 'required|date_format:Y-m',
            'target_amount' => 'required|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'note' => 'nullable|string|max:255',
        ]);

        Budget::updateOrCreate(
            [
                'user_id'     => auth()->id(),
                'month_year' => $request->month_year,
                'category_id' => $request->category_id,
            ],
            [
                'target_amount' => $request->target_amount,
                'note' => $request->note,
            ]
        );

        return redirect()->route('page.budgets.index')->with('success', 'Đặt mục tiêu thành công!');
    }
    
    public function destroy($id)
    {
        $budget = Budget::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();
        $budget->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function edit($id) {
        $budget = Budget::findOrFail($id);
        $categories = Category::all();
        $budgets = Budget::with('category')
            ->where('user_id', auth()->id())
            ->orderBy('month_year', 'desc')
            ->get();
        $totalBudgetsByMonth = Budget::selectRaw('month_year, SUM(target_amount) as total')
            ->where('user_id', auth()->id())
            ->groupBy('month_year')
            ->orderBy('month_year', 'desc')
            ->pluck('total', 'month_year');
        // Thêm icon
        $iconMap = $this->getIconMap();
        foreach ($categories as $category) {
            $category->icon = $iconMap[$category->name] ?? 'default.png';
        }

        return view('page.budgets.index', compact(
            'categories',
            'budgets',
            'totalBudgetsByMonth',
            'budget'
        ));
        }
    public function update(Request $request, $id)
    {
        // Tìm budget theo ID và user
        $budget = Budget::where('id', $id)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();

        // Validate dữ liệu
        $validatedData = $request->validate([
        'month_year' => 'required|date_format:Y-m',
        'target_amount' => 'required|integer|min:1',
        'category_id' => 'required|exists:categories,id',
        'note' => 'nullable|string|max:255',
        ]);
        //Update
        $budget->update($validatedData);
        return redirect()->route('page.budgets.index')
                        ->with('success', 'Cập nhật mục tiêu thành công!');
    }

}
```
**ReportController**
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        //Lấy tháng/năm từ request hoặc mặc định hiện tại
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $formattedMonth = str_pad($month, 2, '0', STR_PAD_LEFT);
        $monthYear = "$year-$formattedMonth";
        $userId = Auth::id();

        //Tổng mục tiêu chi tiêu tháng
        $monthlyTarget = Budget::where('user_id', $userId)
            ->where('month_year', $monthYear)
            ->sum('target_amount');

        //Lấy toàn bộ chi tiêu tháng này
        $monthlyExpenses = Expense::with('category')
            ->where('user_id', $userId)
            ->whereMonth('spend_date', $month)
            ->whereYear('spend_date', $year)
            ->get();

        $monthlyTotal = $monthlyExpenses->sum('amount');

        //Tính số dư
        $balance = $monthlyTarget - $monthlyTotal;

        //Danh mục
        $categories = Category::all();

        //Chi tiêu theo danh mục
        $expensesByCategory = $categories->mapWithKeys(function ($category) use ($monthlyExpenses) {
            $total = $monthlyExpenses->where('category_id', $category->id)->sum('amount');
            return [$category->name => $total];
        })->filter(fn($total) => $total > 0);

        //Top 3 danh mục tiêu nhiều nhất
        $topCategories = $expensesByCategory->sortDesc()->take(3);

        //So sánh với tháng trước
        $prevMonthDate = Carbon::createFromDate($year, $month, 1)->subMonth();
        $prevMonthExpenses = Expense::where('user_id', $userId)
            ->whereMonth('spend_date', $prevMonthDate->month)
            ->whereYear('spend_date', $prevMonthDate->year)
            ->get();
        $prevMonthTotal = $prevMonthExpenses->sum('amount');

        $comparePercentage = $prevMonthTotal > 0
            ? (($monthlyTotal - $prevMonthTotal) / $prevMonthTotal) * 100
            : null;

        //Tỷ lệ hoàn thành mục tiêu & cảnh báo
        $completionRate = 0;
        $warning = null;

        if ($monthlyTarget > 0) {
            $completionRate = ($monthlyTotal / $monthlyTarget) * 100;

            if ($completionRate >= 100) {
                $warning = "⚠️ Bạn đã chi tiêu vượt mục tiêu!";
            }
        }

        //Trả ra view
        return view('page.reports.monthly', compact(
            'monthlyTarget',
            'monthlyTotal',
            'balance',
            'expensesByCategory',
            'topCategories',
            'prevMonthTotal',
            'comparePercentage',
            'completionRate',
            'warning',
            'categories',
            'month',
            'year'
        ));
    }

    public function yearly(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $userId = Auth::id();

        //Tổng mục tiêu cả năm
        $yearlyTarget = Budget::where('user_id', $userId)
            ->where('month_year', 'like', "$year%")
            ->sum('target_amount');


        //Tổng chi tiêu cả năm
        $yearlyExpenses = Expense::where('user_id', $userId)
            ->whereYear('spend_date', $year)
            ->get();
        $yearlyTotal = $yearlyExpenses->sum('amount');

        //Số dư
        $balance = $yearlyTarget - $yearlyTotal;

        //Chi tiêu theo tháng
        $monthlyExpenses = [];
        $monthlyExpenses = Expense::where('user_id', $userId)
            ->whereYear('spend_date', $year)
            ->selectRaw('MONTH(spend_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyExpenses = collect(range(1,12))->map(fn($m) => $monthlyExpenses->get($m, 0));


        return view('page.reports.yearly', compact(
            'yearlyTarget',
            'yearlyTotal',
            'balance',
            'monthlyExpenses',
            'year'
        ));
    }

}
```

## Blade Template (View)
![view](https://github.com/user-attachments/assets/cafc33b1-1a53-4b05-ba09-48c629247805)

## Route
Sử dụng Middleware để bảo vệ request
```
<?php

use App\Http\Controllers\FinancialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SpendingController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [FinancialController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::get('/home', [FinancialController::class, 'index'])
    ->middleware(['auth'])
    ->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/home', [FinancialController::class, 'index'])->name('page.layouts.app');
    Route::get('/home/target', [FinancialController::class, 'target'])->name('page.layouts.index');

    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('page.expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('page.expenses.store');
    Route::delete('/expenses/{id}', [FinancialController::class, 'destroy'])->name('expenses.destroy');
    Route::put('/expenses/{id}', [ExpenseController::class, 'update'])->name('page.expenses.update');

    Route::get('/budgets/create', [BudgetController::class, 'index'])->name('page.budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('page.budgets.store');
    Route::get('/budgets/{id}/edit', [BudgetController::class, 'edit'])->name('page.budgets.edit');
    Route::put('/budgets/{id}', [BudgetController::class, 'update'])->name('page.budgets.update');
    Route::delete('/budgets/{id}', [BudgetController::class, 'destroy'])->name('page.budgets.destroy');

    Route::get('/report', [ReportController::class, 'index'])->name('page.reports.monthly');
    Route::get('/reports/yearly', [ReportController::class, 'yearly'])->name('page.reports.yearly');

});


require __DIR__.'/auth.php';
```

## Security Setup
Sử dụng @csrf và xss để chống tấn công
```
<form action="{{ isset($editExpense) ? route('page.expenses.update', $editExpense->id) : route('page.expenses.store') }}" method="POST">
          @csrf
          @if (isset($editExpense))
            @method('PUT')
          @endif
          <div class="row p-0">
            <!-- Cột trái -->
            <div class="col-md-6">
              <div class="mb-3">
                <label for="spend_date" class="form-label">Chọn ngày</label>
                <input type="date" class="form-control" id="spend_date" name="spend_date" 
                       value="{{ old('spend_date', isset($editExpense) ? $editExpense->spend_date : date('Y-m-d')) }}" required>
              </div>
              <div class="mb-3">
                <label for="amount" class="form-label">Số tiền chi tiêu</label>
                <div class="input-group" >
                  <input type="number" class="form-control" id="amount" name="amount" 
                         placeholder="Nhập số tiền..." value="{{ old('amount', $editExpense->amount ?? '') }}" min="1" required>
                  <span class="input-group-text bg-white">VNĐ</span>
                </div>
              </div>
              <div class="mb-3">
                <label for="note" class="form-label">Ghi chú</label>
                <input type="text" class="form-control" id="note" name="note" placeholder="Nhập ghi chú..." value="{{ old('note', $editExpense->note ?? '') }}">
              </div>
               <!-- Nút Thêm khoản chi -->
          <div class="text-center mt-5">
            <button type="submit" class="btn btn-success px-5"> 
               {{ isset($editExpense) ? 'Cập nhật khoản chi' : 'Thêm khoản chi' }}
              </button>
          </div>
        </form>
```
Validation để kiểm tra và ràng buộc dữ liệu
```
public function destroy($id)
    {
        $budget = Budget::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

        $budget->delete();

        return response()->json(['message' => 'Deleted successfully']);


    }
```

## 📅 Hình ảnh các chức năng chính
### 🔑 Xác thực
- 📝 Đăng kí
![dk](https://github.com/user-attachments/assets/fa262351-16d6-4f0e-896d-677a25fc7dbb)
- 📝 Đăng nhập
![dn](https://github.com/user-attachments/assets/96f2ce60-6f82-49ee-9581-dfad545420db)

### 🏠 Trang chủ
- 📃 Xem danh sách chi tiêu
![home](https://github.com/user-attachments/assets/0faf7d4d-0295-40a3-a166-657b5083ab4f)
- 🔍 Xem chi tiết chi tiêu
![sxct](https://github.com/user-attachments/assets/fb3c36c2-4dab-4bee-83b7-e107e6f327ec)
- 📑 Xem danh sách mục tiêu chi tiêu
![hometarget](https://github.com/user-attachments/assets/1723ff45-036f-4c70-b4de-69ef05fb848d)
- 🔍 Xem chi tiết mục tiêu chi tiêu
![sxmt](https://github.com/user-attachments/assets/ee6626f7-9760-4075-b81a-179889cc35c1)

### 🗂️ Quản lý
- 💵 Quản lý chi tiêu
![qlct](https://github.com/user-attachments/assets/87e21885-7108-4e27-a2ae-ac5f73ebf79c)
- 🎯 Quản lý mục tiêu chi tiêu
![qlmt](https://github.com/user-attachments/assets/2d15970b-b42e-4c85-bba8-6adf760e5b9a)

### 📊 Báo cáo
- 🗓️ Báo cáo chi tiêu tháng
![bct](https://github.com/user-attachments/assets/ec851722-693b-4514-af96-418a430546c2)
- 📅 Báo cáo chi tiêu năm
![bcn](https://github.com/user-attachments/assets/81aeaa76-9241-4fe9-985d-70c1455faa39)

### 📌 Github: https://github.com/camlee05/WNC
### 🌐 GitHub Pages: https://camlee05.github.io/WNC/









