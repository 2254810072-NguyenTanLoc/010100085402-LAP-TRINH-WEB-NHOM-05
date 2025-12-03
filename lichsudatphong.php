<?php
session_start();
include 'includes/config.php';
include 'classes/dbadmin.php';

if (!isset($_SESSION['ma_khach_hang'])) {
    echo "<script>alert('Bạn cần đăng nhập để xem lịch sử đặt phòng!'); window.location='dangnhap.php';</script>";
    exit();
}

$db = new dbadmin();
$ma_khach_hang = intval($_SESSION['ma_khach_hang']);

// Lấy toàn bộ lịch sử đặt phòng chi tiết
$result = $db->getLichSuDatPhongChiTiet($ma_khach_hang);

// Gom dữ liệu vào mảng
$rows = [];
if ($result) {
    while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử đặt phòng</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9;}
        .container {padding: 10px;}
        h2 { color: #1976d2; }
        h3 { margin-top: 30px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: #fff; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f0f0f0; }
        td img { width: 100px; height: 70px; object-fit: cover; border-radius: 6px; }
        .room-info { text-align: left; }
        .status-cho { color: #f0ad4e; font-weight: bold; }
        .status-xacnhan { color: #28a745; font-weight: bold; }
        .status-huy { color: #6c757d; font-weight: bold; }
        .btn-huy { background-color: #dc3545; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; }
        .btn-huy:hover { background-color: #a71d2a; }
        a.back { display: inline-block; margin-top: 20px; background: #1976d2; color: #fff; padding: 8px 14px; border-radius: 8px; text-decoration: none; }
        a.back:hover { background: #0d47a1; }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="container">
<h2>Lịch sử đặt phòng</h2>

<?php
$total_rows = count($rows);
if ($total_rows > 0) {
    $index = 0;

    while ($index < $total_rows) {
        $current_datphong = $rows[$index]['ma_dat_phong'] ?? 0;

        echo "<h3>Đơn #" . htmlspecialchars($current_datphong) . 
             " - Ngày đặt: " . htmlspecialchars($rows[$index]['ngay_dat'] ?? '') . "</h3>";

        echo "<table>";
        echo "<tr>
                <th>Ảnh</th>
                <th>Tên phòng</th>
                <th>Ngày nhận</th>
                <th>Ngày trả</th>
                <th>Giá / đêm</th>
                <th>Thành tiền</th>
              </tr>";

        $tong_don = 0;

        // Lặp tất cả phòng của đơn hiện tại
        while ($index < $total_rows && ($rows[$index]['ma_dat_phong'] ?? 0) == $current_datphong) {
            $row = $rows[$index];

            $anh_phong = htmlspecialchars($row['anh_phong'] ?? 'default.jpg');
            $ten_phong = htmlspecialchars($row['ten_phong'] ?? '');
            $ct_ngay_den = htmlspecialchars($row['ct_ngay_den'] ?? '');
            $ct_ngay_di = htmlspecialchars($row['ct_ngay_di'] ?? '');
            $gia = isset($row['gia']) ? (float)$row['gia'] : 0;
            $ct_thanh_tien = isset($row['ct_thanh_tien']) ? (float)$row['ct_thanh_tien'] : 0;

            $tong_don += $ct_thanh_tien;

            echo "<tr>
                    <td><img src='uploads/$anh_phong' alt=''></td>
                    <td class='room-info'>$ten_phong</td>
                    <td>$ct_ngay_den</td>
                    <td>$ct_ngay_di</td>
                    <td>" . number_format($gia) . " VND</td>
                    <td>" . number_format($ct_thanh_tien) . " VND</td>
                  </tr>";

            $trang_thai = $row['trang_thai'] ?? 'da_huy';
            $index++;
        }

        // Hiển thị tổng đơn
        echo "<tr>
                <td colspan='5' style='text-align:right;font-weight:bold;'>Tổng đơn:</td>
                <td style='font-weight:bold;'>" . number_format($tong_don) . " VND</td>
              </tr>";

        echo "<tr><td colspan='6' style='text-align:right;'>";
        switch ($trang_thai) {
            case 'cho_xac_nhan':
                echo "<span class='status-cho'>Chờ xác nhận</span> ";
                echo '<a class="btn-huy" href="huydatphong.php?ma_dat_phong=' . $current_datphong . '" onclick="return confirm(\'Bạn có chắc muốn hủy đơn này?\')">Hủy</a>';
                break;
            case 'da_xac_nhan':
                echo "<span class='status-xacnhan'>Đã xác nhận</span>";
                break;
            case 'da_huy':
                echo "<span class='status-huy'>Đã hủy</span>";
                break;
            default:
                echo "<span class='status-huy'>Không xác định</span>";
                break;
        }
        echo "</td></tr>";

        echo "</table><br>";
    }

} else {
    echo "<p>Bạn chưa có lịch sử đặt phòng nào.</p>";
}
?>

<a class="back" href="index.php">← Quay lại trang chủ</a>
</div>
</body>
</html>
