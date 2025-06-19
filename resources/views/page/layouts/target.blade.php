<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Quản lý chi tiêu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      background-color: #F7F8FA;
      overflow-y: hidden;
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
    border-right: 1px solid #ddd;
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
      max-width: 55px;
      min-width: 55px;
      height: 55px;
      vertical-align: top;
      position: relative;
      border: 1px solid #e2e6ea;
      border-radius: 8px;
      font-size: 0.8rem;
      padding: 0px;
    }
    .lich>th {
      background-color: rgb(80, 192, 183) !important;
      color: white;
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
      cursor: pointer;
    }

    main{
        padding:24px!important;
        height: 100%;
    }
    .today {
      background-color: rgb(80, 192, 183, 0.5) !important; 
      font-weight: bold;
      color: #000;
    }
    .expense-item:hover{
      background-color: rgb(80, 192, 183, 0.5);
      cursor: pointer;
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
          <li class="nav-item"><a class="nav-link btn btn-primary d-flex flex-column align-items-center" href="#" style="background-color: rgba(69, 165, 157, 0.9)">
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
      <main class="col-11 p-3">
        <h1 class="d-flex justify-content-between align-items-center" style="padding-bottom: 8px;">
          Quản lý chi tiêu
          <div class="d-flex align-items-center gap-2" style="width: 300px;">
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
            <button class="btn btn-primary"><img src="img/search.png" alt="" style="width: 30px;"></button>
          </div>
        </h1>
    
        <!-- Thanh menu -->
        <div class="d-flex justify-content-between align-items-center w-100 gap-3 flex-column flex-md-row">
          <div class="d-flex gap-3 w-100">
            <div class="form-control flex-fill text-center" style="max-width: 25%; height: 80px; display: flex; flex-direction: column; justify-content: center;  background-color:rgb(151, 183, 230);">
              <div class="label">Tổng</div>
              <div class="value fs-4 ">{{ number_format($monthlyTotal) }} đ</div>
            </div>
            <div class="form-control flex-fill text-center" style="max-width: 25%; height: 80px; display: flex; flex-direction: column; justify-content: center;background-color:rgb(151, 230, 168);">
              <div class="label">Mục tiêu</div>
              <div class="value fs-4">{{ number_format($monthlyTarget) }} đ</div>
            </div>
            <div class="form-control flex-fill text-center" style="max-width: 25%; height: 80px; display: flex; flex-direction: column; justify-content: center;background-color:rgb(230, 151, 226);">
              <div class="label">Số dư</div>
              <div class="value fs-4">{{ number_format($balance) }} đ</div>
            </div>
            <div class="form-control flex-fill text-center" style="max-width: 25%; height: 80px; display: flex; flex-direction: column; justify-content: center;background-color:rgb(151, 230, 226);">
              <div class="label">Hôm nay</div>
              <div class="value fs-4">{{ number_format($dailyTotal) }} đ</div>
            </div>
          </div>
        </div>
        <!-- Nội dung chính -->
        <div class="d-flex flex-row gap-4 my-0 flex-wrap">
          <div class="card my-4 flex-grow-1">
            <div class="d-flex justify-content-between align-items-center m-3">
              <h4 class="mb-0">Danh sách mục tiêu chi tiêu tháng</h4>
              <a href="{{ route('page.layouts.app') }}" class="btn btn-success">Chi tiêu</a>
            </div>

            <div class="d-flex flex-column gap-3" style="max-height: 350px; overflow-y:auto;">
              @forelse ($targets as $target)
                @php
                  $categoryName = $target->category->name ?? 'Khác';
                  $emoji = $categoryEmojis[$categoryName];
                @endphp
              <div class="card shadow-sm rounded-3 p-2 expense-item"
                data-id="{{ $target->id }}"
                data-category-name="{{ $categoryName }}"
                data-amount="{{ number_format($target->target_amount) }} đ">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                    <div style="margin-right: 10px;">{!! $emoji !!}</div>
                    <div style="font-size: 1.5rem; font-weight: 600;">{{ $categoryName }}</div>
                  </div> 
                    <div style="font-size: 1.25rem; font-weight: 700;">
                      {{ number_format($target->target_amount) }} đ
                    </div>
                  </div>
                </div>

                @empty
                  <div class="text-center text-muted">Không có mục tiêu chi tiêu trong tháng này.</div>
                @endforelse
              </div>
          </div>

    <!-- Lịch tháng -->
    <div class="card shadow-sm my-4" style="min-width: 350px; flex-shrink: 0;">
      <div class="card-header d-flex justify-content-between align-items-center fw-bold" style="border: none;">
        <button id="prevMonthBtn" class="btn btn-sm ">
          <img src="{{ asset('img/left-arrow.png') }}" alt="" style="width: 25px;">
        </button>
        <h4 id="calendarMonthYear" class="flex-grow-1 text-center m-1" >Lịch tháng</h4>
        <button id="nextMonthBtn" class="btn btn-sm ">
          <img src="{{ asset('img/right-arrow.png') }}" alt="" style="width: 25px;">
        </button>
      </div>
      <div class="card-body p-0" >
        <table class="table table-bordered text-center m-0 small" style="border: 1px solid #000;">
          <thead style="border: 1px solid #ddd;">
            <tr class="lich">
              <th >T2</th><th>T3</th><th>T4</th><th>T5</th><th>T6</th><th>T7</th><th>CN</th>
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

  <!-- Modal Chi tiết Mục tiêu -->
<div class="modal fade" id="targetDetailModal" tabindex="-1" aria-labelledby="targetDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">    
      <!-- Header -->
      <div class="modal-header bg-gradient rounded-top-4" style="background-color:rgb(80, 192, 183)">
        <h5 class="modal-title" id="targetDetailModalLabel">
          <img src="{{ asset('img/list.png') }}" style="height: 25px;" class="me-2"> Chi tiết mục tiêu chi tiêu
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body px-4 py-3">
        <div class="mb-6">
          <img src="{{ asset('img/folder.png') }}" style="height: 20px;" class="me-2">
          <strong>Danh mục:</strong> <span id="modalCategoryName" class="text-muted"></span>
        </div>
        <div class="mb-6">
          <img src="{{ asset('img/money.png') }}" style="height: 20px;" class="me-2">
          <strong>Số tiền:</strong> <span id="modalAmount" class="text-danger fw-semibold"></span>
        </div>
      </div>
      <!-- Footer -->
      <div class="modal-footer justify-content-between px-4 pb-4">
        <button id="editTargetBtn" type="button" class="btn btn-outline-primary rounded-pill px-4" data-id="">
          <img src="{{ asset('img/edit.png') }}" style="height: 20px;" class="me-1"> Sửa
        </button>
        <button id="deleteTargetBtn" type="button" class="btn btn-outline-danger rounded-pill px-4">
          <img src="{{ asset('img/bin.png') }}" style="height: 20px;" class="me-1"> Xóa
        </button>
      </div>

    </div>
  </div>
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
            
            cell.innerHTML = `<div>${date}</div><div class="text-muted small">${total.toLocaleString()}</div>`;
            
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
  let selectedTargetId = null;
  document.querySelectorAll('.expense-item').forEach(item => {
    item.addEventListener('click', () => {
      selectedTargetId = item.dataset.id;

      document.getElementById('modalCategoryName').textContent = item.dataset.categoryName;
      document.getElementById('modalAmount').textContent = item.dataset.amount;
      document.getElementById('editTargetBtn').dataset.id = selectedTargetId;
      document.getElementById('deleteTargetBtn').dataset.id = selectedTargetId;

      const modal = new bootstrap.Modal(document.getElementById('targetDetailModal'));
      modal.show();
    });
  });

// Xử lý nút "Xóa"
document.getElementById('deleteTargetBtn').addEventListener('click', function () {
  if (confirm("Bạn có chắc chắn muốn xóa mục tiêu này?")) {
    fetch(`/budgets/${selectedTargetId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })
    .then(response => {
      if (response.ok) {
        // Ẩn modal
        const modalEl = document.getElementById('targetDetailModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        modalInstance.hide();

        // Xóa thẻ hiển thị mục tiêu khỏi DOM
        document.querySelector(`.expense-item[data-id="${selectedTargetId}"]`).remove();

        // (Tuỳ chọn) Cập nhật tổng số tiền mục tiêu, số dư, v.v.
      } else {
        alert("Xóa thất bại. Vui lòng thử lại.");
      }
    })
    .catch(error => {
      console.error(error);
      alert("Đã xảy ra lỗi.");
    });
  }
});


// Khi click nút "Sửa"
document.getElementById('editTargetBtn')?.addEventListener('click', function () {
  const id = this.dataset.id;
  if (!id) return;

  // Điều hướng đến form tạo mục tiêu, kèm theo ID để sửa
  window.location.href = `/budgets/${id}/edit`;
});


</script>



</body>
</html>
