<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../classes/dbadmin.php';
$db = new dbadmin();

// ===============================
// Xử lý lọc doanh thu theo ngày
// ===============================
$tu_ngay = $_POST['tu_ngay'] ?? '';
$den_ngay = $_POST['den_ngay'] ?? '';
$doanh_thu_loc = [];

if (!empty($tu_ngay) && !empty($den_ngay)) {
    $doanh_thu_loc = $db->doanhThuTheoKhoangNgay($tu_ngay, $den_ngay);
}

// ===============================
// Thống kê dữ liệu tổng quan
// ===============================

$tong_phong = $db->thongKeTongPhong();
$phong_trong = $db->thongKePhongTrong();
$phong_da_dat = $db->thongKePhongDaDat();
$tong_doanh_thu = $db->tongDoanhThu();

// ===============================
// Doanh thu theo tháng
// ===============================

$thang_data = [];
$doanh_thu_thang = $db->doanhThuTheoThang();

while ($row = $doanh_thu_thang->fetch_assoc()) {
    $thang_data[] = $row;
}

// ===============================
// Tỷ lệ phòng
// ===============================

$ty_le_phong = $db->tiLePhong();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang Thống Kê</title>
    <link rel="stylesheet" href="css/thongke.css">
</head>

<body>
    <div class="container">
        <h1>Thống Kê Khách Sạn</h1>

        <!-- Cards tổng quan -->
        <div class="cards">
            <div class="card">
                <h3>Tổng phòng</h3>
                <p><?= $tong_phong ?></p>
            </div>

            <div class="card">
                <h3>Phòng trống</h3>
                <p><?= $phong_trong ?></p>
            </div>

            <div class="card">
                <h3>Phòng đã đặt</h3>
                <p><?= $phong_da_dat ?></p>
            </div>

            <div class="card">
                <h3>Tổng doanh thu</h3>
                <p><?= number_format($tong_doanh_thu, 0, ',', '.') ?> VNĐ</p>
            </div>
        </div>

        <!-- Bảng doanh thu theo tháng -->
        <h2>Doanh thu theo tháng</h2>
        <table class="table-bieu-do">
            <tr>
                <th>Năm</th>
                <th>Tháng</th>
                <th>Doanh thu (VNĐ)</th>
            </tr>

            <?php foreach ($thang_data as $dt): ?>
                <tr>
                    <td><?= $dt['nam'] ?></td>
                    <td><?= $dt['thang'] ?></td>
                    <td><?= number_format($dt['doanh_thu'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Biểu đồ tròn -->
        <h2>Tỷ lệ phòng trống vs đã đặt</h2>
        <div class="pie-chart">
            <div class="slice" style="--p:<?= ($phong_trong / $tong_phong) * 100 ?>;">
                Trống: <?= $phong_trong ?>
            </div>

            <div class="slice" style="--p:<?= ($phong_da_dat / $tong_phong) * 100 ?>;">
                Đã đặt: <?= $phong_da_dat ?>
            </div>
        </div>

        <!-- Form lọc -->
        <h2>Lọc doanh thu theo ngày</h2>

        <!-- GIỮ NGUYÊN TRANG SAU KHI SUBMIT -->
        <form method="post">
            <label>
                Từ ngày:
                <input type="date" name="tu_ngay" value="<?= $tu_ngay ?>">
            </label>

            <label>
                Đến ngày:
                <input type="date" name="den_ngay" value="<?= $den_ngay ?>">
            </label>

            <button type="submit">Lọc</button>
        </form>

        <!-- Kết quả lọc -->
        <?php if (!empty($tu_ngay) && !empty($den_ngay)): ?>
            <h3>Kết quả từ <?= $tu_ngay ?> đến <?= $den_ngay ?></h3>

            <table class="table-bieu-do">
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                </tr>

                <?php while ($row = $doanh_thu_loc->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['ma_dat_phong'] ?></td>
                        <td><?= $row['ho_ten'] ?></td>
                        <td><?= $row['ngay_dat'] ?></td>
                        <td><?= number_format($row['tong_tien'], 0, ',', '.') ?></td>
                        <td><?= $row['trang_thai'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>

    </div>
</body>

</html>
