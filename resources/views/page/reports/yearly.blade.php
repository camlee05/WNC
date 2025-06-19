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
    .info-box {
      background-color: #f8f9fa;
      padding: 1rem;
      border-radius: 10px;
      border: 1px solid #dee2e6;
      text-align: center;
    }
    .info-box h5 {
      margin-bottom: 0.5rem;
    }
    nav.col-1 a:hover {
    background-color: #cfe2ff;
    transform: translateX(5px);
    transition: 0.3s ease;
    }
  /* Màu nền mới cho box thống kê */
  .info-box {
    background-color:rgb(255, 255, 255); /* Xanh nhạt nhẹ */
    padding: 1rem;
    border-radius: 10px;
    border: 1px solid rgba(69, 165, 157, 0.9); /* Viền xanh */
    text-align: center;
    transition: 0.3s;
  }

  /* Màu nền mới cho card bên phải */
  .card {
    background-color:rgb(255, 255, 255); /* Xanh lá nhạt */
    border: 1px solid rgba(69, 165, 157, 0.9);
    border-radius: 12px;
    transition: 0.3s;
  }
  h4.fw-bold {
    font-size: 1.5rem;
    font-weight:400;
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
          <li class="nav-item"><a class="nav-link btn" href="{{ route('page.expenses.create') }}">
            <img src="{{ asset('img/spending.png') }}" alt="" style="width: 25px;">Quản lý</a></li>
          <li class="nav-item"><a class="nav-link btn btn-primary" href="#" style="background-color: rgba(69, 165, 157, 0.9)">
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
        <div class="d-flex justify-content-between align-items-center">
          <h1>Báo cáo chi tiêu</h1>        
        </div>

        <!-- Menu chọn kiểu xem -->
        <div class="btn-group m-2" role="group" aria-label="Menu Thêm chi tiêu">
          <a href="{{ route('page.reports.monthly') }}" class="btn btn-outline-success text-decoration-none" style="background-color:rgb(255, 255, 255)">Tháng</a>
          <button type="button" class="btn btn-success">Năm</button>
        </div>

        <!-- Nội dung chính chia 2 bên -->
        <div class="row">
          <!-- Bên trái -->
          <div class="col-6">
            <!-- Bộ lọc năm -->
            <div class="mb-3">
              <label for="chonNam" class="form-label">Chọn năm</label>
              <input type="number" class="form-control w-auto" id="chonNam" min="2000" max="2100" value="{{ $year }}" />
            </div>
            <div class="info-box mb-3">
              <h5>Tổng mục tiêu chi tiêu (năm)</h5>
              <p id="tongMucTieu">{{ number_format($yearlyTarget) }} VNĐ</p>
            </div>
            <div class="info-box mb-3">
              <h5>Tổng chi tiêu (năm)</h5>
              <p id="tongChiTieu">{{ number_format($yearlyTotal) }} VNĐ</p>
            </div>
            <div class="info-box">
              <h5>Số tiền còn lại</h5>
              <p id="tienConLai">{{ number_format($balance) }} VNĐ</p>
            </div>
          </div>

          <!-- Bên phải -->
          <div class="col-md-6">
            <div class="card p-3 shadow-sm mb-3">
                <table class="table">
                  @foreach($monthlyExpenses as $index => $amount)
                    <tr>
                      <td>Tháng {{ $index + 1 }}</td>
                      <td>{{ number_format($amount, 0, ',', '.') }} VNĐ</td>
                    </tr>
                  @endforeach
                </table>
            </div>
          </div>
        </div>


      </main>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Gán năm hiện tại làm mặc định
    document.addEventListener("DOMContentLoaded", function () {
      const yearInput = document.getElementById("chonNam");
        if (!yearInput.value) {
          yearInput.value = new Date().getFullYear();
        }
    yearInput.addEventListener("change", function () {
      const newYear = yearInput.value;
      const url = new URL(window.location.href);
      url.searchParams.set('year', newYear);
      window.location.href = url.toString();
    });

    });
    const monthNames = ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
                    "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"];
    const chiTieuThang = @json($monthlyExpenses);
  </script>
</body>
</html>
