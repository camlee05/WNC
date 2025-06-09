<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý chi tiêu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    nav.col-1 {
      color: black !important;
      border-right: 1px solid grey;
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
      background-color: #0d6efd !important;
      color: white !important;
      border-color: #0d6efd !important;
    }
    nav.col-1 a:hover {
  background-color: #cfe2ff;
  transform: translateX(5px);
  transition: 0.3s ease;
}

  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
            <nav class="col-1 vh-100 p-3" style="position: sticky; top:0;">
        <h4><img src="{{ asset('img/budget.png') }}" alt="Icon Budget" style="width: 75px;"></h4>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link btn " href="{{ route('page.layouts.app') }}">
            <img src="{{ asset('img/home.png') }}" alt="" style="width: 25px;">Trang chủ</a></li>
          <li class="nav-item"><a class="nav-link btn btn-primary" href="" style="background-color: #0d6efd">
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

      <main class="col-11 p-4">
        <div class="d-flex justify-content-between align-items-center">
          <h1>Đặt mục tiêu chi tiêu</h1>
          <div class="btn-group" role="group" aria-label="Menu Thêm chi tiêu">
          <a href="{{ route('page.expenses.create') }}" class="btn btn-outline-primary text-decoration-none">Chi tiêu</a>
         <button type="button" class="btn btn-primary">Đặt mục tiêu</button>
        </div>
      </div>
      <hr>

        <div class="m-4">
          @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif

          @if(session('success'))
          <div class="alert alert-success">
            {{ session('success') }}
          </div>
          @endif
        </div>

        <form action="{{ route('page.budgets.store') }}" method="POST">
          @csrf
          <div class="row p-4">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="thangNam" class="form-label">Chọn tháng/năm</label>
                <input type="month" class="form-control" id="thangNam" name="month_year"
                  value="{{ old('month_year', date('Y-m')) }}" required />
              </div>
              <div class="mb-3">
                <label for="sotien" class="form-label">Số tiền mục tiêu</label>
                <div class="input-group">
                  <input type="number" class="form-control" id="sotien" name="target_amount"
                    placeholder="Nhập số tiền..." value="{{ old('target_amount') }}" min="1" required />
                  <span class="input-group-text">VNĐ</span>
                </div>
              </div>
              <div class="mb-3">
                <label for="ghichu" class="form-label">Ghi chú</label>
                <input type="text" class="form-control" id="ghichu" name="note" placeholder="Nhập ghi chú..."
                  value="{{ old('note') }}" />
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label d-block">Chọn danh mục</label>
              <div class="row g-2">
                @foreach ($categories as $category)
                <div class="col-4">
                  <button type="button" class="btn btn-outline-primary w-100 category-button"
                    data-id="{{ $category->id }}">{{ $category->name }}</button>
                </div>
                @endforeach
              </div>
              <input type="hidden" name="category_id" id="category_id" value="{{ old('category_id') }}" required />
            </div>

            <div class="col-12 text-center mt-4">
              <button type="submit" class="btn btn-success px-5">Thêm mục tiêu</button>
            </div>
          </div>
        </form>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const categoryButtons = document.querySelectorAll('.category-button');
      const categoryInput = document.getElementById('category_id');

      categoryButtons.forEach(btn => {
        btn.addEventListener('click', function () {
          categoryButtons.forEach(b => b.classList.remove('btn-active-custom'));
          this.classList.add('btn-active-custom');
          categoryInput.value = this.getAttribute('data-id');
        });
      });

      // Khôi phục trạng thái chọn cũ
      const oldCategoryId = categoryInput.value;
      if (oldCategoryId) {
        categoryButtons.forEach(btn => {
          if (btn.getAttribute('data-id') === oldCategoryId) {
            btn.classList.add('btn-active-custom');
          }
        });
      }
    });
  </script>
</body>
</html>
