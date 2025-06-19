<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý chi tiêu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body{
      background-color: #F7F8FA;
      overflow-y: hidden;
    }
    nav.col-1 {
      color: black !important;
      border-right: 1px solid #ddd;
    }
    nav.col-1 a {
      color: black !important;
    }
    nav.col-1 a:hover {
      background-color: rgb(178, 192, 214);
      border-radius: 10px;
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    input[type=number] {
      -moz-appearance: textfield;
    }
    .btn-active-custom {
      background-color: rgba(69, 165, 157, 0.9) !important;
      color: white !important;
      border-color: rgba(69, 165, 157, 0.9) !important;
    }
    nav.col-1 a:hover {
    background-color: #cfe2ff;
    transform: translateX(5px);
    transition: 0.3s ease;
    }
    .category-button{
      background-color: white;
    }
    .category-button:hover {
      background-color: rgba(69, 165, 157, 0.9) !important;
      border-color: rgba(69, 165, 157, 0.9) !important;
      color: white !important;
      }

  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-1 vh-100 p-3" style="position: sticky; top:0;background-color: rgb(80, 192, 183);">
        <h4><img src="{{ asset('img/budget.png') }}" alt="Icon Budget" style="width: 75px;"></h4>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link btn " href="{{ route('page.layouts.app') }}">
            <img src="{{ asset('img/home.png') }}" alt="" style="width: 25px;">Trang chủ</a></li>
          <li class="nav-item"><a class="nav-link btn btn-primary" href="" style="background-color: rgba(69, 165, 157, 0.9)">
            <img src="{{ asset('img/spending.png') }}" alt="" style="width: 25px;">Quản lý</a></li>
          <li class="nav-item"><a class="nav-link btn" href="{{ route('page.reports.monthly') }}">
            <img src="{{ asset('img/report.png') }}" alt="" style="width: 25px;">Báo cáo</a></li>
        </ul>
        <!-- Dropdown cố định dưới cùng sidebar -->
        <div class="position-absolute bottom-0 start-0 end-0 mb-3 px-2">
          <div class="dropdown w-100 text-center">
            <a href="#" class="d-flex flex-column align-items-center text-dark text-decoration-none" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="{{ asset('img/profile.png') }}" alt="Profile" class="rounded-circle mb-1" style="width: 40px; height: 40px;">
              <span>{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu text-small shadow w-100" aria-labelledby="userDropdown">
              <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a>
              </li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">Log Out</button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="col-11 p-4">
        <div class="d-flex justify-content-between align-items-center" style="padding:0 0px 10px 0;">
          <h1>Chi tiêu</h1>
          <div class="btn-group" role="group" aria-label="Menu Thêm chi tiêu">
            <button type="button" class="btn btn-success">Chi tiêu</button>
            <a href="{{ route('page.budgets.index') }}" class="btn btn-outline-success text-decoration-none" style="background-color:rgb(255, 255, 255)">
              Đặt mục tiêu
            </a>
          </div>
        </div>
      
        <!-- Hiển thị thông báo lỗi -->
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Hiển thị thông báo thành công -->
        @if (session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
        @endif

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
            </div>

            <!-- Cột phải -->
            <div class="col-md-6">
              <label class="form-label d-block">Chọn danh mục</label>
              <div class="row g-2">
                @foreach ($categories as $category)
                  <div class="col-4">
                    <button type="button" class="btn btn-outline-success w-100 category-button" 
                          data-id="{{ $category->id }}">
                      {!! $category->icon !!}
                      <div>{{ $category->name }}</div>
                    </button>
                  </div>
                @endforeach
              </div>
              <input type="hidden" name="category_id" id="category_id"    value="{{ old('category_id', $editExpense->category_id ?? '') }}" required>
            </div>
          </div>

         
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const categoryButtons = document.querySelectorAll('.category-button');
      const categoryInput = document.getElementById('category_id');

      // Khôi phục trạng thái nút nếu có category_id cũ
      const oldCategoryId = categoryInput.value;
      if (oldCategoryId) {
        categoryButtons.forEach(btn => {
          if (btn.dataset.id === oldCategoryId) {
            btn.classList.add('btn-active-custom');
          }
        });
      }

      // Xử lý chọn danh mục
      categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
          categoryButtons.forEach(b => b.classList.remove('btn-active-custom'));
          this.classList.add('btn-active-custom');
          categoryInput.value = this.dataset.id;
        });
      });
          const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      setTimeout(() => {
        alert.classList.add('fade');
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500); // Xóa khỏi DOM sau khi mờ dần
      }, 3000); // Hiện trong 3 giây
    });
    });
  </script>
</body>
</html>