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
    }
    nav.col-1 {
      color: black !important;
      border-right: 1px solid grey;
    }
    nav.col-1 a {
      color: black !important;
    }
    nav.col-1 a:hover {
      background-color: rgba(69, 165, 157, 0.9);
      border-radius: 10px;
    }

    .info-box h5 {
      margin-bottom: 0.5rem;
    }
    .month-btn.active {
  background-color: rgba(69, 165, 157, 0.9);
  color: #fff;
}
  .dropdown-menu {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(69, 165, 157, 0.9);
    font-size: 14px;
    padding: 1rem; /* thêm padding nếu chưa có */
  min-width: 260px; /* đảm bảo chiều rộng dropdown đủ đẹp */
  }
  .dropdown-toggle {
    min-width: 160px;
  }
  #monthGrid .btn {
    padding: 0.3rem 0.5rem;
    font-size: 13px;
  }
  #monthGrid .btn.active {
    background-color: rgba(69, 165, 157, 0.9);
    color: white;
  }
  #monthGrid .btn.today {
    border: 1px dashed #0d6efd;
  }
  #monthGrid .btn:hover {
  background-color: rgba(69, 165, 157, 0.2);
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
          @if ($warning)
            <div class="alert alert-danger" role="alert">
              ⚠️ {{ $warning }}
            </div>
          @endif
        </div>

        <div>
          <div class="d-flex justify-content-between align-items-center mb-4">
            <!-- Menu chọn kiểu xem -->
            <div class="btn-group" role="group" aria-label="Menu">
              <button type="button" class="btn btn-success">Tháng</button>
              <a href="{{ route('page.reports.yearly') }}" class="btn btn-outline-success text-decoration-none" style="background-color:rgb(255, 255, 255)">Năm</a>
            </div>
              <!-- Bộ lọc tháng/năm kiểu dropdown -->
            <div class="dropdown m-3" style="background-color:rgb(255, 255, 255)">
              <button id="monthPickerBtn"
                      class="btn btn-outline-secondary dropdown-toggle"
                      data-bs-toggle="dropdown"
                      data-bs-auto-close="outside"
                      aria-expanded="false"></button>

              <div class="dropdown-menu" style="min-width: 260px;">
                <!-- Header: chọn năm -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <button id="prevYear" class="btn btn-sm btn-link px-1">&laquo;</button>
                  <span id="yearLabel" class="fw-bold"></span>
                  <button id="nextYear" class="btn btn-sm btn-link px-1">&raquo;</button>
                </div>

                <!-- Lưới tháng -->
                <div class="row g-2 mb-3" id="monthGrid"></div>

                <!-- Nút xác nhận / huỷ -->
                <div class="d-flex justify-content-end gap-2">
                  <button id="cancelBtn" class="btn btn-outline-secondary btn-sm">Huỷ</button>
                  <button id="confirmBtn" class="btn btn-primary btn-sm">Xác nhận</button>
                </div>
              </div>
          </div>
        </div>


        <!-- Nội dung chính chia 2 bên -->
        <div class="row g-4">
          <!-- Bên trái -->
          <div class="col-md-4">
            <div class="info-box mb-3">
              <h5>Tổng mục tiêu chi tiêu</h5>
              <h4 id="tongMucTieu">{{ number_format($monthlyTarget, 0, ',', '.') }} đ</h4>
            </div>
            <div class="info-box">
              <h5>Số tiền còn lại</h5>
              <h4 id="tienConLai">{{ number_format($balance, 0, ',', '.') }} đ</h4>
            </div>
          </div>

          <!-- Giữa -->
          <div class="col-md-4">
            <div class="info-box mb-3">
              <h5>Tổng chi tiêu</h5>
              <h4 id="tongChiTieu">{{ number_format($monthlyTotal, 0, ',', '.') }} đ</h4>
            </div>
            <!-- Tỷ lệ hoàn thành mục tiêu -->
            <div class="info-box mb-3">
              <h5>Tỷ lệ hoàn thành mục tiêu </h5>
              <h4>{{ number_format($completionRate, 2) }}%</h4>
            </div>
          </div>

          <!-- Bên phải -->
                     <div class="col-md-4">
            <div class="info-box mb-3 ">
              <h5>So với tháng trước</h5>
              @if ($comparePercentage !== null)
                <h4 class="text-primary">
                  {{ $comparePercentage >= 0 ? 'tăng' : 'giảm' }}
                  {{ number_format($comparePercentage, 2) }}% 
                </h4>
              @else
                <p>Không có dữ liệu tháng trước</p>
              @endif
            </div>
          </div>

        </div>

      

        <div class="row g-4">
          <div class="col-md-6">
            <div class="card p-3 shadow-sm mb-3">
              <h5 class="mb-3">Chi tiêu theo danh mục</h5>
              <table class="table">
                @foreach($expensesByCategory as $categoryName => $amount)
                  <tr>
                    <td>{{ $categoryName }}</td>
                    <td>{{ number_format($amount, 0, ',', '.') }} VNĐ</td>
                  </tr>
                @endforeach
              </table>
          </div>
          </div>
          <div class="col-md-6">
            <div class="card p-3 shadow-sm">
              <h5>Top 3 danh mục tiêu nhiều nhất</h5>
              <table class="table mb-0">
                @foreach($topCategories as $name => $amount)
                  <tr>
                    <td>{{ $name }}</td>
                    <td>{{ number_format($amount, 0, ',', '.') }} đ</td>
                  </tr>
                @endforeach
              </table>
            </div>
          </div>

        </div>


      </main>
    </div>
  </div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  (function () {
    function getParam(name) {
      const params = new URLSearchParams(window.location.search);
      return params.get(name);
    }

    const urlMonth = parseInt(getParam("month")) - 1;
    const urlYear = parseInt(getParam("year"));

    const today = new Date();
    let currentYear = isNaN(urlYear) ? today.getFullYear() : urlYear;
    let currentMonth = isNaN(urlMonth) ? today.getMonth() : urlMonth;

    let tempYear = currentYear;
    let tempMonth = currentMonth;

    const monthNames = [
      "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4",
      "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8",
      "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
    ];

    const btnPicker = document.getElementById("monthPickerBtn");
    const yearLabel = document.getElementById("yearLabel");
    const monthGrid = document.getElementById("monthGrid");

    const prevYearBtn = document.getElementById("prevYear");
    const nextYearBtn = document.getElementById("nextYear");
    const cancelBtn = document.getElementById("cancelBtn");
    const confirmBtn = document.getElementById("confirmBtn");

    function updateDisplay() {
      btnPicker.textContent = `${monthNames[currentMonth]} / ${currentYear}`;
      renderMonthButtons();
      yearLabel.textContent = tempYear;
    }

    function renderMonthButtons() {
      monthGrid.innerHTML = "";
      for (let i = 0; i < 12; i++) {
        const col = document.createElement("div");
        col.className = "col-4";
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "btn btn-sm w-100 month-btn";
        btn.textContent = monthNames[i];
        if (tempMonth === i && tempYear === currentYear) {
          btn.classList.add("active");
        }
        if (
          tempYear === today.getFullYear() &&
          i === today.getMonth()
        ) {
          btn.classList.add("today");
        }
        btn.onclick = () => {
          tempMonth = i;
          renderMonthButtons();
        };
        col.appendChild(btn);
        monthGrid.appendChild(col);
      }
    }

    prevYearBtn.onclick = () => {
      tempYear--;
      yearLabel.textContent = tempYear;
      renderMonthButtons();
    };

    nextYearBtn.onclick = () => {
      tempYear++;
      yearLabel.textContent = tempYear;
      renderMonthButtons();
    };

    cancelBtn.onclick = () => {
      tempMonth = currentMonth;
      tempYear = currentYear;
      renderMonthButtons();
    };

    confirmBtn.onclick = () => {
      currentMonth = tempMonth;
      currentYear = tempYear;
      window.location.href = `?month=${currentMonth + 1}&year=${currentYear}`;
    };

    updateDisplay();
  })();
</script>

</body>
</html>
