<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Quản lý chi tiêu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      background-color: #F7F8FA;
    
        height: 100%;
    }
    .month-btn.active {
      background-color: #0d6efd;
      color: #fff;
    }
    .today {
      background-color: #0d6efd;
      color: #fff;
      border-radius: 50%;
    }
    /* Màu chữ sidebar thành màu đen */
    nav.col-1 {
    color: black !important;
    border-right: 1px solid grey;
    }
    nav.col-1 a {
    color: black !important;
    }
    nav.col-1 a:hover {
    background-color:rgb(178, 192, 214) ;
    border-radius: 10px;
    }
    /* Các ô lịch có kích thước bằng nhau */
#calendarBody td {
  width: 50px;
  height: 60px;
  vertical-align: top;
  position: relative;
  padding: 6px;
  position: relative;
   border: 1px solid #e2e6ea;
  border-radius: 8px;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}


/* Căn giữa nội dung */
#calendarBody td > div {
  text-align: center;
  user-select: none;
  font-weight: 500;
}

nav.col-1 a:hover {
  background-color: #cfe2ff;
  transform: translateX(5px);
  transition: 0.3s ease;
}
/* Bảng lịch bo góc, bóng nhẹ */
table.table-bordered {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
  border: 1px solid #ddd;
}

main{
    padding:24px!important;
    height: 100%;
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
          <li class="nav-item"><a class="nav-link btn btn-primary d-flex flex-column align-items-center" href="#" style="background-color: #0d6efd">
            <img src="{{ asset('img/home.png') }}" alt="" style="width: 25px;">Trang chủ</a></li>
          <li class="nav-item"><a class="nav-link btn d-flex flex-column align-items-center" href="{{ route('page.expenses.create') }}">
            <img src="{{ asset('img/spending.png') }}" alt="" style="width: 25px;">Quản lý</a></li>
          <li class="nav-item"><a class="nav-link btn d-flex flex-column align-items-center" href="{{ route('page.reports.monthly') }}">
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
      <main class="col-11">
        <h1 class="d-flex justify-content-between align-items-center">
  Quản lý chi tiêu
  <div class="d-flex align-items-center gap-2" style="width: 300px;">
    <input type="text" class="form-control" placeholder="Tìm kiếm...">
    <button class="btn btn-primary"><img src="img/search.png" alt="" style="width: 30px;"></button>
  </div>
</h1>
<hr>

        <!-- Thanh menu -->
        <div class="d-flex justify-content-between align-items-center w-100 gap-3 flex-column flex-md-row">
          <!-- Bên trái -->
            <div class="d-flex gap-3 w-100">
  <div class="form-control flex-fill text-center" style="max-width: 25%; height: 100px; display: flex; flex-direction: column; justify-content: center;  background-color:rgb(151, 183, 230);">
    <div class="label">Tổng</div>
    <div class="value fs-4 ">{{ number_format($monthlyTotal) }} đ</div>
  </div>
  <div class="form-control flex-fill text-center" style="max-width: 25%; height: 100px; display: flex; flex-direction: column; justify-content: center;background-color:rgb(151, 230, 168);">
    <div class="label">Mục tiêu</div>
    <div class="value fs-4">{{ number_format($monthlyTarget) }} đ</div>
  </div>
  <div class="form-control flex-fill text-center" style="max-width: 25%; height: 100px; display: flex; flex-direction: column; justify-content: center;background-color:rgb(230, 151, 226);">
    <div class="label">Số dư</div>
    <div class="value fs-4">{{ number_format($balance) }} đ</div>
  </div>
  <div class="form-control flex-fill text-center" style="max-width: 25%; height: 100px; display: flex; flex-direction: column; justify-content: center;background-color:rgb(151, 230, 226);">
    <div class="label">Hôm nay</div>
    <div class="value fs-4">{{ number_format($dailyTotal) }} đ</div>
  </div>
</div>

        </div>
        <!-- Nội dung chính -->
<div class="d-flex flex-row gap-4 my-0 flex-wrap">
  

    <div class="card my-4 flex-grow-1">
    <h4 class="mb-3">Danh sách chi tiêu tháng</h4>
<div class="table-responsive" style="max-height: 400px;">
  <table class="table table-hover align-middle shadow-sm border rounded-3 overflow-hidden">
    <thead class="table-primary text-center">
      <tr>
        <th scope="col">Danh mục</th>
        <th scope="col">Ngày</th>
        <th scope="col">Số tiền</th>
        <th scope="col">Ghi chú</th>
      </tr>
    </thead>
    <tbody>
      @forelse($monthlyExpenses as $expense)
          @php
      $categoryName = $expense->category->name ?? 'Không rõ';
      $color = $categoryColors[$categoryName] ?? '#adb5bd'; // Màu mặc định nếu không có
    @endphp
        <tr>
          <td class="text-center" style="color:{{ $color }};font-weight:bold;">{{ $expense->category->name ?? 'Không rõ' }}</td>
          <td class="text-center">{{ \Carbon\Carbon::parse($expense->spend_date)->format('d/m/Y') }}</td>
          <td class="text-center">{{ number_format($expense->amount) }} đ</td>

          <td class="text-center">{{ $expense->note }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-center text-muted">Không có khoản chi nào trong tháng này.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

  </div>

  <!-- Lịch tháng -->
  <div class="card shadow-sm my-4" style="min-width: 350px; flex-shrink: 0;">
    <div class="card-header d-flex justify-content-between align-items-center fw-bold" style="background-color:white">
        <button id="prevMonthBtn" class="btn btn-sm btn-outline-secondary">
          <img src="img/left-arrow.png" alt="" style="width: 25px;"></button>
        <span id="calendarMonthYear" class="flex-grow-1 text-center">Lịch tháng</span>
        <button id="nextMonthBtn" class="btn btn-sm btn-outline-secondary">
          <img src="img/right-arrow.png" alt="" style="width: 25px;"></button>
      </div>

      <div class="card-body p-2" >
        <table class="table table-bordered text-center mb-0 small">
          <thead>
            <tr>
              <th>T2</th><th>T3</th><th>T4</th><th>T5</th><th>T6</th><th>T7</th><th>CN</th>
            </tr>
          </thead>
          <tbody id="calendarBody">
            <!-- JavaScript sẽ vẽ ngày ở đây -->
          </tbody>
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
    const expenseDataByDay = @json($dailyTotals);

  (function () {
    const monthNames = [
      "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4",
      "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8",
      "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
    ];

    function getParam(name) {
      const params = new URLSearchParams(window.location.search);
      return params.get(name);
    }

    const urlMonth = parseInt(getParam("month")) - 1;
    const urlYear = parseInt(getParam("year"));

    const today = new Date();
    let currentYear = isNaN(urlYear) ? today.getFullYear() : urlYear;
    let currentMonth = isNaN(urlMonth) ? today.getMonth() : urlMonth;

    // Cập nhật tiêu đề lịch tháng-năm
    function updateCalendarTitle() {
      document.getElementById("calendarMonthYear").textContent = `${monthNames[currentMonth]} / ${currentYear}`;
    }

    // Vẽ lịch
    function renderCalendar(month, year) {
      const calendarBody = document.getElementById("calendarBody");
      calendarBody.innerHTML = "";

      const firstDay = new Date(year, month, 1);
      const lastDate = new Date(year, month + 1, 0).getDate();
      const startDay = (firstDay.getDay() + 6) % 7; // Chỉnh thứ 2 là đầu tuần

      let date = 1;
      for (let i = 0; i < 6; i++) {
        let row = document.createElement("tr");
        for (let j = 0; j < 7; j++) {
          let cell = document.createElement("td");
          cell.style.verticalAlign = "top";
          if (i === 0 && j < startDay) {
            cell.textContent = "";
          } else if (date > lastDate) {
            cell.textContent = "";
          } else {
            const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
            const total = Number(expenseDataByDay[fullDate]) || 0;

            cell.innerHTML = `<div>${date}</div><div class="text-muted small">${total.toLocaleString()} đ</div>`;

            // Tô màu hôm nay
            const isToday =
              date === today.getDate() &&
              month === today.getMonth() &&
              year === today.getFullYear();
            if (isToday) cell.classList.add("today");

            date++;
          }
          row.appendChild(cell);
        }
        calendarBody.appendChild(row);
        if (date > lastDate) break;
      }
    }

    // Chuyển tháng trái, phải
    document.getElementById("prevMonthBtn").onclick = () => {
      currentMonth--;
      if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
      }
      updateDisplayAndReload();
    };

    document.getElementById("nextMonthBtn").onclick = () => {
      currentMonth++;
      if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
      }
      updateDisplayAndReload();
    };

    // Cập nhật tiêu đề, vẽ lịch và load lại trang với tham số tháng-năm
    function updateDisplayAndReload() {
      updateCalendarTitle();
      renderCalendar(currentMonth, currentYear);
      const queryMonth = currentMonth + 1;
      window.location.href = `?month=${queryMonth}&year=${currentYear}`;
    }

    // Khởi tạo hiển thị
    updateCalendarTitle();
    renderCalendar(currentMonth, currentYear);
  })();
</script>



</body>
</html>
