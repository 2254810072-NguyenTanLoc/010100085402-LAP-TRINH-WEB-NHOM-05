<?php
session_start();
require 'includes/config.php';
require 'classes/dbadmin.php';
$db = new dbadmin();

// Kiểm tra đăng nhập
if (!isset($_SESSION['ma_khach_hang'])) {
    echo "<script>alert('Vui lòng đăng nhập để cập nhật thông tin'); window.location='dangnhap.php';</script>";
    exit();
}

$ma_khach_hang = $_SESSION['ma_khach_hang'];

// Lấy thông tin khách hàng hiện tại
$kh = $db->getKhachHangTheoId($ma_khach_hang);
if (!$kh) {
    echo "<script>alert('Không tìm thấy thông tin khách hàng'); window.location='index.php';</script>";
    exit();
}

// Xử lý POST cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten = $_POST['ho_ten'] ?? '';
    $email = $_POST['email'] ?? '';
    $so_dien_thoai = $_POST['so_dien_thoai'] ?? '';
    $dia_chi = $_POST['dia_chi'] ?? '';

    if (!$ho_ten || !$email || !$so_dien_thoai) {
        $error = "Vui lòng điền đầy đủ thông tin bắt buộc";
    } else {
        $ok = $db->capNhatThongTinKhachHang($ma_khach_hang, $ho_ten, $email, $so_dien_thoai, $dia_chi);
        if ($ok) {
            echo "<script>alert('Cập nhật thành công'); window.location='index.php';</script>";
            exit();
        } else {
            $error = "Cập nhật thất bại, thử lại";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật thông tin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="auth-container">
        <div class="auth-box">
            <h2>Cập nhật thông tin khách hàng</h2>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="POST" action="">
                <input type="text" name="ho_ten" value="<?= ($kh['ho_ten']) ?>" placeholder="Họ tên" required>
                <input type="email" name="email" value="<?= ($kh['email']) ?>" placeholder="Email" required>
                <input type="text" name="so_dien_thoai" value="<?= ($kh['so_dien_thoai']) ?>" placeholder="Số điện thoại" required>
                <textarea type="text" name="dia_chi" value="<?= ($kh['dia_chi']) ?>" placeholder="Địa chỉ" cols="40" rows="3"></textarea>
                <button type="submit">Cập nhật</button>
            </form>
            <form method="GET" action="doimatkhau.php" style="margin-top:15px;">
    <button type="submit" style="background:#28a745;">Đổi mật khẩu</button>
</form>
        </div>
    </div>
</body>
</html>
