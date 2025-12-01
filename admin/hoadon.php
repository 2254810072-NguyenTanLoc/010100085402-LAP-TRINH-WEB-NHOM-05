<?php
session_start();
include '../includes/config.php';
include '../classes/dbadmin.php';
if (!isset($_SESSION['admin'])) {
    header("Location: ../dangnhap.php");
    exit();
}

$db = new dbadmin();
$hoadons = $db->getAll("hoadon");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quản lý hóa đơn</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>Danh sách hóa đơn 🧾</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Mã hóa đơn</th>
            <th>Mã đặt phòng</th>
            <th>Mã khách hàng</th>
            <th>Ngày lập</th>
            <th>Tổng tiền</th>
            <th>Thanh toán</th>
        </tr>
        <?php while ($row = $hoadons->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['ma_hoa_don']; ?></td>
            <td><?php echo $row['ma_dat_phong']; ?></td>
            <td><?php echo $row['ma_khach_hang']; ?></td>
            <td><?php echo $row['ngay_lap']; ?></td>
            <td><?php echo number_format($row['tong_tien']); ?> VNĐ</td>
            <td><?php echo $row['trang_thai_thanh_toan']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="index.php">⬅ Quay lại trang quản trị</a>
</body>
</html>
