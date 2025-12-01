<?php
session_start();
require 'classes/dbadmin.php';
$db = new dbadmin();

if (!isset($_SESSION['ma_khach_hang'])) {
    echo "<script>alert('Vui lòng đăng nhập để đặt phòng'); window.location='dangnhap.php';</script>";
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];

// Xác định action: 'chon' hay 'tatca'
$action = $_POST['action'] ?? '';

if ($action === 'chon') {
    if (!empty($_POST['chon_phong'])) {
        $chon_phong = $_POST['chon_phong'];
    } else {
        echo "<script>alert('Bạn chưa chọn phòng nào!'); window.location='giohang.php';</script>";
        exit();
    }
} elseif ($action === 'tatca') {
    // Lấy tất cả phòng trong giỏ hàng
    $rows = $db->layChiTietGioHangTheoKhach($ma_khach_hang);
    $chon_phong = [];
    if ($rows && $rows->num_rows > 0) {
        while ($r = $rows->fetch_assoc()) {
            $chon_phong[] = $r['ma_chi_tiet'];
        }
    }
    if (count($chon_phong) === 0) {
        echo "<script>alert('Giỏ hàng trống!'); window.location='giohang.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Hành động không hợp lệ'); window.location='giohang.php';</script>";
    exit();
}

// Lấy chi tiết phòng an toàn
$ds_phong = $db->layChiTietNhieuCTGH($chon_phong, $ma_khach_hang);
if (count($ds_phong) === 0) {
    echo "<script>alert('Không có phòng để đặt'); window.location='giohang.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Xác nhận đặt phòng</title>
<link rel="stylesheet" href="css/datphongchon.css">
<style>
.form-container { max-width: 700px; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; }
.selected-rooms { margin-bottom: 20px; }
.room-item { display: flex; align-items: center; margin-bottom: 10px; }
.room-item img { width: 100px; height: 70px; object-fit: cover; margin-right: 10px; border-radius: 6px; }
label { display:block; margin-top:10px; font-weight:bold; }
input, textarea { width:100%; padding:8px; margin-top:5px; border-radius:5px; border:1px solid #ccc; }
button { margin-top:15px; padding:10px 20px; border:none; border-radius:6px; background:#007bff; color:#fff; cursor:pointer; }
button:hover { background:#0056b3; }
</style>
</head>
<body>

<div class="form-container">
<h2>Xác nhận thông tin khách hàng</h2>

<div class="selected-rooms">
    <h3>Phòng sẽ đặt</h3>
    <?php foreach ($ds_phong as $ct): ?>
        <div class="room-item">
            <img src="uploads/<?= htmlspecialchars($ct['anh_phong'] ?? 'default.jpg') ?>" alt="">
            <div>
                <p><b><?= htmlspecialchars($ct['ten_phong']) ?></b></p>
                <p>Ngày đến: <?= htmlspecialchars($ct['ngay_den']) ?></p>
                <p>Ngày đi: <?= htmlspecialchars($ct['ngay_di']) ?></p>
                <p>Giá: <?= number_format($ct['don_gia']) ?> đ</p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<form method="POST" action="datphongdachon.php">
    <label>Họ tên:</label>
    <input type="text" name="ho_ten" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Số điện thoại:</label>
    <input type="text" name="so_dien_thoai" required>

    <label>Địa chỉ:</label>
    <textarea name="dia_chi" rows="3"></textarea>

    <!-- Hidden input các phòng -->
    <?php foreach ($chon_phong as $ma_ct): ?>
        <input type="hidden" name="chon_phong[]" value="<?= htmlspecialchars($ma_ct) ?>">
    <?php endforeach; ?>

    <!-- Hidden action để biết đặt "chon" hay "tatca" -->
    <input type="hidden" name="action" value="<?= htmlspecialchars($action) ?>">

    <button type="submit">Xác nhận đặt phòng</button>
</form>

</div>
</body>
</html>
