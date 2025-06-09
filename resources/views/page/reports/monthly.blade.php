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
    .month-btn.active {
  background-color: #0d6efd;
  color: #fff;
}
  .dropdown-menu {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
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
    background-color: #0d6efd;
    color: white;
  }
  #monthGrid .btn.today {
    border: 1px dashed #0d6efd;
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
      <!-- Sidebar -->
      <nav class="col-1 vh-100 p-3" style="position: sticky; top:0;">
        <h4><img src="{{ asset('img/budget.png') }}" alt="Icon Budget" style="width: 75px;"></h4>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link btn " href="{{ route('page.layouts.app') }}">
            <img src="{{ asset('img/home.png') }}" alt="" style="width: 25px;">Trang chủ</a></li>
          <li class="nav-item"><a class="nav-link btn" href="{{ route('page.expenses.create') }}">
            <img src="{{ asset('img/spending.png') }}" alt="" style="width: 25px;">Quản lý</a></li>
          <li class="nav-item"><a class="nav-link btn btn-primary" href="#" style="background-color: #0d6efd">
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
      <main class="col-11 p-0">
        <h1 style="border-bottom: 1px solid grey; margin-bottom: 0px;">
          <div class="ms-4">Báo cáo chi tiêu</div>
        </h1>

        <!-- Menu chọn kiểu xem -->
        <div class="btn-group m-2" role="group" aria-label="Menu Thêm chi tiêu">
          <button type="button" class="btn btn-primary">Tháng</button>
          <button type="button" class="btn btn-outline-primary">Năm</button>
        </div>
        <div style="border-top: 1px solid grey;"></div>

        <!-- Bộ lọc tháng/năm -->
        <!-- Bộ lọc tháng/năm kiểu dropdown -->
<div class="dropdown m-3">
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


        <!-- Nội dung chính chia 2 bên -->
        <div class="row m-3">
          <!-- Bên trái -->
          <div class="col-md-6">
            <div class="info-box mb-3">
              <h5>Tổng mục tiêu chi tiêu</h5>
              <p id="tongMucTieu">{{ number_format($monthlyTarget, 0, ',', '.') }} đ</p>
            </div>
            <div class="info-box mb-3">
              <h5>Tổng chi tiêu</h5>
              <p id="tongChiTieu">{{ number_format($monthlyTotal, 0, ',', '.') }} đ</p>
            </div>
            <div class="info-box">
              <h5>Số tiền còn lại</h5>
              <p id="tienConLai">{{ number_format($balance, 0, ',', '.') }} đ</p>
            </div>
          </div>

          <!-- Bên phải -->
          <div class="col-md-6">
            <div class="info-box mb-2">
              <h5>Ăn uống</h5>
              <p>2,000,000 VNĐ</p>
            </div>
            <div class="info-box mb-2">
              <h5>Giao thông</h5>
              <p>1,000,000 VNĐ</p>
            </div>
            <div class="info-box mb-2">
              <h5>Giải trí</h5>
              <p>1,500,000 VNĐ</p>
            </div>
            <div class="info-box mb-2">
              <h5>Nhà ở</h5>
              <p>3,000,000 VNĐ</p>
            </div>
          </div>
        </div>

        <!-- Biểu đồ -->
        <div class="m-3">
          <h5 class="text-center mb-3">Biểu đồ chi tiêu theo danh mục</h5>
          <div class="text-center">
            <canvas id="pieChart" width="400" height="400"></canvas>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Thư viện JS -->
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
