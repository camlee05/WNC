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

  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-1 vh-100 p-3" style="position: sticky; top:0;">
        <h4>Sidebar</h4>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link btn" href="#">Trang chủ</a></li>
          <li class="nav-item"><a class="nav-link btn" href="#">Quản lý</a></li>
          <li class="nav-item"><a class="nav-link btn" href="#">Biểu đồ</a></li>
          <li class="nav-item"><a class="nav-link btn btn-primary" href="#" style="background-color: #0d6efd">Báo cáo</a></li>
        </ul>
      </nav>

      <!-- Main Content -->
      <main class="col-11 p-0">
        <h1 style="border-bottom: 1px solid grey; margin-bottom: 0px;">
          <div class="ms-4">Báo cáo chi tiêu</div>
        </h1>

        <!-- Menu chọn kiểu xem -->
        <div class="btn-group m-2" role="group" aria-label="Menu Thêm chi tiêu">
          <button type="button" class="btn btn-outline-primary">Tháng</button>
          <button type="button" class="btn btn-primary">Năm</button>
        </div>
        <div style="border-top: 1px solid grey;"></div>

        <!-- Bộ lọc năm -->
        <div class="m-3">
          <label for="chonNam" class="form-label">Chọn năm</label>
          <input type="number" class="form-control w-auto" id="chonNam" min="2000" max="2100" />
        </div>

        <!-- Nội dung chính chia 2 bên -->
        <div class="row m-3">
          <!-- Bên trái -->
          <div class="col-md-6">
            <div class="info-box mb-3">
              <h5>Tổng mục tiêu chi tiêu (năm)</h5>
              <p id="tongMucTieu">120,000,000 VNĐ</p>
            </div>
            <div class="info-box mb-3">
              <h5>Tổng chi tiêu (năm)</h5>
              <p id="tongChiTieu">90,000,000 VNĐ</p>
            </div>
            <div class="info-box">
              <h5>Số tiền còn lại</h5>
              <p id="tienConLai">30,000,000 VNĐ</p>
            </div>
          </div>

          <!-- Bên phải -->
          <div class="col-md-6">
            <div class="row">
              <!-- 12 tháng -->
              <div class="col-6 mb-2" id="thangChiTieu">
                <!-- Dữ liệu từng tháng sẽ được thêm ở đây bằng JS -->
              </div>
            </div>
          </div>
        </div>

        <!-- Biểu đồ -->
        <div class="m-3">
          <h5 class="text-center mb-3">Biểu đồ chi tiêu theo tháng</h5>
          <div class="text-center">
            <canvas id="pieChart" width="400" height="400"></canvas>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Gán năm hiện tại làm mặc định
    document.addEventListener("DOMContentLoaded", function () {
      const yearInput = document.getElementById("chonNam");
      const currentYear = new Date().getFullYear();
      yearInput.value = currentYear;

      // Dữ liệu giả chi tiêu theo tháng
      const chiTieuThang = [
        7000000, 8000000, 8500000, 7500000,
        6500000, 6000000, 9000000, 8800000,
        9200000, 8700000, 8600000, 9500000
      ];
      const monthNames = [
        "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4",
        "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8",
        "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
      ];

      const thangChiTieuContainer = document.getElementById("thangChiTieu");
      for (let i = 0; i < 12; i++) {
        const div = document.createElement("div");
        div.className = "info-box mb-2";
        div.innerHTML = `<h6>${monthNames[i]}</h6><p>${chiTieuThang[i].toLocaleString()} VNĐ</p>`;
        thangChiTieuContainer.appendChild(div);
      }

      // Biểu đồ tròn
      const ctx = document.getElementById("pieChart").getContext("2d");
      new Chart(ctx, {
        type: "pie",
        data: {
          labels: monthNames,
          datasets: [{
            data: chiTieuThang,
            backgroundColor: monthNames.map((_, i) =>
              `hsl(${i * 30}, 70%, 60%)`
            ),
          }],
        },
      });
    });
  </script>
</body>
</html>
